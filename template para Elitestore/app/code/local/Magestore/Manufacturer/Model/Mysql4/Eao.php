<?php

class Magestore_Manufacturer_Model_Mysql4_Eao extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('manufacturer/eao', 'option_id');
    }	
}