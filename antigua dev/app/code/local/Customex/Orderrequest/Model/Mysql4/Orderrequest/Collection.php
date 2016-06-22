<?php

class Customex_Orderrequest_Model_Mysql4_Orderrequest_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('orderrequest/orderrequest');
    }
}