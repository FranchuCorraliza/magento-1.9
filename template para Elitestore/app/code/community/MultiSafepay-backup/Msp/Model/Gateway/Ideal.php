<?php

/**
 *
 * @category MultiSafepay
 * @package  MultiSafepay_Msp
 */
class MultiSafepay_Msp_Model_Gateway_Ideal extends MultiSafepay_Msp_Model_Gateway_Abstract {

    protected $_code = "msp_ideal";
    public $_model = "ideal";
    public $_gateway = "IDEAL";
    protected $_formBlockType = 'msp/idealIssuers';

    public function assignData($data) {
        parent::assignData($data);
        $session = Mage::getSingleton('checkout/session');
        $session->setData('payment_additional', $data);
        return $this;
    }

    public function getPayment($storeId = null) {
        $payment = parent::getPayment($storeId);
        $payment->setIssuer($this->_issuer);

        return $payment;
    }

    public function getIdealIssuers($storeId = null) {
        $idealissuers = parent::getIdealIssuersHTML($storeId);

        return $idealissuers;
    }

}
