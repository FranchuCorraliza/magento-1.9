

<?php

/**
 *
 * @category MultiSafepay
 * @package  MultiSafepay_Msp
 */
class MultiSafepay_Msp_Block_Creditcards extends Mage_Payment_Block_Form {

    /**
     * Construct
     */
    protected function _construct() {
       
        parent::_construct();
        $this->setTemplate('msp/creditcards.phtml');

    }

    /**
     * @return array
     */
    public function getCreditcards() {
        /** @var $msp MultiSafepay_Msp_Model_Gateway_Ideal */
        $msp = Mage::getSingleton("msp/gateway_creditcard");
        $base = $msp->getCreditcards();

        return $base;
    }
    /**
     * @return array
     */
    public function getCreditcardLabels() {
        /** @var $msp MultiSafepay_Msp_Model_Gateway_Ideal */
        $msp = Mage::getSingleton("msp/gateway_creditcard");
        $base = $msp->getCreditcardLabels();

        return $base;
    }
    
    
    

}
