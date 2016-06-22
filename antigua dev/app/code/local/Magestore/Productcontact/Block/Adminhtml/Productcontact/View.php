<?php

class Magestore_Productcontact_Block_Adminhtml_Productcontact_View extends Mage_Adminhtml_Block_Template
{
    public function __construct()
	{
		parent::_construct();
		$this->setTemplate('productcontact/view.phtml');
		return $this;
	}
	
	public function getProductContact() {
		$id = $this->getRequest()->getParam('id');
		
		$store_id = $this->getRequest()->getParam('store', 0);
		
		$collection = Mage::getModel('productcontact/productcontact')
			->load($id)
			;
			
		return $collection;
	}
}