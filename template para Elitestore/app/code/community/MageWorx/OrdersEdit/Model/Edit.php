<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersEdit_Model_Edit extends Mage_Core_Model_Abstract
{
    /**
     * Order items which have already been saved
     *
     * @var array
     */
    protected $_savedOrderItems = array();

    /**
     * The flag shows whether missed quote items have been checked
     *
     * @var bool
     */
    protected $_quoteItemsAlreadyChecked = false;

    /**
     * Get model for logging order changes
     *
     * @return MageWorx_OrdersEdit_Model_Edit_Log
     */
    public function getLogModel()
    {
        return Mage::getSingleton('mageworx_ordersedit/edit_log');
    }

    /**
     * Get quote model by order
     *
     * @param Mage_Sales_Model_Order $order
     * @return boolean|Mage_Sales_Model_Quote
     */
    public function getQuoteByOrder(Mage_Sales_Model_Order $order)
    {
        $this->checkOrderQuote($order);

        $quoteId = $order->getQuoteId();
        $storeId = $order->getStoreId();

        //@todo modification is needed to make correct restoration of bundled products
        $this->checkQuoteItems($quoteId, $order);

        $quote = Mage::getModel('sales/quote')->setStoreId($storeId)->load($quoteId);

        return $quote;
    }

    /**
     * Check whether the order's quote exists. If no then create it from the order
     *
     * @param Mage_Sales_Model_Order $order
     * @return $this
     * @throws Exception
     */
    protected function checkOrderQuote(Mage_Sales_Model_Order $order)
    {
        $quoteId = $order->getQuoteId();
        $quote = Mage::getModel('sales/quote')->setStoreId($order->getStoreId())->load($quoteId);
        if ($quote && $quote->getId()) {
            return $this;
        }

        $convertor = Mage::getSingleton('sales/convert_order');

        // Copy quote data
        $quote = $convertor->toQuote($order);

        // Copy shipping address data
        if ($order->getShippingAddress()) {
            $shippingAddress = $quote->getShippingAddress();
            Mage::helper('core')->copyFieldset('sales_convert_order_address', 'to_quote_address', $order->getShippingAddress(), $shippingAddress);
            Mage::helper('core')->copyFieldset('sales_convert_order', 'to_quote_address_shipping', $order, $shippingAddress);
        }

        // Copy billing address
        if ($order->getBillingAddress()) {
            $billingAddress = $quote->getBillingAddress();
            Mage::helper('core')->copyFieldset('sales_convert_order_address', 'to_quote_address', $order->getBillingAddress(), $billingAddress);
        }

        // Copy payment
        if ($order->getPayment()) {
            $payment = $quote->getPayment();
            $convertor->paymentToQuotePayment($order->getPayment(), $payment);
        }

        // Recreate shipping rates
        if ($quote->getShippingAddress() && !$quote->getShippingAddress()->getGroupedAllShippingRates()) {
            $quote->getShippingAddress()->setCollectShippingRates(true)->collectShippingRates();
        }

        $quote->save();

        $order->setQuoteId($quote->getId())->save();

        return $this;
    }

    /**
     * Check and restore quote items which have been deleted from database
     *
     * @param                        $quoteId
     * @param Mage_Sales_Model_Order $order
     * @internal param \Mage_Sales_Model_Quote $quote
     * @return $this
     */
    protected function checkQuoteItems($quoteId, Mage_Sales_Model_Order $order)
    {
        if ($this->_quoteItemsAlreadyChecked) {
            return $this;
        }

        /** @var MageWorx_OrdersEdit_Helper_Edit $editHelper */
        $editHelper = Mage::helper('mageworx_ordersedit/edit');

        /** @var Mage_Sales_Model_Order_item $orderItem */
        foreach ($order->getAllItems() as $orderItem) {
            $quoteItemId = $orderItem->getQuoteItemId();
            $quoteItem = Mage::getModel('sales/quote_item')->load($quoteItemId);

            if ($quoteItem && $editHelper->isQuoteItemAvailable($quoteItem, $orderItem)) {
                /** TODO: temporary disabled. Bug with not existing quote. Tracker: 10727 
                if ($quoteItem->getQuoteId() != $quoteId && $quoteItem->getId()) {
                    // @var Mage_Sales_Model_Quote $quote
                    $quote = Mage::getModel('sales/quote')->setStoreId($order->getStoreId())->load($quoteId);
                    if($quote && $quote->getId()) {
                        $quoteItem->setQuote($quote);
                        $quoteItem->save();
                    }
                } */
                continue;
            }

            $product = Mage::getModel('catalog/product')
                ->setStoreId($order->getStoreId())
                ->load($orderItem->getProductId());

            $newQuoteItem = Mage::getModel('sales/convert_order')->itemToQuoteItem($orderItem);
            $qty = $this->getQtyRest($orderItem, true);
            $newQuoteItem->setQuoteId($quoteId)
                ->setProduct($product)
                ->setQty($qty)
                ->save();

            $orderItem->setQuoteItemId($newQuoteItem->getItemId())->save();
        }

        $this->_quoteItemsAlreadyChecked = true;

        return $this;
    }

    /**
     * Remove specific qty of order item from order
     *
     * @param Mage_Sales_Model_Order_Item $orderItem
     * @param null $qtyToReturn
     * @return $this
     * @internal param Mage_Sales_Model_Order $order
     */
    protected function returnOrderItem(Mage_Sales_Model_Order_Item $orderItem, $qtyToReturn = null)
    {
        if (is_null($qtyToReturn)) {
            $qtyToReturn = $orderItem->getQtyToRefund() + $orderItem->getQtyToCancel();
        }

        if ($qtyToReturn > 0) {

            if ($orderItem->getParentItem()) {
                $qtyToCancel = $qtyToReturn;
                $qtyToReturn -= $qtyToCancel;
            } else {
                $qtyToCancel = min($qtyToReturn, $orderItem->getQtyToCancel());
                $qtyToReturn -= $qtyToCancel;
            }

            $this->cancelOrderItem($orderItem, $qtyToCancel);
        }

        if ($qtyToReturn > 0 && $orderItem->getQtyToRefund() > 0) {
            $this->refundOrderItem($orderItem, $qtyToReturn);
        }

        return $this;
    }

    /**
     * Refund specific qty of order item
     *
     * @param Mage_Sales_Model_Order_Item $orderItem
     * @param $qtyToRefund
     * @return $this
     * @internal param Mage_Sales_Model_Order $order
     */
    protected function refundOrderItem(Mage_Sales_Model_Order_Item $orderItem, $qtyToRefund)
    {
        $cmModel = Mage::getSingleton('mageworx_ordersedit/edit_creditmemo');
        $cmModel->addItemToRefund($orderItem->getId(), $qtyToRefund);

        if ($orderItem->getProductType() == 'bundle') {
            $orderItem->setQtyRefunded($qtyToRefund);
        }

        return $this;
    }

    /**
     * Cancel specific qty of order item
     *
     * @param Mage_Sales_Model_Order_Item $orderItem
     * @param null                        $qtyToCancel
     * @return Mage_Sales_Model_Order_Item
     */
    protected function cancelOrderItem(Mage_Sales_Model_Order_Item $orderItem, $qtyToCancel = null)
    {
        if ($orderItem->getStatusId() !== Mage_Sales_Model_Order_Item::STATUS_CANCELED) {
            if (!$qtyToCancel) {
                $qtyToCancel = $orderItem->getQtyToCancel();
            }

            $origQtyCancelled = $orderItem->getQtyCanceled();
            $orderItem->setQtyCanceled($origQtyCancelled + $qtyToCancel);
            Mage::dispatchEvent('sales_order_item_cancel', array('item' => $orderItem));

            $orderItem->setTaxCanceled(
                $orderItem->getTaxCanceled() +
                $orderItem->getBaseTaxAmount() * $orderItem->getQtyCanceled() / $orderItem->getQtyOrdered()
            );
            $orderItem->setHiddenTaxCanceled(
                $orderItem->getHiddenTaxCanceled() +
                $orderItem->getHiddenTaxAmount() * $orderItem->getQtyCanceled() / $orderItem->getQtyOrdered()
            );
        }

        return $orderItem;
    }

    /**
     * Invoice order items
     *
     * @param Mage_Sales_Model_Order $order
     * @param array $qtys
     * @return mixed
     * @throws Exception
     * @todo Unused method?
     */
    protected function invoiceOrderItems(Mage_Sales_Model_Order $order, $qtys = array())
    {
        $invoice = Mage::helper('mageworx_ordersedit')->invoiceOrder($order);

        /** @var Mage_Sales_Model_Order_Item $orderItem */
        foreach ($order->getAllItems() as $orderItem) {
            if ($orderItem->getProductType() == 'bundle') {
                $orderItem->setQtyInvoiced($this->getQtyRest($orderItem, false));
                $orderItem->save();
            }
        }

        return $invoice;
    }

    /**
     * Get model for converting quote parts to order
     *
     * @return Mage_Core_Model_Abstract
     */
    public function getConvertor()
    {
        return Mage::getSingleton('mageworx_ordersedit/edit_quote_convert');
    }

    /**
     * Save billng/shipping address
     *
     * @param Mage_Sales_Model_Quote_Address $quoteAddress
     * @param Mage_Sales_Model_Order_Address $orderAddress
     * @return $this
     * @throws Exception
     */
    protected function saveAddress(
        Mage_Sales_Model_Quote_Address $quoteAddress,
        Mage_Sales_Model_Order_Address $orderAddress
    ) {
        $quote = $quoteAddress->getQuote();
        $order = $orderAddress->getOrder();

        $this->getConvertor()->addressToOrderAddress($quoteAddress, $orderAddress);

        if (($quote->getIsVirtual() && $orderAddress->getAddressType() == 'billing')
            || (!$quote->getIsVirtual() && $orderAddress->getAddressType() == 'shipping')
        ) {
            Mage::helper('core')->copyFieldset('sales_convert_quote_address', 'to_order', $quoteAddress, $order);
        }

        $orderAddress->save();
        $quoteAddress->save();

        return $this;
    }

    /**
     * Save payment method
     *
     * @param Mage_Sales_Model_Quote_Payment $quotePayment
     * @param Mage_Sales_Model_Order_Payment $orderPayment
     * @return $this
     * @throws Exception
     */
    protected function savePayment(
        Mage_Sales_Model_Quote_Payment $quotePayment,
        Mage_Sales_Model_Order_Payment $orderPayment
    ) {
        $orderPayment = $this->getConvertor()->paymentToOrderPayment($quotePayment, $orderPayment);
        $orderPayment->save();
        $quotePayment->save();

        return $this;
    }

    /**
     * Save changed order products
     *
     * @param Mage_Sales_Model_Quote $quote
     * @param Mage_Sales_Model_Order $order
     * @param                        $changes
     * @return $this
     */
    protected function saveOldOrderItems(Mage_Sales_Model_Quote $quote, Mage_Sales_Model_Order $order, $changes)
    {
        foreach ($changes as $itemId => $params) {

            /**
             * @var Mage_Sales_Model_Quote_Item $quoteItem
             * @var Mage_Sales_Model_Order_Item $orderItem
             * @var MageWorx_OrdersEdit_Model_Edit_Quote_Convert $converter
             * @var MageWorx_OrdersEdit_Model_Edit_Log $log
             * @var MageWorx_OrdersEdit_Helper_Edit $helper
             * @var Mage_Sales_Model_Quote_Item $childQuoteItem
             * @var Mage_Sales_Model_Order_Item $childOrderItem
             */
            $quoteItem  = $quote->getItemById($itemId);
            $orderItem  = $order->getItemByQuoteItemId($itemId);
            $converter  = $this->getConvertor();
            $helper = Mage::helper('mageworx_ordersedit/edit');
            $log = $this->getLogModel();

            if (!$orderItem || !$helper->checkOrderItemForCancelRefund($orderItem)) {
                continue;
            }

            $orderItemQty = $this->getQtyRest($orderItem);

            if (isset($params['qty']) && $params['qty'] < 0.001) {
                $params['action'] = 'remove';
            }

            if ((isset($params['action']) && $params['action'] == 'remove')) {
                $this->_removeOrderItem($order, $orderItem);

                $itemChange = array(
                    'name'         => $orderItem->getName(),
                    'qty_before'   => $orderItemQty,
                    'qty_after'    => '',
                    'price_before' => $orderItem->getOrigData('price'),
                    'price_after'  => $orderItem->getPrice()
                );
                $log->addItemChange($orderItem->getId(), $itemChange);

                continue;
            }

            if (isset($params['qty']) && $params['qty'] != $orderItemQty) {

                $origQtyOrdered = $orderItem->getQtyOrdered();
                $orderItem = $converter->itemToOrderItem($quoteItem, $orderItem);

                /* Change main item qty */
                $this->changeItemQty($orderItem, $params['qty'], $origQtyOrdered);

                /* Change qty of child products if exists */
                if ($orderItem->getProductType() == 'bundle' || $orderItem->getProductType() == 'configurable') {
                    foreach ($quote->getAllItems() as $childQuoteItem) {
                        if ($childQuoteItem->getParentItemId() == $quoteItem->getId()) {

                            /* Recalculate totals of new order item */
                            $childQuoteItem->save();
                            $childOrderItem = $order->getItemByQuoteItemId($childQuoteItem->getId());
                            $origChildQtyOrdered = $childOrderItem->getQtyOrdered();
                            $childOrderItem = $converter->itemToOrderItem($childQuoteItem, $childOrderItem);

                            /* Change child item qty and save */
                            $this->changeItemQty($childOrderItem, $params['qty'] * $childQuoteItem->getQty(), $origChildQtyOrdered);
                            $childOrderItem->save();
                            $this->_savedOrderItems[] = $childOrderItem->getItemId();

                        }
                    }
                }
            }

            $itemChange = array(
                'name'         => $orderItem->getName(),
                'qty_before'   => $orderItemQty,
                'qty_after'    => $quoteItem->getQty(),
                'price_before' => $orderItem->getOrigData('price'),
                'price_after'  => $orderItem->getPrice()
            );

            /* Check Discount changes */
            if (isset($params['use_discount'])
                && $params['use_discount'] == 1
                && $quoteItem->getOrigData('discount_amount') == 0
                && $quoteItem->getData('discount_amount') > 0
            ) {
                $itemChange['discount'] = 1;
            } elseif ($quoteItem->getData('discount_amount') < 0.001 && $quoteItem->getOrigData('discount_amount') > 0) {
                $itemChange['discount'] = -1;
            }

            /* Add item changes to log */
            if ($itemChange['qty_before'] != $itemChange['qty_after']
                || $itemChange['price_before'] != $itemChange['price_after']
                || isset($itemChange['discount'])
            ) {
                $log->addItemChange($orderItem->getId(), $itemChange);
            }

            $quoteItem->save();
            $orderItem->save();

            $this->_savedOrderItems[] = $orderItem->getItemId();
        }

        return $this;
    }

    /**
     * @param Mage_Sales_Model_Order_Item $orderItem
     * @param int|float $newQty
     * @param int|float $origQtyOrdered
     * @return Mage_Sales_Model_Order_Item
     */
    protected function changeItemQty($orderItem, $newQty, $origQtyOrdered)
    {
        $orderItemQty = $this->getQtyRest($orderItem);

        if ($newQty < $orderItemQty) {
            $qtyToRemove = $orderItemQty - $newQty;
            $orderItem->setQtyOrdered($origQtyOrdered);
            $this->returnOrderItem($orderItem, $qtyToRemove);
        } else {
            $qtyDiff = $newQty - $orderItemQty;
            $this->_removeQtyFromStock($orderItem->getProductId(), $qtyDiff);
            $orderItem->setQtyOrdered($origQtyOrdered + $qtyDiff);
        }

        return $this;
    }

    /**
     * Completely remove item (full qty)
     *
     * @param Mage_Sales_Model_Order $order
     * @param Mage_Sales_Model_Order_Item $orderItem
     * @return $this
     */
    protected function _removeOrderItem(Mage_Sales_Model_Order $order, Mage_Sales_Model_Order_Item $orderItem)
    {
        $this->returnOrderItem($orderItem);
        if ($orderItem->getProductType() == 'bundle' || $orderItem->getProductType() == 'configurable') {
            /** @var Mage_Sales_Model_Order_Item $childOrderItem */
            foreach ($orderItem->getChildrenItems() as $childOrderItem) {
                //@todo check code with invoiced items
                $this->cancelOrderItem($childOrderItem, $this->getQtyRest($childOrderItem, false));
            }
        }

        $orderItem->setSubtotal(0)
            ->setBaseSubtotal(0)
            ->setTaxAmount(0)
            ->setBaseTaxAmount(0)
            ->setTaxPercent(0)
            ->setDiscountAmount(0)
            ->setBaseDiscountAmount(0)
            ->setRowTotal(0)
            ->setBaseRowTotal(0);

        return $this;
    }

    /**
     * Add new products to order
     *
     * @param Mage_Sales_Model_Quote $quote
     * @param Mage_Sales_Model_Order $order
     * @param                        $changes
     * @return $this
     */
    protected function saveNewOrderItems(Mage_Sales_Model_Quote $quote, Mage_Sales_Model_Order $order, $changes)
    {
        /** @var Mage_Sales_Model_Quote_Item $quoteItem */
        foreach ($quote->getAllItems() as $quoteItem) {
            /** @var Mage_Sales_Model_Order_Item $orderItem */
            $orderItem = $order->getItemByQuoteItemId($quoteItem->getItemId());
            if ($orderItem && $orderItem->getItemId()) {
                continue;
            }

            $quoteItem->save();

            $orderItem = $this->getConvertor()->itemToOrderItem($quoteItem, $orderItem);
            $order->addItem($orderItem);
            if ($orderItem->save()) {
                $this->_removeQtyFromStock($orderItem->getProductId(), $orderItem->getQtyOrdered());
            }

            /*** Add new items to log ***/
            $changedItem = $quoteItem;
            $itemChange = array(
                'name'       => $changedItem->getName(),
                'qty_before' => 0,
                'qty_after'  => $changedItem->getQty()
            );
            $this->getLogModel()->addItemChange($changedItem->getId(), $itemChange);

            $this->_savedOrderItems[] = $orderItem->getItemId();
        }

        /** @var Mage_Sales_Model_Quote_Item $childQuoteItem */
        foreach ($quote->getAllItems() as $childQuoteItem) {
            /** @var Mage_Sales_Model_Order_Item $childOrderItem */
            $childOrderItem = $order->getItemByQuoteItemId($childQuoteItem->getItemId());

            /*** Add items relations for configurable and bundle products ***/
            if ($childQuoteItem->getParentItemId()) {
                /** @var Mage_Sales_Model_Order_Item $parentOrderItem */
                $parentOrderItem = $order->getItemByQuoteItemId($childQuoteItem->getParentItemId());

                $childOrderItem->setParentItemId($parentOrderItem->getItemId());
                $childOrderItem->save();
            }

        }

        return $this;
    }

    /**
     * Apply all the changes to order and save it
     *
     * @param Mage_Sales_Model_Quote $quote
     * @param Mage_Sales_Model_Order $order
     * @param                        $changes
     * @return $this
     * @throws Exception
     */
    public function saveOrder(Mage_Sales_Model_Quote $quote, Mage_Sales_Model_Order $order, $changes)
    {
        if (isset($changes['billing_address'])) {
            $this->saveAddress($quote->getBillingAddress(), $order->getBillingAddress());
            unset($changes['billing_address']);
        }

        if (isset($changes['shipping_address'])) {
            $this->saveAddress($quote->getShippingAddress(), $order->getShippingAddress());
            unset($changes['shipping_address']);
        }

        if (isset($changes['payment'])) {
            $this->savePayment($quote->getPayment(), $order->getPayment());
            unset($changes['payment']);
        }

        $this->_savedOrderItems = array();

        if (isset($changes['product_to_add']) && !empty($changes['product_to_add'])) {
            $this->saveNewOrderItems($quote, $order, $changes['product_to_add']);
        }

        if (isset($changes['quote_items'])) {
            $this->saveOldOrderItems($quote, $order, $changes['quote_items']);
        }

        $address = $quote->getIsVirtual() ? $quote->getBillingAddress() : $quote->getShippingAddress();
        Mage::helper('core')->copyFieldset('sales_convert_quote_address', 'to_order', $address, $order);
        $address->save();

        /** @var Mage_Sales_Model_Quote_Item $quoteItem */
        foreach ($quote->getAllVisibleItems() as $quoteItem) {

            /** @var Mage_Sales_Model_Order_Item $orderItem */
            $orderItem = $order->getItemByQuoteItemId($quoteItem->getItemId());

            if (isset($orderItem) && (in_array($orderItem->getItemId(), $this->_savedOrderItems) || !isset($changes['quote_items']))) {
                continue;
            }

            /** @var Mage_Sales_Model_Order_Item $orderItem */
            $orderItem = $this->getConvertor()->itemToOrderItem($quoteItem, $orderItem);
            $orderItem->setOrderId($order->getId());
            $orderItem->save();

            $quoteChildrens = $quoteItem->getChildren();
            $orderChildrens = array();
            foreach ($quoteChildrens as $childQuoteItem) {

                /** @var Mage_Sales_Model_Order_Item $childOrderItem */
                $childOrderItem = $order->getItemByQuoteItemId($childQuoteItem->getItemId());

                if (isset($childOrderItem) && in_array($childOrderItem->getItemId(), $this->_savedOrderItems)) {
                    continue;
                }

                /** @var Mage_Sales_Model_Order_Item $childOrderItem */
                $childOrderItem = $this->getConvertor()->itemToOrderItem($childQuoteItem, $childOrderItem);
                $childOrderItem->setOrderId($order->getId());
                $childOrderItem->setParentItem($orderItem);
                $childOrderItem->setParentItemId($orderItem->getId());
                $childOrderItem->save();
                $orderChildrens[] = $childOrderItem;
            }

            if (!empty($orderChildrens)) {
                foreach ($orderChildrens as $child) {
                    $orderItem->addChildItem($child);
                }
                $orderItem->save();
            }

            /*** Add new items to log ***/
            $changedItem = $quoteItem;
            $itemChange = array(
                'name'       => $changedItem->getName(),
                'qty_before' => 0,
                'qty_after'  => $changedItem->getQty()
            );
            $this->getLogModel()->addItemChange($changedItem->getId(), $itemChange);
        }

        if (empty($changes['customer_id'])) {
            $changes['customer_id'] = $order->getCustomerId();
        }

        // Collect order all items qty
        $changes['total_qty_ordered'] = 0;
        foreach ($order->getAllItems() as $orderItem) {
            $changes['total_qty_ordered'] += $orderItem['qty_ordered'] - $orderItem['qty_canceled'];
        }
        if (isset($changes['status'])) {
            $changes['state'] = $changes['status'];
        }
        $order->addData($changes);

        $this->getLogModel()->commitOrderChanges($order);

        $quote->save();
        $order->save();

        return $this;
    }

    /**
     * @param $productId
     * @param $qty
     */
    protected function _removeQtyFromStock($productId, $qty)
    {
        /** @var Mage_CatalogInventory_Model_Stock_Item $stockItem */
        $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);
        $qtyAfter = $stockItem->getQty() - $qty;
        if ($qtyAfter <= 0) {
            $stockItem->setIsInStock(0);
            $stockItem->setQty(0);
        } else {
            $stockItem->setIsInStock(1);
            $stockItem->setQty($qtyAfter);
        }
        $stockItem->save();
    }

    /**
     * Get rest of available item qty.
     * qty = qty - canceled [ - refunded ] (optional)
     *
     * @param Mage_Sales_Model_Order_Item $item
     * @param bool|false $excludeRefunded
     * @return float|int
     */
    protected function getQtyRest($item, $excludeRefunded = false)
    {
        $qty = $item->getOrigData('qty_ordered') - $item->getQtyCanceled();
        if ($excludeRefunded) {
            $qty -= $item->getQtyRefunded();
        }

        return $qty;
    }
}