<?php

/**
 *
 * @category MultiSafepay
 * @package  MultiSafepay_Msp
 */
class MultiSafepay_Msp_Model_Gateway_Banktransfer extends MultiSafepay_Msp_Model_Gateway_Abstract {

    protected $_code = "msp_banktransfer";
    public $_model = "banktransfer";
    public $_gateway = "BANKTRANS";
    public $string = '';
}
