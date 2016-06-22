<?php
/**
 * Event sales_order_invoice_pay
 *
 */
require_once 'Mage/Sales/Model/Observer.php';
class Customex_SendInvoice_Model_Observer extends Mage_Sales_Model_Observer {
    
    public function sendInvoiceEmail($observer) {
            $invoice = $observer->getEvent ()->getInvoice ();
            
            switch ($invoice->getState ()) {
                case Mage_Sales_Model_Order_Invoice::STATE_PAID :
                    
                    if (! $invoice->getOrder ()->getEmailSent ()) {
                        $invoice->sendEmail ();
                    }
                    break;
            }
        return $this;
    }
}
