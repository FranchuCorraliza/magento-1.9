<?php 
class Elite_Contactus_Model_Contactus extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('elite_contactus/contactus');
    }
}