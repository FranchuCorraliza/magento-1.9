<?php

/**
 *
 * @category MultiSafepay
 * @package  MultiSafepay_Msp
 */
class MultiSafepay_Msp_Model_Gateway_Default extends MultiSafepay_Msp_Model_Gateway_Abstract {

    protected $_module = "payment";
    protected $_code = "msp";
    protected $_formBlockType = 'msp/default';

    //protected $_loadSettingsConfig = false; // dont use default settings

    public function setParams($params) {
        if (isset($params['gateway'])) {
            $this->_gateway = preg_replace("|[^a-zA-Z]+|", "", $params['gateway']);
        }
    }

    public function getNotificationUrl() {
        return $this->getModelUrl("msp/standard/notification");
    }

    public function getOrderPlaceRedirectUrl() {
        return $this->getModelUrl("msp/standard/redirect/model/standard/gateway/" . $this->_gateway);
    }

}
