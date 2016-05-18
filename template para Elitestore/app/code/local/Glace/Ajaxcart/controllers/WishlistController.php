<?php
/*
 * Developer: Rene Voorberg
* Team site: http://cmsideas.net/
* Support: http://support.cmsideas.net/
*
*
*/
require_once Mage::getModuleDir('controllers', 'Mage_Wishlist').DS.'IndexController.php';
class Glace_Ajaxcart_WishlistController extends Mage_Wishlist_IndexController
{	
	//used in addAction(), removeAction() and fromcartAction() functions
	public function norouteAction() {
		return $this->_refreshWishlistBlocks();
	}
	
	protected function _validateFormKey() {
		return true;
	}

	//used in addAction(), cartAction(), updateAction() and updateItemOptionsAction() functions
	protected function _redirect($_url)
	{
		$calledFrom = Mage::helper('ajaxcart')->getCallingFunction();

		//if the _redirect() function was called from cartAction() it means it had an error, in which case the wishlist blocks will be refreshed
		if ($calledFrom == 'cartAction' || $calledFrom == 'updateAction') {
			return $this->_refreshWishlistBlocks();
		}
			
	    $result = array();
	    
		//update layout messages if not updateItemOptionsAction()
		if ($calledFrom!='updateItemOptionsAction') {			
			$productId = (int) $this->getRequest()->getParam('product', false);
			$product = Mage::getModel('catalog/product')->load($productId);
			
			if (!$productId || !$product->getId() || !$product->isVisibleInCatalog()) {
				$result['error'] = -1;
				$result['message'] = $this->__('Cannot specify product.');
	        } else {
	        	//delete original wishlist success message
	        	$session = Mage::getSingleton('customer/session');		
				$messages = $session->getMessages();
				foreach( $messages->getItems() as $message ){
					if ($message->getType() == 'success'){
						$message->setIdentifier('remove_success_messages');
					}
				}
				$messages->deleteMessageByIdentifier('remove_success_messages');
	        
	        	//add ajaxcart wishlist success message
				$message = $this->__('%s has been added to your wishlist.', $product->getName());
		        $session->addSuccess($message);
				$result['popup'] = 'success';
				$result['is_action'] = 'wishlist'; 
		    }		
	    } else {
			$result['close_popup'] = 'options';	
		}
	        
	    $result['update_section']['html_layout_messages'] = $this->_getLayoutMessagesHtml();	
	        
	    //load wishlist page html only if on the wishlist page
		if($this->getRequest()->getParam('is_wishlist_page', false)) {
		    $result['update_section']['html_wishlist_page'] = Mage::helper('ajaxcart')->getWishlistPageHtml($this);
		} else {
		    $result['update_section']['html_wishlist'] = Mage::helper('ajaxcart')->getWishlistHtml($this);
		}
        $result['update_section']['html_wishlist_link'] = Mage::helper('ajaxcart')->getWishlistLinkHtml($this);     
		
		$this->getResponse()->setHeader('Content-Type', 'text/plain');
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
	}	
	
	public function configureAction() {
		$this->_redirectUrl('/configure/');
	}

