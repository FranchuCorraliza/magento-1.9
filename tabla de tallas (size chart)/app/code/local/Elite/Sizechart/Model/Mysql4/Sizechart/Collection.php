<?php
class Elite_Sizechart_Model_Mysql4_Sizechart_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        //parent::__construct();
        $this->_init('sizechart/sizechart');
    }
}