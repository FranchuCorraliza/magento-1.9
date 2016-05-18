<?php
/**
 * MageWorx
 * Admin Order Grid  extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersGrid
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersGrid_Model_Order_Invoice extends Mage_Core_Model_Abstract {

    /**
     * Create invoice for order
     *
     * @param Mage_Sales_Model_Order $order
     * @return bool
     */
    public function invoiceOrder(Mage_Sales_Model_Order $order)
    {
        return $this->create($order);
    }

    /**
     * Mass invoice orders by ids
     * Return count of invoiced orders
     *
     * @param array $orderIds
     * @return int
     */
    public function invoiceOrders($orderIds)
    {
        $count = 0;

        foreach ($orderIds as $orderId) {
            $orderId = intval($orderId);
            if ($orderId <= 0) {
                continue;
            }

            /** @var Mage_Sales_Model_Order $order */
            $order = Mage::getModel('sales/order')->load($orderId);
            if (!$order->getId()) {
                continue;
            }
            if (!$order->canInvoice()) {
                continue;
            }

            $invoice = $this->invoiceOrder($order);
            if ($invoice) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return bool
     * @throws Exception
     * @throws bool
     */
    protected function create(Mage_Sales_Model_Order $order)
    {
        /** @var MageWorx_OrdersGrid_Helper_Data $helper */
        $helper = Mage::helper('mageworx_ordersgrid');
        $savedQtys = array();

        /** @var Mage_Sales_Model_Order_Item $orderItem */
        foreach ($order->getAllItems() as $orderItem) {
            if ($orderItem->getQtyToInvoice() > 0) {
                $savedQtys[$orderItem->getId()] = $orderItem->getQtyToInvoice();
            }
        }

        /** @var Mage_Sales_Model_Order_Invoice $invoice */
        $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice($savedQtys);
        if (!$invoice->getTotalQty()) {
            return false;
        }

        $invoice->setRequestedCaptureCase('online');
        $invoice->register();

        // if send email
        $sendEmailFlag = $helper->isSendInvoiceEmail();
        if ($sendEmailFlag) {
            $invoice->setEmailSent(true);
        }

        $invoice->getOrder()->setCustomerNoteNotify($sendEmailFlag);
        $invoice->getOrder()->setIsInProcess(true);

        $transactionSave = Mage::getModel('core/resource_transaction')
            ->addObject($invoice)
            ->addObject($invoice->getOrder());
        $transactionSave->save();

        // if send email
        $invoice->sendEmail($sendEmailFlag, '');

        return true;
    }

}