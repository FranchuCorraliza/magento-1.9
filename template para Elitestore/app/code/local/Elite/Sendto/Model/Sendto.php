<?php 
class Elite_Sendto_Model_Sendto extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('sendto/sendto');
    }
}