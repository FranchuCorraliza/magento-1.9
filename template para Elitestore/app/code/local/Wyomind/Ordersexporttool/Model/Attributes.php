<?php

class Wyomind_Ordersexporttool_Model_Attributes extends Mage_Core_Model_Abstract {

    protected function _construct() {


        $this->_init('ordersexporttool/attributes');
    }

    protected function _beforeSave() {

        if (is_array($this->getAttributeOrderItem()))
            $this->setAttributeOrderItem(implode(',', $this->getAttributeOrderItem()));
        if (is_array($this->getAttributeOrderAddress()))
            $this->setAttributeOrderAddress(implode(',', $this->getAttributeOrderAddress()));
        if (is_array($this->getAttributeInvoice()))
            $this->setAttributeInvoice(implode(',', $this->getAttributeInvoice()));
        if (is_array($this->getAttributeShipment()))
            $this->setAttributeShipment(implode(',', $this->getAttributeShipment()));
        if (is_array($this->getAttributeCreditmemo()))
            $this->setAttributeCreditmemo(implode(',', $this->getAttributeCreditmemo()));
        if (is_array($this->getAttributeOrderPayment()))
            $this->setAttributeOrderPayment(implode(',', $this->getAttributeOrderPayment()));

        return parent::_beforeSave();
    }

}