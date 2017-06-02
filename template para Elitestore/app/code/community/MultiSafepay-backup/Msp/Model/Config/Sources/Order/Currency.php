<?php

/**
 *
 * @category MultiSafepay
 * @package  MultiSafepay_Msp
 */
class MultiSafepay_Msp_Model_Config_Sources_Order_Currency {

    /**
     * @return array
     */
    public function toOptionArray() {
        $currencies = array();
        //$storeId = Mage::app()->getRequest()->getParam('store', 0);
        //$codes = Mage::app()->getStore($storeId)->getAvailableCurrencyCodes(true);
        //$codes = Mage::app()->getStore()->getAvailableCurrencyCodes(true);

        $currencyModel = Mage::getModel('directory/currency');
        $currencies = $currencyModel->getConfigAllowCurrencies();

        foreach ($currencies as $code) {
            $currencies2[] = array(
                "value" => $code,
                "label" => Mage::helper("msp")->__($code),
            );
        }
        return $currencies2;
    }

}
