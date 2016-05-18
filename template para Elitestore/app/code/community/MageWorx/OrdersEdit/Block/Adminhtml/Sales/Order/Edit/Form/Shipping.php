<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersEdit_Block_Adminhtml_Sales_Order_Edit_Form_Shipping extends Mage_Adminhtml_Block_Sales_Order_Create_Shipping_Method_Form
{
    /**
     * Prepare layout for shipping method form
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->setTemplate('mageworx/ordersedit/edit/shipping_method.phtml');
        return $this;
    }

    /**
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        /** @var Mage_Sales_model_Order $order */
        $order = $this->getOrder() ? $this->getOrder() : Mage::registry('ordersedit_order');
        /** @var Mage_Sales_Model_Quote $quote */
        $quote = Mage::getSingleton('mageworx_ordersedit/edit')->getQuoteByOrder($order);
        $pendingChanges = Mage::helper('mageworx_ordersedit/edit')->getPendingChanges($order->getId());
        if (!empty($pendingChanges)) {
            /** @var Mage_Sales_Model_Quote $quote */
            $quote = Mage::getSingleton('mageworx_ordersedit/edit_quote')->applyDataToQuote($quote, $pendingChanges);
        }

        return $quote;
    }

    /**
     * Retrieve quote shipping address model
     *
     * @return Mage_Sales_Model_Quote_Address
     */
    public function getAddress()
    {
        return $this->getQuote()->getShippingAddress();
    }

    /**
     * Retrieve array of shipping rates groups
     *
     * @return array
     */
    public function getShippingRates()
    {
        if (empty($this->_rates)) {
            /** @var Mage_Sales_Model_Quote_Address $address */
            $address = $this->getAddress();
            $address->setCollectShippingRates(true);
            $address = $address->collectShippingRates();
            $groups = $address->getGroupedAllShippingRates();
            return $this->_rates = $groups;
        }
        return $this->_rates;
    }
}