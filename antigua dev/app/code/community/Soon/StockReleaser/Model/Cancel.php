<?php

/**
 * Agence Soon
 *
 * @category    Soon
 * @package     Soon_StockReleaser
 * @copyright   Copyright (c) 2011 Agence Soon. (http://www.agence-soon.fr)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author      Hervé Guétin
 */
class Soon_StockReleaser_Model_Cancel extends Mage_Core_Model_Abstract {
    const ORDER_STATUSES_CONFIG = 'stockreleaser/settings/order_statuses';

    public function _construct() {
        $this->_init('stockreleaser/cancel');
    }

    /**
     * Save cancellation date of order
     * 
     * @param Mage_Sales_Model_Order $order
     * @return Soon_StockReleaser_Model_Cancel
     */
    public function registerCancel($order) {
        $paymentMethod = $order->getPayment()->getMethod();

        $leadtimeValue = Mage::getStoreConfig('stockreleaser/leadtime/' . $paymentMethod);
        
        if ($leadtimeValue != '') { //Only continue if leadtime value is set.
            $leadtime = $leadtimeValue * $this->_getLeadtimeMultiplier(Mage::getStoreConfig('stockreleaser/leadtime/' . $paymentMethod . '-unit'));
            $autoCancelDate = date("Y-m-d H:i:s", strtotime($order->getCreatedAt()) + $leadtime);

            $data = array(
                'order_id' => $order->getId(),
                'autocancel_date' => $autoCancelDate,
            );

            $this->setData($data);
            $this->save();
        }

        return $this;
    }

    /**
     * Process order cancellation
     * 
     * @return Soon_StockReleaser_Model_Cancel
     */
    public function processCancel() {

        $orders = Mage::getModel('sales/order')
                ->getCollection()
                ->addAttributeToFilter('status', array('in' => $this->_getOrderStatuses()));

        foreach ($orders as $order) {
            $ordersIds[] = $order->getId();
        }

        $ordersToCancel = $this->getResourceCollection()
                ->addFieldToFilter('order_id', array('in' => $ordersIds))
                ->addFieldToFilter('autocancel_date', array('lt' => now()))
                ->addIsNotCanceledFilter();

        foreach ($ordersToCancel as $orderToCancel) {
            $order = Mage::getModel('sales/order')->load($orderToCancel->getOrderId());
            $order->cancel()
                    ->save();

            $orderComment = Mage::helper('stockreleaser')->__('This order has been automatically cancelled by the "Soon_StockReleaser" module.');
            $order->setStatus(Mage_Sales_Model_Order::STATE_CANCELED)
                    ->addStatusHistoryComment($orderComment)
                    ->save();

            $orderToCancel->setAutocancelStatus(1)->save();
        }

        return $this;
    }

    /**
     * Delete outdated orders to automatically cancel
     * 
     * Those are the ones that are not automatically canceled yet but completed.
     * 
     * @return Soon_StockReleaser_Model_Cancel
     */
    public function cleanCompletedOrders() {
        $collection = $this->getResourceCollection()->getCompletedOrders();
        $collection->walk('delete');
        return $this;
    }

    /**
     * Returns multiplier for seconds based on unit used in config
     * 
     * @param string $unit
     * @return int
     */
    protected function _getLeadtimeMultiplier($unit) {
        $leadtimeMultipliers = array(
            'min' => 60,
            'hour' => 3600,
            'day' => 86400
        );

        return (int) $leadtimeMultipliers[$unit];
    }

    /**
     * Retrieve array of order statuses that must be automatically cancelled
     * 
     * @return array
     */
    protected function _getOrderStatuses() {
        $orderStatusesConfig = Mage::getStoreConfig(self::ORDER_STATUSES_CONFIG);
        $orderStatuses = explode(',', $orderStatusesConfig);

        return $orderStatuses;
    }

}
