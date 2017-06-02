<?php
class Elite_WishlistReminder_Model_Cron{	
	
	
	public function sendWishlistReminder(){
		$wishlistCollection=Mage::getModel("wishlist/wishlist")->getCollection();
		foreach ($wishlistCollection as $item){
			
			//Enviamos recordatorio a las 24h de la última actualización de una wishlist y a la semana de la última actualización
			$updateAt=$item->getUpdatedAt();
			$diferencia=time()-strtotime($updateAt);
			if(((60*60*24) < $diferencia && (60*60*48) > $diferencia) || ((60*60*24*7) < $diferencia && (60*60*24*8) > $diferencia)){ 
				$this->_sendReminder($item);
			}
			$wishlist=Mage::getModel("wishlist/wishlist")->load($item->getWishlistId());
			$wishlistItemCollection=$wishlist->getItemCollection();
			foreach($wishlistItemCollection as $product){
				$simpleProductId=$product->getBuyRequest()->getSimpleProductId();
				$notificadoRS=$product->getBuyRequest()->getData("notificadoRS");
				$notificadoLS=$product->getBuyRequest()->getData("notificadoSL");
				$availableQty=$product->getBuyRequest()->getAvailableQty();
				if ($simpleProductId!=null && $simpleProductId!=''){
					$simpleProduct=Mage::getModel('catalog/product')->load($simpleProductId);
					$actualQty=Mage::getModel('cataloginventory/stock_item')->loadByProduct($simpleProduct)->getQty();
					if ($availableQty==0 && $actualQty>=1 && !$notificadoRS){
						$this->_sendRestockNofication($item,$product);
						//Marcamos el item para no volver a enviar la notificación
						
						$buyRequest=$product->getBuyRequest();
						$data=$buyRequest->getData();
						$data['notificadoRS']=1;
						$data=serialize($data);
						$options=$product->getOptions();
						foreach ($options as $index => $option){
							if ($option->getCode()=="info_buyRequest"){
								$option->setValue($data);
								$option->save();
							}
						}
						
					}elseif ($availableQty>1 && $actualQty==1 && !$notificadoLS){
						$this->_sendLastUnitNofication($item,$product);
						//Marcamos el item para no volver a enviar la notificación
						$buyRequest=$product->getBuyRequest();
						$data=$buyRequest->getData();
						$data['notificadoSL']=1;
						$data=serialize($data);
						$options=$product->getOptions();
						foreach ($options as $index => $option){
							if ($option->getCode()=="info_buyRequest"){
								$option->setValue($data);
								$option->save();
							}
						}
					}elseif ($availableQty>0 && $actualQty==0){
						//Actualizamos la cantidad disponible a 0 para esperar a que se vuelva a poner disponible
						$buyRequest=$product->getBuyRequest();
						$data=$buyRequest->getData();
						$data['available_qty']=0;
						$data=serialize($data);
						$options=$product->getOptions();
						foreach ($options as $index => $option){
							if ($option->getCode()=="info_buyRequest"){
								$option->setValue($data);
								$option->save();
							}
						}
					}					
				}
				
			}
		}
		return true;
	} 
	
	protected function _sendReminder($item){
		$customer=Mage::getModel("customer/customer")->load($item->getCustomerId());
		$wishlist=Mage::getModel("wishlist/wishlist")->load($item->getWishlistId());
		$wishlistItemCollection=$wishlist->getItemCollection();
		$data['customerName']=$customer->getFirstname();
		$data['customerEmail']=$customer->getEmail();
		$data['wishlist']=array();
		foreach($wishlistItemCollection as $item){
			$product=Mage::getModel('catalog/product')->load($item->getProductId());
			$productPrice=	(string) Mage::helper('core')->currency($product->getFinalPrice(), true, false);
			$attr = $product->getResource()->getAttribute('talla');
			if ($attr->usesSource()) {
				$productSize = Mage::helper("orange35_wishlistpanel")->__("Size: ") . $attr->getSource()->getOptionText($item->getBuyRequest()['super_attribute'][133]). " " . $product->getData("tallaje");
			}
			$productLink=	$product->getProductUrl();
			$productImage=	(string) Mage::helper('catalog/image')->init($product, 'thumbnail')->resize(200);
			$productName=	strtolower($product->getName());
			$productBrand=	strtoupper($product->getAttributeText('manufacturer'));
			$data['wishlist'][]=
				array(
					'productPrice'=>$productPrice,
					'productSize'=>$productSize,
					'productLink'=>$productLink,
					'productImage'=>$productImage,
					'productName'=>$productName,
					'productBrand'=>$productBrand
				);
				
		}
		if($data) {
			try {
				$model = Mage::getModel('orange35_wishlistpanel/sharewishlist');
				$data['store_id'] = Mage::app()->getStore()->getId();
				$data['status']	  = 1;
				$data['created_time']	  = now();
				$data['updated_time']	  = now();
				$model->sendReminder($data);
			} catch (Exception $e) {
				Mage::log("Error al enviar recordatorio: $e",null,"wishlist-reminder.log");
				Mage::log($data,null,"wishlist-reminder.log");
				return;
			}
		}
	}
	
