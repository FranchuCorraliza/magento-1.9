<?php

class Echidna_Wordpress_Model_Wordpress extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('wordpress/wordpress');
    }
}