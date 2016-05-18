<?php
/*
 * Developer: Rene Voorberg
* Team site: http://cmsideas.net/
* Support: http://support.cmsideas.net/
*
*
*/
require_once Mage::getModuleDir('controllers', 'Mage_Checkout').DS.'CartController.php';
class Glace_Ajaxcart_CartController extends Mage_Checkout_CartController
{
    protected $_result = array();

	protected function _initProduct()    
	{
		$productId = $this->getRequest()->getParam('product',false); 
		if ($productId) {            
			$product = Mage::getModel('catalog/product')->setStoreId(Mage::app()->getStore()->getId())->load($productId);            
			if ($product->getId()) {
				Mage::unregister('current_product');
				Mage::register('current_product',$product);				
				Mage::unregister('product');
				Mage::register('product',$product);
				return $product;           
			}        
		}       
		return false;    
	}
	
    protected function _getSession()
    {
        return Mage::getSingleton('checkout/session');
    }
	
	protected function _redirect($path, $arguments = array())
	{
		return $this->_goBack();
	}
	
	protected function _redirectReferer($defaultUrl=null)
	{
		return $this->_goBack();
	}
	
	protected function _goBack()
    {
        $refererUrl = $this->_getRefererUrl(); 
		
		//add error messages to response and remove them from session; for this we had to use a protected $_result array
		$session = $this->_getSession();		
		$messages = $session->getMessages();
		foreach( $messages->getItems() as $message ){
			if ($message->getType() == 'error'){
				$this->_result['error'] = -1;
				$this->_result['message'][] = $message->getText();
				$message->setIdentifier('remove_error_messages');
			}
		}
		$messages->deleteMessageByIdentifier('remove_error_messages');
		
		if (!isset($this->_result['error'])) {
			if ($this->getRequest()->getParam('close_popup', false)) {
				$this->_result['close_popup'] = 'options';		
			}	
			
			$this->_result['update_section']['html_cart'] = Mage::helper('ajaxcart')->getCartHtml($this);
			$this->_result['update_section']['html_cart_count'] = Mage::helper('ajaxcart')->getCartCountHtml($this);
			$this->_result['update_section']['html_cart_link'] = Mage::helper('ajaxcart')->getCartLinkHtml($this);
			$this->_result['update_section']['html_wishlist_link'] = Mage::helper('ajaxcart')->getWishlistLinkHtml($this);
			
			//add notice messages to response
			$session = $this->_getSession();		
			$messages = $session->getMessages();
			foreach( $messages->getItems() as $message ){
				if ($message->getType() == 'notice'){
					$this->_result['notice'] = 1;
					$this->_result['notice_message'][] = $message->getText();
					$message->setIdentifier('remove_notice_messages');
				}
			}
			$messages->deleteMessageByIdentifier('remove_notice_messages');

			//load cart page html only if on the shopping cart page
			if(strpos($refererUrl, 'checkout/cart/') !== false || $this->getRequest()->getParam('is_cart_page', false)) {
				$this->_initLayoutMessages(array('checkout/session', 'catalog/session', 'customer/session'));
				$this->_result['update_section']['html_cart_page'] = Mage::helper('ajaxcart')->getCartPageHtml($this);
				$this->_result['update_section']['html_ajaxcart_js'] = Mage::getModel('ajaxcart/ajaxcart')->getAjaxCartQtysJs();
			} else {
			 	$session = $this->_getSession();		
				$messages = $session->getMessages();
				foreach( $messages->getItems() as $message ){
					if ($message->getType() == 'success'){
						$message->setIdentifier('remove_success_messages');
					}
				}
				$messages->deleteMessageByIdentifier('remove_success_messages');
			}
		}

		$this->getResponse()->setHeader('Content-Type', 'text/plain');
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($this->_result));
    }
    
    //used on shopping cart page to edit cart items in popup
	public function configureAction() {
		$result = array();			
    	$this->_getSession()->setRedirectUrl();
		
		// Extract item and product to configure
        $id = (int) $this->getRequest()->getParam('id');
        $item = null;
        $cart = $this->_getCart();
        if ($id) {
            $item = $cart->getQuote()->getItemById($id);
        }

        if (!$item) {
        	//ADD ERRORS
            $this->_getSession()->addError($this->__('Quote item is not found.'));
            $result['redirect'] = $this->getRequest()->getParam('redirect_url');

			$this->getResponse()->setHeader('Content-Type', 'text/plain');
        	$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result)); 
            return;
        }
		
		//open configuration popup
		$result['popup'] = 'options';				
	    $result['form_action'] = Mage::getUrl('checkout/cart/updateItemOptions', array('id'=>$id));
	    
	    $product = Mage::getModel('catalog/product')->load($item->getProductId());	
		$result['product_id'] = $product->getId();    
		
		Mage::helper('catalog/product')->prepareProductOptions($product, $item->getBuyRequest());
		
		Mage::register('product', $product);
		Mage::register('current_product', $product);
		
	    //$result['update_section']['html_options_layout_messages'] = $this->_getLayoutMessagesHtml();
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
				
		$this->getResponse()->setHeader('Content-Type', 'text/plain');
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
	}
	
    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }	
	
	public function clearCartAction()
	{
		$result = array();
		
        try {
			if (Mage::helper('ajaxcart')->getMagentoVersion()>1420) {
				$this->_getCart()->truncate()->save();
				$this->_getSession()->setCartWasUpdated(true);		
			} else {
				$this->_getSession()->clear();				
			}			
        } catch (Mage_Core_Exception $exception) {
            $this->_getSession()->addError($exception->getMessage());
        } catch (Exception $exception) {
            $this->_getSession()->addException($exception, $this->__('Cannot update shopping cart.'));
        }	
		
		$result['update_section']['html_cart'] = Mage::helper('ajaxcart')->getCartHtml($this);
		$result['update_section']['html_cart_count'] = Mage::helper('ajaxcart')->getCartCountHtml($this);
		$result['update_section']['html_cart_link'] = Mage::helper('ajaxcart')->getCartLinkHtml($this);
		
		//load cart page html only if on the shopping cart page
		if($this->getRequest()->getParam('is_cart_page', false)) {
			$result['update_section']['html_cart_page'] = Mage::helper('ajaxcart')->getCartPageHtml($this);
			$result['update_section']['html_ajaxcart_js'] = Mage::getModel('ajaxcart/ajaxcart')->getAjaxCartQtysJs();
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
			$this->_initLayoutMessages(array('checkout/session', 'catalog/session', 'customer/session'));
		}
        $update = $layout->getUpdate();
        $update->load('ajaxcart_index_messages');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }
	    
}