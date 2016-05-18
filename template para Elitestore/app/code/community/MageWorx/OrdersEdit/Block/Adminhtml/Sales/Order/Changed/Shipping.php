<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersEdit_Block_Adminhtml_Sales_Order_Changed_Shipping extends Mage_Adminhtml_Block_Sales_Order_Abstract
{
    /** @var string  */
    protected $_template = 'mageworx/ordersedit/changed/shipping.phtml';

    /**
     * @return mixed
     */
    public function getActiveMethodRate()
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
        /** @var array $rates */
        $rates = $quote->getShippingAddress()
            ->setCollectShippingRates(true)
            ->collectShippingRates()
            ->getGroupedAllShippingRates();
        /** @var string $method */
        $method = $quote->getShippingAddress()->getShippingMethod();

        foreach ($rates as $group) {
            foreach ($group as $code => $rate) {
                if ($rate->getCode() == $method) {
                    return $rate;
                }
            }
        }

        return false;
    }

    /**
     * @param $carrierCode
     * @return string
     */
    public function getCarrierName($carrierCode)
    {
        if ($name = Mage::getStoreConfig('carriers/'.$carrierCode.'/title', $this->getOrder()->getStoreId())) {
            return $name;
        }
        return $carrierCode;
    }

    /**
     * @param $isShippingInclTax
     * @return mixed
     */
    public function getShippingPrice($isShippingInclTax = false)
    {
        if ($isShippingInclTax) {
            $baseShippingAmount = $this->getQuote()->getShippingAddress()->getBaseShippingInclTax();
        } else {
            $baseShippingAmount = $this->getQuote()->getShippingAddress()->getBaseShippingAmount();
        }
        return $this->getQuote()->getStore()->convertPrice(
            $baseShippingAmount,
            true
        );
    }
}