<?php
class Elite_OrderByRequest_IndexController extends Mage_Core_Controller_Front_Action
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
	
	public function submitAction(){
		$data = $this->getRequest()->getPost();
		$error = false;
		if($data) {
			$product = Mage::getModel('catalog/product')->load($data['product_id']);
			try {
				$model = Mage::getModel('orderbyrequest/productcontact');
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
				return;
			} catch (Exception $e) {
				Mage::getSingleton('catalog/session')->addError($this->__("Your contact for product can't sent. Please try again"));
                $this->_redirectUrl($product->getProductUrl());
				return;
			}
		}
		
	}
}