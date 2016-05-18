<?php
/*
 * Developer: Rene Voorberg
* Team site: http://cmsideas.net/
* Support: http://support.cmsideas.net/
*
*
*/
class Glace_Ajaxcart_IndexController extends Mage_Checkout_Controller_Action 
{	   	
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
	
    public function getOnepage()
    {
        return Mage::getSingleton('checkout/type_onepage');
    }

	//initialize ajaxcart; add the product to the cart or open the product options popup
	public function initAction()	
	{
        $result = array();
        $params = $this->getRequest()->getParams();	
        $redirectUrl = $params['redirect_url'];

		if (!Mage::helper('ajaxcart')->isEnabled()) {
            Mage::getSingleton('checkout/session')->addError($this->__('The Ajax Cart extension is disabled.'));
			$result['redirect'] = $redirectUrl;
			
			$this->getResponse()->setHeader('Content-Type', 'text/plain');
        	$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result)); 
        	return;
        }
		
		$product = $this->_initProduct();
		if (!$product) {
			$result['redirect'] = $redirectUrl;
			
			$this->getResponse()->setHeader('Content-Type', 'text/plain');
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
			return;
		}

		if (!$params['skip_popup'] && ($this->_hasOptions($product) || $product->isGrouped())) {
			$result['popup'] = 'options';
			$result['form_action'] = Mage::helper('ajaxcart')->getInitUrl();
			$result['qty'] = $params['qty'];
			$result['product_id'] = $product->getId();
				
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
			//add product to cart	
			$cart = Mage::getSingleton('checkout/cart');
			try {
				if (isset($params['qty'])) {
					$filter = new Zend_Filter_LocalizedToNormalized(
						array('locale' => Mage::app()->getLocale()->getLocaleCode())
					);
					$params['qty'] = $filter->filter($params['qty']);
				}
			
				$related = $this->getRequest()->getParam('related_product');
			
			
				$cart->addProduct($product, $params);
				if (!empty($related)) {
					$cart->addProductsByIds(explode(',', $related));
				}
			
				$cart->save();
				
				Mage::dispatchEvent('checkout_cart_add_product_complete',
					array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
				);  
				
                $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->escapeHtml($product->getName()));
                Mage::getSingleton('checkout/session')->addSuccess($message);
			} catch (Mage_Core_Exception $e) {
				$result['error'] = -1;
				if (Mage::getSingleton('catalog/session')->getUseNotice(true)) {
					$result = array('message' => $e->getMessage());
				} else {
					$messages = array_unique(explode("\n", $e->getMessage()));                
					foreach ($messages as $message) {                	
						$result['message'][] = html_entity_decode($message);
					}
				}
				
				$this->getResponse()->setHeader('Content-Type', 'text/plain');
				$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
				return;
			} catch (Exception $e) {
				$result = array('error' => -1, 'message' => $this->__('Cannot add the item to shopping cart.'));
				Mage::logException($e);
				
				$this->getResponse()->setHeader('Content-Type', 'text/plain');
				$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
				return;
			}

        	Mage::getSingleton('checkout/session')->setCartWasUpdated(true);			
			
			$result['is_action'] = 'cart';
			$result['close_popup'] = 'options';
            
            $result['update_section']['html_layout_messages'] = $this->_getLayoutMessagesHtml();
	        $result['update_section']['html_cart'] = Mage::helper('ajaxcart')->getCartHtml($this);
	        $result['update_section']['html_cart_count'] = Mage::helper('ajaxcart')->getCartCountHtml($this);
            $result['update_section']['html_cart_link'] = Mage::helper('ajaxcart')->getCartLinkHtml($this);
			$result['update_section']['html_ajaxcart_js'] = Mage::getModel('ajaxcart/ajaxcart')->getAjaxCartQtysJs();

			//load cart page html only if on the shopping cart page; used when adding cross-sells to the cart or when adding products from the compare popup
			if(isset($params['is_cart_page']) && $params['is_cart_page']) {
           		$result['update_section']['html_cart_page'] = Mage::helper('ajaxcart')->getCartPageHtml($this);
           		$result['update_section']['html_ajaxcart_js'] = Mage::getModel('ajaxcart/ajaxcart')->getAjaxCartQtysJs();
           		
		   		if ($this->getRequest()->getParam('is_compare_popup', false)) {
					$result['popup'] = 'success';
		   		}
            } else {            
				$result['popup'] = 'success';
            }
		}
		//Mage::helper('core')->jsonEncode
		$this->getResponse()->setHeader('Content-Type', 'text/plain');
       	$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
	}	

    //initialize login popup
    public function loginAction()
    {		
        $redirectUrl = $this->getRequest()->getParam('redirect_url');
        $result = array();
        
        if (!Mage::helper('ajaxcart')->isEnabled()) {
            Mage::getSingleton('checkout/session')->addError($this->__('The Ajax Cart extension is disabled.'));
			$result['redirect'] = $redirectUrl;
			
			$this->getResponse()->setHeader('Content-Type', 'text/plain');
        	$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result)); 
        	return;
        }
        
    	if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $result['redirect'] = $redirectUrl;            
  
			$this->getResponse()->setHeader('Content-Type', 'text/plain');
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            return;
        }        
    
		$result['popup'] = 'ajaxcart-login';
		$result['update_section']['html_login'] = Mage::helper('ajaxcart')->getLoginHtml($this);
		
		$this->getResponse()->setHeader('Content-Type', 'text/plain');
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    } 	
	
    //Login post action
    public function loginPostAction()
    {	
        $redirectUrl = $this->getRequest()->getParam('redirect_url');
        $result = array();
        
        if (!Mage::helper('ajaxcart')->isEnabled()) {
            Mage::getSingleton('checkout/session')->addError($this->__('The Ajax Cart extension is disabled.'));
			$result['redirect'] = $redirectUrl;
			
			$this->getResponse()->setHeader('Content-Type', 'text/plain');
        	$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result)); 
        	return;
        }
        
    	if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $result['redirect'] = $redirectUrl;            
            
			$this->getResponse()->setHeader('Content-Type', 'text/plain');
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            return;
        }           

        if ($this->getRequest()->isPost()) {
            $login = $this->getRequest()->getPost('login');
            if (!empty($login['username']) && !empty($login['password'])) {
                try {
	                $session = Mage::getSingleton('customer/session');
                    $session->login($login['username'], $login['password']);
                    
					$customer = $session->getCustomer();
                    if ($session->getCustomer()->getIsJustConfirmed()) {
                    	$isJustConfirmed = true;
                        $customer->sendNewAccountEmail(
                        	$isJustConfirmed ? 'confirmed' : 'registered',
            				'',
            				Mage::app()->getStore()->getId()
            			);
                    }
		           	
		           	$result['update_section']['welcome'] = $this->__('Welcome, %s!', Mage::helper('core')->htmlEscape($session->getCustomer()->getName()));
		           	$result['update_section']['html_cart'] = Mage::helper('ajaxcart')->getCartHtml($this);
		           	$result['update_section']['html_cart_count'] = Mage::helper('ajaxcart')->getCartCountHtml($this);
		            $result['update_section']['html_cart_link'] = Mage::helper('ajaxcart')->getCartLinkHtml($this);
		
					//load cart page html only if on the shopping cart page;
					if($this->getRequest()->getParam('is_cart_page', false)) {
		           		$result['update_section']['html_cart_page'] = Mage::helper('ajaxcart')->getCartPageHtml($this);
		            }		
			        
			        $result['update_section']['html_compare'] = Mage::helper('ajaxcart')->getCompareHtml($this);
			        if ($this->getRequest()->getParam('is_compare_popup', false)) {
				        $result['update_section']['html_compare_popup'] = Mage::helper('ajaxcart')->getComparePopupHtml($this);
				    }
				    
		           	$result['update_section']['html_ajaxcart_js'] = Mage::getModel('ajaxcart/ajaxcart')->getAjaxCartQtysJs();
			        
			        //used by ajaxcart to add the div container for the wishlist sidebar in Observer.php
			        Mage::getSingleton('customer/session')->setIsFirstLogin(true);
                } catch (Mage_Core_Exception $e) {
                    switch ($e->getCode()) {
                        case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
                            $value = Mage::helper('customer')->getEmailConfirmationUrl($login['username']);
                            $message = Mage::helper('ajaxcart')->__('This account is not confirmed. Please confirm your email address before logging in.', $value);
                            break;
                        case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
                            $message = $e->getMessage();
                            break;
                        default:
                            $message = $e->getMessage();
                    }
                                        
                    $result = array('error' => -1, 'message' => html_entity_decode($message));
                    
					$this->getResponse()->setHeader('Content-Type', 'text/plain');
                    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                    return;
                } catch (Exception $e) {
                    // Mage::logException($e); // PA DSS violation: this exception log can disclose customer password
                }
            } else {
                $message = $this->__('Login and password are required.');                    
                $result = array('error' => -1, 'message' => $message);
                
				$this->getResponse()->setHeader('Content-Type', 'text/plain');
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                return;
            }
        }
		
		$this->getResponse()->setHeader('Content-Type', 'text/plain');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }	

    protected function _hasOptions($_product)
    {
        if ($_product->getTypeInstance(true)->hasOptions($_product)) {
            return true;
        }
        return false;
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