	protected function _sendLastUnitNofication($item,$product){
		$customer=Mage::getModel("customer/customer")->load($item->getCustomerId());
		$wishlist=Mage::getModel("wishlist/wishlist")->load($item->getWishlistId());
		$data['customerName']=$customer->getFirstname();
		$data['customerEmail']=$customer->getEmail();
		$product=Mage::getModel('catalog/product')->load($product->getProductId());
		$productPrice=	(string) Mage::helper('core')->currency($product->getFinalPrice(), true, false);
		$attr = $product->getResource()->getAttribute('talla');
		if ($attr->usesSource()) {
			$productSize = Mage::helper("orange35_wishlistpanel")->__("Size: ") . $attr->getSource()->getOptionText($item->getBuyRequest()['super_attribute'][133]). " " . $product->getData("tallaje");
		}
		$productLink=	$product->getProductUrl();
		$productImage=	(string) Mage::helper('catalog/image')->init($product, 'thumbnail')->resize(200);
		$productName=	strtolower($product->getName());
		$productBrand=	strtoupper($product->getAttributeText('manufacturer'));
		$data['productPrice']=$productPrice;
		$data['productSize']=$productSize;
		$data['productLink']=$productLink;
		$data['productImage']=$productImage;
		$data['productName']=$productName;
		$data['productBrand']=$productBrand;
		if($data) {
			try {
				$model = Mage::getModel('orange35_wishlistpanel/sharewishlist');
				$data['store_id'] = Mage::app()->getStore()->getId();
				$data['status']	  = 1;
				$data['created_time']	  = now();
				$data['updated_time']	  = now();
				$model->sendLastUnitNotification($data);
			} catch (Exception $e) {
				Mage::log("Error al enviar recordatorio: $e",null,"wishlist-reminder.log");
				Mage::log($data,null,"wishlist-reminder.log");
				return;
			}
		}
		
	}
	
	protected function _sendRestockNofication($item,$product){
		$customer=Mage::getModel("customer/customer")->load($item->getCustomerId());
		$wishlist=Mage::getModel("wishlist/wishlist")->load($item->getWishlistId());
		$data['customerName']=$customer->getFirstname();
		$data['customerEmail']=$customer->getEmail();
		$product=Mage::getModel('catalog/product')->load($product->getProductId());
		$productPrice=	(string) Mage::helper('core')->currency($product->getFinalPrice(), true, false);
		$attr = $product->getResource()->getAttribute('talla');
		if ($attr->usesSource()) {
			$productSize = Mage::helper("orange35_wishlistpanel")->__("Size: ") . $attr->getSource()->getOptionText($item->getBuyRequest()['super_attribute'][133]). " " . $product->getData("tallaje");
		}
		$productLink=	$product->getProductUrl();
		$productImage=	(string) Mage::helper('catalog/image')->init($product, 'thumbnail')->resize(200);
		$productName=	strtolower($product->getName());
		$productBrand=	strtoupper($product->getAttributeText('manufacturer'));
		$data['productPrice']=$productPrice;
		$data['productSize']=$productSize;
		$data['productLink']=$productLink;
		$data['productImage']=$productImage;
		$data['productName']=$productName;
		$data['productBrand']=$productBrand;
		if($data) {
			try {
				$model = Mage::getModel('orange35_wishlistpanel/sharewishlist');
				$data['store_id'] = Mage::app()->getStore()->getId();
				$data['status']	  = 1;
				$data['created_time']	  = now();
				$data['updated_time']	  = now();
				$model->sendRestockNotification($data);
			} catch (Exception $e) {
				Mage::log("Error al enviar recordatorio: $e",null,"wishlist-reminder.log");
				Mage::log($data,null,"wishlist-reminder.log");
				return;
			}
		}
	}
}