<?php

class Magestore_Auction_Model_Mysql4_Watcher_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('auction/watcher');
    }
}