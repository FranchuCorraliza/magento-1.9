<?php

/**
 *
 * @category MultiSafepay
 * @package  MultiSafepay_Msp
 */
class MultiSafepay_Msp_Model_Observer_Abstract extends MultiSafepay_Msp_Model_Abstract {

    protected $_order;
    protected $_bilingInfo;

    protected function _construct() {
        $this->_loadLastOrder();
        $this->_setOrderBillingInfo();
    }

    /**
     * Each payment method has it's own observer. When one of thos observers is called, this checks if it's
     * payment method is being used and therefore, if this observer needs to do anything.
     * 
     * @param unknown_type $observer
     */
    protected function _isChosenMethod($observer) {
        return (bool) $observer->getOrder()->getPayment()->getMethod() === $this->_code;
    }

    /**
     * @return array
     */
    protected function _getAllActivePaymentMethods($storeId) {
        $code = array();
        $payments = Mage::getSingleton('payment/config')->getActiveMethods($storeId);

        foreach ($payments as $payment) {
            $code[] = $payment->getCode();
        }

        return $code;
    }

}
