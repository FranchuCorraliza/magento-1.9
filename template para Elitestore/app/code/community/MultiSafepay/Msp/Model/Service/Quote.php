<?php

/**
 *
 * @category MultiSafepay
 * @package  MultiSafepay_Msp
 */
class MultiSafepay_Msp_Model_Service_Quote extends Mage_Sales_Model_Service_Quote {

    public $availablePaymentMethodCodes = array(
        'msp',
        //'mspcheckout', 
        'msp_ideal',
        'msp_payafter',
        'msp_mistercash',
        'msp_visa',
        'msp_mastercard',
        'msp_banktransfer',
        'msp_maestro',
        'msp_paypal',
        'msp_webgift',
        'msp_ebon',
        'msp_babygiftcard',
        'msp_boekenbon',
        'msp_erotiekbon',
        'msp_parfumnl',
        'msp_parfumcadeaukaart',
        'msp_degrotespeelgoedwinkel',
        'msp_giropay',
        'msp_multisafepay',
        'msp_directebanking',
        'msp_directdebit',
        'msp_yourgift',
        'msp_wijncadeau',
        'msp_lief',
        'msp_amex',
        'msp_paypal',
        'msp_gezondheidsbon',
        'msp_fashioncheque',
    );

    /**
     * @return Mage_Sales_Model_Order
     */
    public function submitOrder() {
        $order = parent::submitOrder();

        if (Mage::app()->getStore()->isAdmin()) {
            return $order;
        }

        if (in_array($this->_quote->getPayment()->getMethod(), $this->availablePaymentMethodCodes)) {
            if (Mage::getStoreConfig('payment/msp/keep_cart', $this->_quote->getStoreId()) ||
                    Mage::getStoreConfig('msp/settings/keep_cart', $this->_quote->getStoreId()) ||
                    $this->_quote->getPayment()->getMethod() == 'msp_payafter' ||
                    $this->_quote->getPayment()->getMethod() == 'msp_klarna') {

                $this->_quote->setIsActive(true)->save();
                $this->_quote->setReservedOrderId(null)->save();
            }
        }

        return $order;
    }

}
