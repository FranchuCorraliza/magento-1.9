<?php

/**
 *
 * @category MultiSafepay
 * @package  MultiSafepay_Msp
 */
class MultiSafepay_Msp_Model_Gateway_Creditcard extends MultiSafepay_Msp_Model_Gateway_Abstract {

    protected $_code = "msp_creditcard";
    public $_model = "creditcard";
    public $_gateway = "CREDITCARD";
    protected $_formBlockType = 'msp/creditcards';

   
   public function assignData($data) {
        parent::assignData($data);
        $session = Mage::getSingleton('checkout/session');
        $session->setData('payment_additional', $data);
        

        return $this;
    }
    
    public function getCreditcards($storeId = null) {
        $cards = Mage::getStoreConfig("msp_gateways/msp_creditcard/card_select");
        return $cards;
    }
    
    
    public function getCreditcardLabels($storeId = null) {
         $labels = array("VISA"=>"Visa", "MASTERCARD"=>"Mastercard", "MAESTRO"=>"Maestro","AMEX"=>"American Express");
        return $labels;
    }
    
   
}
