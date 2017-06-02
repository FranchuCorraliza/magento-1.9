<?php

class Elite_ProductsDetails_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
		$this->loadLayout();
		$this->renderLayout();
    }
	
	public function formAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function shareAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function submitAction(){
		
		$data = $this->getRequest()->getPost();
		$error = false;
		if($data) {
			$product = Mage::getModel('catalog/product')->load($data['product_id']);
			try {
				if ($data['g-recaptcha-response']){
					$model = Mage::getModel('productsdetails/productcontact');
					$data['store_id'] = Mage::app()->getStore()->getId();
					$data['status']	  = 1;
					$data['created_time']	  = now();
					$data['updated_time']	  = now();
					$data['product_name']     = strtolower($product->getName());
					$data['image_product']	  = Mage::helper('catalog/image')->init($product, 'thumbnail')->resize(300);
					$data['manufacturer']	  = strtoupper($product->getAttributeText('manufacturer'));
					$data['product_link']	  = $product->getProductUrl();
					$data['product_ref']	  = $product->getSku();
					$model->sendMailToCustomer($data)
						->sendMailToAdmin($data);
					Mage::getSingleton('catalog/session')->addSuccess($this->__("Thanks for your contact. We will respond you as soon as possible."));
					$this->_redirectUrl($product->getProductUrl());
				}else{
					Mage::getSingleton('catalog/session')->addError($this->__("Your contact for product can't sent. Please try again"));
					$this->_redirectReferer();
				}
				
				return;
			} catch (Exception $e) {
				Mage::getSingleton('catalog/session')->addError($this->__("Your contact for product can't sent. Please try again"));
                $this->_redirectUrl($product->getProductUrl());
				return;
			}
		}
		
	}
	
	public function sharesubmitAction(){
		$data = $this->getRequest()->getPost();
		$error = false;
			if($data) {
				$product = Mage::getModel('catalog/product')->load($data['product_id']);
				if(Mage::getSingleton('customer/session')->isLoggedIn()){
					try {
						if ($data['g-recaptcha-response']){
							$model = Mage::getModel('productsdetails/shareviaemail');
							$data['store_id'] = Mage::app()->getStore()->getId();
							$data['status']	  = 1;
							$data['created_time']	  = now();
							$data['updated_time']	  = now();
							$data['product_name']     = strtolower($product->getName());
							$data['image_product']	  = Mage::helper('catalog/image')->init($product, 'thumbnail')->resize(300);
							$data['manufacturer']	  = strtoupper($product->getAttributeText('manufacturer'));
							$data['product_link']	  = $product->getProductUrl(); 
							$data['product_price']	  = Mage::helper('core')->currency($product->getFinalPrice(), true, false);
							$model->sendmail($data);
							Mage::log("Compartido",null,"compartiendo.log");
							Mage::getSingleton('catalog/session')->addSuccess($this->__("Thanks for sharing this item."));
							$this->_redirectUrl($product->getProductUrl());
						}else{
							Mage::getSingleton('catalog/session')->addError($this->__("Your contact for product can't sent. Please try again"));
							$this->_redirectReferer();
						}
						return;
					} catch (Exception $e) {
						Mage::getSingleton('catalog/session')->addError($this->__("Your email can't sent. Please try again"));
						$this->_redirectUrl($product->getProductUrl());
						return;
					}
				}else{
					Mage::getSingleton('catalog/session')->addError($this->__("You must be logged to share it"));
					$this->_redirectUrl($product->getProductUrl());
					return;
				}
			}
		
	}
	
}