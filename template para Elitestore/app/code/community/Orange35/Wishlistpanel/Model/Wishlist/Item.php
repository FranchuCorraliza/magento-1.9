<?php
class Orange35_Wishlistpanel_Model_Wishlist_Item extends Mage_Wishlist_Model_Item
{
	protected function getSizeId(){
		return $this->getBuyRequest()->getData()['super_attribute'][133];
	}
	protected function getProductModel(){
		return Mage::getModel("catalog/product")->load($this->getProductId());
	}
	public function getProductSize(){
		$product = $this->getProductModel();
		$buyRequest= $this->getBuyRequest();
		$sizeId=$this->getSizeId();
		$attr = $product->getResource()->getAttribute('talla');
		$productSize="";
		if ($attr->usesSource()) {
			$productSize = "<span class='label'>" . Mage::helper("orange35_wishlistpanel")->__("size: ") ."</span>" . $attr->getSource()->getOptionText($sizeId). " " . $product->getData("tallaje");
		}
		return $productSize;
	}
	
	public function getAddToCartUrl(){
		$sizeId=$this->getSizeId();
		$addToCartUrl=Mage::getUrl('checkout/cart/add', array('product'=>$this->getProductId(),'qty'=>1, 'form_key' => Mage::getSingleton('core/session')->getFormKey())). '?super_attribute[133]='.$sizeId;
		return $addToCartUrl;
	}
	
	public function isLastUnit(){
		$product = $this->getProductModel();
		$buyRequest= $this->getBuyRequest();
		$sizeId=$this->getSizeId();
		$childProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null,$product);
		foreach ($childProducts as $child){
			if (($child->getTalla()==$sizeId) && ($child->getStockItem()->getQty()==1)){
					return true;
			}
		}
		return false;
	}
	
	public function isSaleable(){
		$product = $this->getProductModel();
		$buyRequest= $this->getBuyRequest();
		$sizeId=$this->getSizeId();
		$childProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null,$product);
		foreach ($childProducts as $child){
			if (($child->getTalla()==$sizeId) && ($child->getStockItem()->getQty()>0)){
					return true;
			}
		}
		return false;
	}
}
		