<?php

class Magestore_Manufacturer_Model_Mysql4_Eaov extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('manufacturer/eaov', 'value_id');
    }	
}