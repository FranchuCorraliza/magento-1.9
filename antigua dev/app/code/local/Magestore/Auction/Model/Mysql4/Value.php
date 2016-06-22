<?php

class Magestore_Auction_Model_Mysql4_Value extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('auction/value', 'value_id');
    }
}