<?php
class Magestore_Productcontact_Block_Productcontact extends Mage_Directory_Block_Data
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getProductcontact()     
     { 
        if (!$this->hasData('productcontact')) {
            $this->setData('productcontact', Mage::registry('productcontact'));
        }
        return $this->getData('productcontact');
        
    }
	
	public function getCustomer() {
		return Mage::getSingleton('customer/session')->getCustomer();
	}
	
	public function getTemplate()
	{
		if(!Mage::helper('magenotification')->checkLicenseKey('Productcontact')){
			return null;
		}
		return parent::getTemplate();
	}	
	
}