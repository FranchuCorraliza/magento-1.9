<?php

/**
 * MageWorx
 * Admin Order Grid  extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersGrid
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_OrdersGrid_Model_Order extends Mage_Core_Model_Abstract
{

    /**
     * Cancel & Delete order completely (from DB)
     *
     * @param Mage_Sales_Model_Order | int $order
     * @throws Exception
     */
    public function deleteOrderCompletelyById($order)
    {
        if (is_object($order)) {
            $orderId = $order->getId();
        } else {
            /** @var Mage_Sales_Model_Order $order */
            $order = Mage::getModel('sales/order')->load(intval($order), 'entity_id');
            $orderId = $order->getId();
        }

        if ($orderId) {
            /* Cancel order before delete */
            try {
                $order->cancel()->save();
            } catch (Exception $e) {
                Mage::logException($e);
            }

            /* Delete order from DB */
            Mage::getResourceModel('mageworx_ordersgrid/order')->deleteOrderCompletely($order);
        }
    }

}