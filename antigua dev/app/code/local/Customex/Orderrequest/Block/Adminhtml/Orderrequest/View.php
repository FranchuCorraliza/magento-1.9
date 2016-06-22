<?php

class Customex_Orderrequest_Block_Adminhtml_Orderrequest_View extends Mage_Adminhtml_Block_Template
{
    public function __construct()
	{
		parent::_construct();
		$this->setTemplate('orderrequest/view.phtml');
		return $this;
	}
	
	public function getOrderrequest() {
		$id = $this->getRequest()->getParam('id');
		
		$store_id = $this->getRequest()->getParam('store', 0);
		
		$collection = Mage::getModel('orderrequest/orderrequest')
			->load($id)
			;
			
		return $collection;
	}
}