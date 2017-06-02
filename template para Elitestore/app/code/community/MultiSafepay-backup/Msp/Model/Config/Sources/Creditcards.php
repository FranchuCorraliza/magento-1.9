<?php

/**
 *
 * @category MultiSafepay
 * @package  MultiSafepay_Msp
 */
class MultiSafepay_Msp_Model_Config_Sources_Creditcards {

    /**
     * @return array
     */
    public function toOptionArray() {
        return array(
            array(
                "value" => "VISA",
                "label" => "Visa"
            ),
            array(
                "value" => "MASTERCARD",
                "label" => "Mastercard"
            ),
            array(
                "value" => "MAESTRO",
                "label" => "Maestro"
            ),
            array(
                "value" => "AMEX",
                "label" => "American Express"
            ),
        );
    }

}
