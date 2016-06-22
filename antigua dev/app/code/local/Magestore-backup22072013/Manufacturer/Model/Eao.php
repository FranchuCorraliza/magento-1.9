<?php
class Magestore_Manufacturer_Model_Eao extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('manufacturer/eao');
    }	
}
?>