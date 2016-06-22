<?php
class Customex_Orderrequest_Block_Orderrequest extends Mage_Directory_Block_Data
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getOrderrequest()     
     { 
        if (!$this->hasData('orderrequest')) {
            $this->setData('orderrequest', Mage::registry('orderrequest'));
        }
        return $this->getData('orderrequest');
        
    }
	
	public function getCustomer() {
		return Mage::getSingleton('customer/session')->getCustomer();
	}
	
	public function getTemplate()
	{
		return parent::getTemplate();
	}	
	
}