<?php

class MultiSafepay_Msp_Block_Klarna extends Mage_Payment_Block_Form {

    public $_code;
    public $_issuer;
    public $_model;
    public $_countryArr = null;
    public $_country;

    protected function _construct() {
        $this->setTemplate('msp/klarna.phtml');

        parent::_construct();
    }

}
