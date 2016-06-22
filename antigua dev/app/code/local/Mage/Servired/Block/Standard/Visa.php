<?php
class Mage_Servired_Block_Standard_Visa extends Mage_Core_Block_Template
{

    protected function _construct()
    {
        parent::_construct();
        if (Mage::app()->getStore()->getStoreId() == 1){
			$this->setTemplate('servired/visa.phtml');
		}else{			
			$this->setTemplate('servired/visa_en.phtml');
		}
    }
}