	//used in cartAction(), allcartAction() and fromcartAction() functions
	protected function _redirectUrl($_url)
	{
		$calledFrom = Mage::helper('ajaxcart')->getCallingFunction();
		if(strpos($_url, '/configure/') !== false) {
			if ($calledFrom=='fromcartAction' || $calledFrom=='configureAction') {
		        $itemId = (int) $this->getRequest()->getParam('id');
			} else {
		        $itemId = (int) $this->getRequest()->getParam('item');
		    }
	        $item = Mage::getModel('wishlist/item')->loadWithOptions($itemId);
	        $product = Mage::getModel('catalog/product')->load($item->getProductId());
	        
			$result = array();			
			
			if (!$this->getRequest()->getParam('skip_popup', false)) {
				//open configuration popup
				$result['popup'] = 'options';				
				if ($calledFrom=='configureAction') {
					$result['form_action'] = Mage::helper('wishlist')->getUpdateUrl($item).'product/'.$product->getId().'/';				
				} else {
					$result['form_action'] = Mage::helper('wishlist')->getAddToCartUrl($item);
				}
				
				//set qty
				if ($qty = $this->getRequest()->getParam('qty', false)) {
			        if (is_array($qty)) {
			            if (isset($qty[$itemId])) {
			                $qty = $qty[$itemId];
			            } else {
			                $qty = 1;
			            }
			        }
			        $qty = $this->_processLocalizedQty($qty);
			        if ($qty) {
			            $item->setQty($qty);
			        }
				} 		
				
				$buyRequest = $item->getBuyRequest();
	            if (!$buyRequest->getQty() && $item->getQty()) {
	                $buyRequest->setQty($item->getQty());
	            }
				Mage::helper('catalog/product')->prepareProductOptions($product, $buyRequest);
				
				$result['product_id'] = $product->getId();
				Mage::register('product', $product);
				Mage::register('current_product', $product);
				
	        	$result['update_section']['html_options_layout_messages'] = $this->_getLayoutMessagesHtml();
				if (!$product->isGrouped()){
					if ($product->getTypeId()=='configurable'){
						Mage::getSingleton('customer/session')->setAjaxCartAction('options_popup_conf');
						$result['update_section']['html_options'] = Mage::helper('ajaxcart')->getConfigurableHtml($this);
						Mage::getSingleton('customer/session')->setAjaxCartAction();
					} else if ($product->getTypeId()=='bundle'){
						$result['update_section']['html_options'] = Mage::helper('ajaxcart')->getBundleHtml($this);
					} else if ($product->getTypeId()=='downloadable'){
						$result['update_section']['html_options'] = Mage::helper('ajaxcart')->getDownloadableHtml($this);
					} else {
						$result['update_section']['html_options'] = Mage::helper('ajaxcart')->getOptionsHtml($this);				
					}
				} else {
					$result['update_section']['html_options'] = Mage::helper('ajaxcart')->getGroupedHtml($this);
				}
	        } else {
				//print error if configure popup is already opened and the configure action was re-called 
				$result['error'] = -1;
				//get catalog error messages			
				$session = Mage::getSingleton('catalog/session');			
				$messages = $session->getMessages();
				foreach( $messages->getItems() as $message ){
					if ($message && $message->getType() != 'success'){
				    	$result['message'][] = $message->getText();
					}
				}
				
				//get wishlist error messages	
				$session = Mage::getSingleton('wishlist/session');			
				$messages = $session->getMessages();
				foreach( $messages->getItems() as $message ){
					if ($message && $message->getType() != 'success'){
				    	$result['message'][] = $message->getText();
					}
				}
				
				//load error messages in background to not show up again the second time
				Mage::getSingleton('catalog/session')->getMessages(true);
				Mage::getSingleton('wishlist/session')->getMessages(true);				
			}
		} else {				
	        $result = array();
	        if ($calledFrom!='fromcartAction') {
				$result['popup'] = 'success';
				$result['is_action'] = 'cart'; 
				$result['close_popup'] = 'options';				
						    
				//load wishlist page html only if on the wishlist page
				if($this->getRequest()->getParam('is_wishlist_page', false)) {
			        $result['update_section']['html_wishlist_page'] = Mage::helper('ajaxcart')->getWishlistPageHtml($this);
			    }
				
				//add success message if item was moved to the cart from the wishlist; if it still exists an error will be displayed (i.e. product is out of stock)
		        $itemId = (int) $this->getRequest()->getParam('item');
		        $item = Mage::getModel('wishlist/item')->load($itemId);
		        if ($calledFrom!='allcartAction' && !$item->getId()) {	 
					$session = Mage::getSingleton('checkout/session');		
					$productId = Mage::getSingleton('checkout/session')->getLastAddedProductId(true);
					$product = Mage::getModel('catalog/product')->load($productId);	
				
					$message = $this->__('%s was added to your shopping cart.', $product->getName());
					$session->addSuccess($message);
				}    			
		        $result['update_section']['html_layout_messages'] = $this->_getLayoutMessagesHtml();
			}	 
			    
			$result['update_section']['html_wishlist_link'] = Mage::helper('ajaxcart')->getWishlistLinkHtml($this); 
			
			//load wishlist sidebar html only if not on wishlist page
			if(!$this->getRequest()->getParam('is_wishlist_page', false)) {
			    $result['update_section']['html_wishlist'] = Mage::helper('ajaxcart')->getWishlistHtml($this);
			}	
			
			$result['update_section']['html_cart'] = Mage::helper('ajaxcart')->getCartHtml($this);
			$result['update_section']['html_cart_count'] = Mage::helper('ajaxcart')->getCartCountHtml($this);
			$result['update_section']['html_cart_link'] = Mage::helper('ajaxcart')->getCartLinkHtml($this);
			
			//load cart page html only if on the shopping cart page; 
			if($this->getRequest()->getParam('is_cart_page', false)) {
				$this->_initLayoutMessages(array('checkout/session', 'wishlist/session', 'catalog/session', 'customer/session'));
			    $result['update_section']['html_cart_page'] = Mage::helper('ajaxcart')->getCartPageHtml($this);
			    $result['update_section']['html_ajaxcart_js'] = Mage::getModel('ajaxcart/ajaxcart')->getAjaxCartQtysJs();
			} else if($this->getRequest()->getParam('is_wishlist_page', false)) {
			    $result['update_section']['html_ajaxcart_js'] = Mage::getModel('ajaxcart/ajaxcart')->getAjaxCartQtysJs();
			}
		}
		
		$this->getResponse()->setHeader('Content-Type', 'text/plain');
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
	}	
	
	//used in removeAction() function
	protected function _redirectReferer($_url)
	{
		return $this->_refreshWishlistBlocks();
	}
	
	protected function _refreshWishlistBlocks() {
		Mage::getSingleton('customer/session')->getMessages(true);
		
        $result = array();
        
        $result['update_section']['html_wishlist_link'] = Mage::helper('ajaxcart')->getWishlistLinkHtml($this);   
		    
		//load wishlist page html only if on the wishlist page; else load wishlist sidebar
		if($this->getRequest()->getParam('is_wishlist_page', false)) {
		    $result['update_section']['html_wishlist_page'] = Mage::helper('ajaxcart')->getWishlistPageHtml($this);
			$result['update_section']['html_ajaxcart_js'] = Mage::getModel('ajaxcart/ajaxcart')->getAjaxCartQtysJs();
		} else {
		    $result['update_section']['html_wishlist'] = Mage::helper('ajaxcart')->getWishlistHtml($this);			
		}  
		
		$this->getResponse()->setHeader('Content-Type', 'text/plain');
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
	}
	
    protected function _getLayoutMessagesHtml()
    {        
	    Mage::app()->getCacheInstance()->cleanType('layout');
		/* Mage::getModel('ajaxcart/ajaxcart')->loadQuoteMessages(); */
        $layout = $this->getLayout();   
        if (Mage::helper('ajaxcart')->getMagentoVersion()>1411) {     
			$this->_initLayoutMessages(array('checkout/session', 'wishlist/session', 'catalog/session', 'customer/session'));
		}
        $update = $layout->getUpdate();
        $update->load('ajaxcart_index_messages');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }
    
}