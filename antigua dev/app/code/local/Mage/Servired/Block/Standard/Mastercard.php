<?php
class Mage_Servired_Block_Standard_Mastercard extends Mage_Core_Block_Template
{

    protected function _construct()
    {
        parent::_construct();
		if (Mage::app()->getStore()->getStoreId() == 1){
	        $this->setTemplate('servired/mastercard.phtml');
		}else{
	        $this->setTemplate('servired/mastercard_en.phtml');						
		}
    }
}