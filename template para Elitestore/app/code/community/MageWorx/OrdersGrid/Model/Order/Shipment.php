<?php

/**
 * MageWorx
 * Admin Order Grid  extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersGrid
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_OrdersGrid_Model_Order_Shipment extends Mage_Core_Model_Abstract
{

    /**
     * @param array $orderIds
     * @return int
     */
    public function shipOrders($orderIds)
    {
        $count = 0;

        if (!is_array($orderIds)) {
            return $count;
        }

        foreach ($orderIds as $orderId) {

            $orderId = intval($orderId);
            if ($orderId <= 0) {
                continue;
            }

            try {
                /** @var Mage_Sales_Model_Order $order */
                $order = Mage::getModel('sales/order')->load($orderId);

                if (!$order->getId() || $order->getForcedDoShipmentWithInvoice() || !$order->canShip()) {
                    continue;
                }

                $savedQtys = array();
                foreach ($order->getAllItems() as $orderItem) {
                    $savedQtys[$orderItem->getId()] = $orderItem->getQtyToShip();
                }

                /** @var Mage_Sales_Model_Order_Shipment $shipment */
                $shipment = Mage::getModel('sales/service_order', $order)->prepareShipment($savedQtys);

                if (!$shipment || !$shipment->getTotalQty()) {
                    continue;
                }

                $shipment->register();

                /* If need send email */
                $sendEmailFlag = Mage::helper('mageworx_ordersgrid')->isSendShipmentEmail();
                if ($sendEmailFlag) {
                    $shipment->setEmailSent(true);
                }

                $shipment->getOrder()->setCustomerNoteNotify($sendEmailFlag);
                $shipment->getOrder()->setIsInProcess(true);

                Mage::getModel('core/resource_transaction')
                    ->addObject($shipment)
                    ->addObject($shipment->getOrder())
                    ->save();

                /* Try to send email */
                $shipment->sendEmail($sendEmailFlag, '');

                $count++;

            } catch (Exception $e) {
                Mage::logException($e);
            }
        }

        return $count;
    }
}