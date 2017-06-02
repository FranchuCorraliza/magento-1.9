<?php

/**
 *
 * @category MultiSafepay
 * @package  MultiSafepay_Msp
 */
class MultiSafepay_Msp_Model_Config_Sources_Groups {

    var $_options = null;

    public function toOptionArray() {
        if (!$this->_options) {


            $this->_options = Mage::getModel('customer/group')->getCollection()->toOptionArray();

            array_unshift($this->_options, array('value' => '', 'label' => Mage::helper('adminhtml')->__('-- Please Select --')));
        }
        return $this->_options;
    }

}
