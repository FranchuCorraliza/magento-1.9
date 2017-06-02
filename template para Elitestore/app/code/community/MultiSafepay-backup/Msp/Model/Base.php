<?php

/**
 *
 * @category MultiSafepay
 * @package  MultiSafepay_Msp
 */
require_once(Mage::getBaseDir('lib') . DS . 'multisafepay' . DS . 'MultiSafepay.combined.php');

class MultiSafepay_Msp_Model_Base extends Varien_Object {

    protected $_config;
    protected $_order = null;
    protected $_lockId = null;
    protected $_lockCode = 'msp';
    protected $_isLocked = null;
    protected $_lockFile = null;
    protected $_lockFilename = null;
    protected $_logId = null;
    protected $_logFileName = 'multisafepay.log';
    public $api = null;
    public $payafterapi = null;
    public $invoiceSaved = null;
    public $transdetails;
    public $mspDetails;
    protected $_paid;
    public $methodMap = array(
        'IDEAL' => 'msp_ideal',
        'CREDITCARD' => 'msp_creditcard',
        'DOTPAY' => 'msp_dotpay',
        'PAYAFTER' => 'msp_payafter',
        'EINVOICE' => 'msp_einvoice',
        'KLARNA' => 'msp_klarna',
        'MISTERCASH' => 'msp_mistercash',
        'VISA' => 'msp_visa',
        'EPS' => 'msp_eps',
        'FERBUY' => 'msp_ferbuy',
        'MASTERCARD' => 'msp_mastercard',
        'BANKTRANS' => 'msp_banktransfer',
        'MAESTRO' => 'msp_maestro',
        'PAYPAL' => 'msp_paypal',
        'AMEX' => 'msp_amex',
        'WEBSHOPGIFTCARD' => 'msp_webgift',
        'EBON' => 'msp_ebon',
        'BABYGIFTCARD' => 'msp_babygiftcard',
        'BOEKENBON' => 'msp_boekenbon',
        'EROTIEKBON' => 'msp_erotiekbon',
        'GIVEACARD' => 'msp_giveacard',
        'PARFUMNL' => 'msp_parfumnl',
        'PARFUMCADEAUKAART' => 'msp_parfumcadeaukaart',
        'DEGROTESPEELGOEDWINKEL' => 'msp_degrotespeelgoedwinkel',
        'GIROPAY' => 'msp_giropay',
        'MULTISAFEPAY' => 'msp_multisafepay',
        'DIRECTBANK' => 'msp_directebanking',
        'DIRDEB' => 'msp_directdebit',
        'YOURGIFT' => 'msp_yourgift',
        'WIJNCADEAU' => 'msp_wijncadeau',
        'LIEF' => 'msp_lief',
        'GEZONDHEIDSBON' => 'msp_gezondheidsbon',
        'FASHIONCHEQUE' => 'msp_fashioncheque',
        'FASHIONGIFTCARD' => 'msp_fashiongiftcard',
        'PODIUM' => 'msp_podium',
        'VVVGIFTCARD' => 'msp_vvvgiftcard',
        'SPORTENFIT' => 'msp_sportenfit',
        'BEAUTYANDWELLNESS' => 'msp_beautyandwellness',
    );

    /**
     * Set the config object of the Base
     */
    public function setConfigObject($config) {
        $this->_config = $config;
        return $this;
    }

    /**
     * @param $name string
     * @return bool|string
     */
    public function getConfigData($name) {
        if (isset($this->_config[$name])) {
            return $this->_config[$name];
        }

        return false;
    }

    /**
     * Logging functions
     */
    public function isDebug() {
        return $this->getConfigData('debug');
    }

    public function setLogId($id = null) {
        $this->_logId = $id;
    }

    public function log() {
        $argv = func_get_args();
        $data = array_shift($argv);

        if (is_string($data)) {
            $logData = @vsprintf($data, $argv);

            // if vsprintf failed, just use the data
            if (!$logData) {
                $logData = $data;
            }
            if ($this->_logId) {
                $logData = '[' . $this->_logId . '] ' . $logData;
            }
        } else {
            $logData = $data;
        }

        if ($this->isDebug()) {
            Mage::log($logData, null, $this->_logFileName);
        }
    }

    /**
     * Returns an instance of de Api and set some standard settings
     */
    public function getApi() {
        if ($this->api) {
            return $this->api;
        }

        $this->api = new MultiSafepay();
        $this->api->plugin_name = 'Magento';
        $this->api->version = Mage::getConfig()->getNode('modules/MultiSafepay_Msp/version');
        $this->api->use_shipping_notification = false;
        $this->api->test = ($this->getConfigData("test_api") == 'test');
        $this->api->merchant['account_id'] = $this->getConfigData("account_id");
        $this->api->merchant['site_id'] = $this->getConfigData("site_id");
        $this->api->merchant['site_code'] = $this->getConfigData("secure_code");
        $this->api->plugin['shop'] = 'Magento';
        $this->api->plugin['shop_version'] = Mage::getVersion();
        $this->api->plugin['plugin_version'] = $this->api->version;
        $this->api->plugin['partner'] = '';
        $this->api->plugin['shop_root_url'] = '';

        return $this->api;
    }

    /**
     * Returns an instance of de Payafter Api and set some standard settings
     */
    public function getPayAfterApi($order) {
        if ($this->api) {
            return $this->api;
        }

        $isTestMode = (Mage::getStoreConfig('msp_gateways/' . $order->getPayment()->getMethodInstance()->getCode() . '/test_api_pad', $order->getStoreId()) == MultiSafepay_Msp_Model_Config_Sources_Accounts::TEST_MODE);
        $suffix = '';

        if ($isTestMode) {
            $suffix = '_test';
        }

        $this->api = new MultiSafepay();
        $this->api->plugin_name = 'Magento';
        $this->api->version = Mage::getConfig()->getNode('modules/MultiSafepay_Msp/version');
        $this->api->use_shipping_notification = false;
        $this->api->test = $isTestMode;
        $this->api->merchant['account_id'] = Mage::getStoreConfig('msp_gateways/' . $order->getPayment()->getMethodInstance()->getCode() . '/account_id_pad' . $suffix, $order->getStoreId());
        $this->api->merchant['site_id'] = Mage::getStoreConfig('msp_gateways/' . $order->getPayment()->getMethodInstance()->getCode() . '/site_id_pad' . $suffix, $order->getStoreId());
        $this->api->merchant['site_code'] = Mage::getStoreConfig('msp_gateways/' . $order->getPayment()->getMethodInstance()->getCode() . '/secure_code_pad' . $suffix, $order->getStoreId());
        $this->api->plugin['shop'] = 'Magento';
        $this->api->plugin['shop_version'] = Mage::getVersion();
        $this->api->plugin['plugin_version'] = $this->api->version;
        $this->api->plugin['partner'] = '';
        $this->api->plugin['shop_root_url'] = '';

        return $this->api;
    }

    //set api based on gateway settings
    public function getGatewaysApi($order, $gateway_code) {
        if ($this->api) {
            return $this->api;
        }

        $isTestMode = (Mage::getStoreConfig('msp_giftcards/' . $gateway_code . '/test_api_pad', $order->getStoreId()) == MultiSafepay_Msp_Model_Config_Sources_Accounts::TEST_MODE);
        $suffix = '';

        if ($isTestMode) {
            $suffix = '_test';
        }

        $this->api = new MultiSafepay();
        $this->api->plugin_name = 'Magento';
        $this->api->version = Mage::getConfig()->getNode('modules/MultiSafepay_Msp/version');
        $this->api->use_shipping_notification = false;
        $this->api->test = $isTestMode;
        $this->api->merchant['account_id'] = Mage::getStoreConfig('msp_giftcards/' . $gateway_code . '/account_id_pad' . $suffix, $order->getStoreId());
        $this->api->merchant['site_id'] = Mage::getStoreConfig('msp_giftcards/' . $gateway_code . '/site_id_pad' . $suffix, $order->getStoreId());
        $this->api->merchant['site_code'] = Mage::getStoreConfig('msp_giftcards/' . $gateway_code . '/secure_code_pad' . $suffix, $order->getStoreId());
        $this->api->plugin['shop'] = 'Magento';
        $this->api->plugin['shop_version'] = Mage::getVersion();
        $this->api->plugin['plugin_version'] = $this->api->version;
        $this->api->plugin['partner'] = '';
        $this->api->plugin['shop_root_url'] = '';

        return $this->api;
    }

    /**
     * Update an order according to the specified MultiSafepay status
     */
    public function updateStatus($order, $mspStatus, $mspDetails = array()) {
        $orderSaved = false;
        $statusInitialized = $this->getConfigData("initialized_status");
        $statusBanktransferInitialized = $this->getConfigData("initialized_banktransfer_status");
        $statusComplete = $this->getConfigData("complete_status");
        $statusUncleared = $this->getConfigData("uncleared_status");
        $statusVoid = $this->getConfigData("void_status");
        $statusDeclined = $this->getConfigData("declined_status");
        $statusExpired = $this->getConfigData("expired_status");
        $statusRefunded = $this->getConfigData("refunded_status");
        $statusPartialRefunded = $this->getConfigData("partial_refunded_status");
        $autocreateInvoice = $this->getConfigData("autocreate_invoice");

        $creditmemo_enabled = $this->getConfigData("use_refund_credit_memo");

        if ($creditmemo_enabled && ($mspStatus == 'refunded' || $mspStatus == 'partial_refunded')) {
            return true;
        }

        $usedMethod = $this->methodMap[$mspDetails['paymentdetails']['type']];



        /**
         *    Create the transaction details array
         */
        $transdetails = array();
        $transdetails['ewallet'] = '--------------------';
        foreach ($mspDetails['ewallet'] as $key => $value) {
            $transdetails[$key] = $value;
        }

        $transdetails['customer'] = '--------------------';
        foreach ($mspDetails['customer'] as $key => $value) {
            $transdetails[$key] = $value;
        }

        $transdetails['transaction'] = '--------------------';
        foreach ($mspDetails['transaction'] as $key => $value) {
            $transdetails[$key] = $value;
        }

        $transdetails['paymentdetails'] = '--------------------';
        foreach ($mspDetails['paymentdetails'] as $key => $value) {
            $transdetails[$key] = $value;
        }

        $this->transdetails = $transdetails;
        $this->mspDetails = $mspDetails;
        /*
         * We need to update the shippingmethods for FCO transactions because these can still change after the order is created
         */
        $details = $mspDetails;
        $quoteid = $order->getQuoteId();
        $quote = Mage::getModel('sales/quote')->load($quoteid);


        if (!empty($details['shipping']['type'])) {
            $qAddress = $order->getShippingAddress();
            $qAddress->setTaxAmount($details['total-tax']['total']);
            $qAddress->setBaseTaxAmount($details['total-tax']['total']);

            if ($details['shipping']['type'] == 'flat-rate-shipping') {
                $method = 'mspcheckout_flatrate';
            } elseif ($details['shipping']['type'] == 'pickup') {
                $method = 'mspcheckout_pickup';
            }

            if (!empty($method)) {
                //Mage::getSingleton('tax/config')->setShippingPriceIncludeTax(false);

                $excludingTax = $details['shipping']['cost'];

                $order->setShippingMethod($method)
                        ->setShippingDescription($details['shipping']['name'])
                        ->setShippingAmount($excludingTax, true)
                        ->setBaseShippingAmount($excludingTax, true);

                $includingTax = Mage::helper('tax')->getShippingPrice($excludingTax, true, $qAddress, $quote->getCustomerTaxClassId());
                $shippingTax = $includingTax - $excludingTax;
                $order->setShippingTaxAmount($shippingTax)
                        ->setBaseShippingTaxAmount($shippingTax)
                        ->setShippingInclTax($includingTax)
                        ->setBaseShippingInclTax($includingTax);
            } else {
                $order->setShippingMethod(null);
            }

            $order->setGrandTotal($details['order-total']['total'] + $details['shipping']['cost']);
            $order->setBaseGrandTotal($details['order-total']['total'] + $details['shipping']['cost']);
            $order->setTotalPaid($details['order-total']['total'] + $details['shipping']['cost']);
            $order->save();
        }




        $complete = false;
        $cancel = false;
        $newState = null;
        $newStatus = true; // makes Magento use the default status belonging to state
        $statusMessage = '';
        //$this->_paid = $details['transaction']['amount']/100;
        //If the order already has in invoice than it was paid for using another method? So if our transaction expires we shouldn't update it to cancelled because it was already invoiced.
        if ($order->hasInvoices() && $mspStatus == 'expired') {
            return true;
        }

        if (($usedMethod == 'PAYAFTER' || $usedMethod == 'KLARNA' || $usedMethod == 'EINVOICE') && ($mspStatus == 'cancelled' || $mspStatus == 'void')) {
            return true;
        }

        $payment_method_quote = $quote->getPayment();
        $payment = $order->getPayment();

        if ($usedMethod != $payment->getMethod() && !empty($usedMethod)) {
            $payment->setMethod($usedMethod);
            $payment->save();
            $payment_method_quote->setMethod($usedMethod);
            $payment_method_quote->save();
        }

        switch ($mspStatus) {
            case "initialized":

                if ($mspDetails['paymentdetails']['type'] == 'BANKTRANS') {
                    $newState = Mage_Sales_Model_Order::STATE_NEW;
                    $newStatus = $statusBanktransferInitialized;
                    $statusMessage = Mage::helper("msp")->__("Banktransfer transaction started, waiting for payment");
                } else {
                    $newState = Mage_Sales_Model_Order::STATE_NEW;
                    $newStatus = $statusInitialized;
                    $statusMessage = Mage::helper("msp")->__("Transaction started, waiting for payment");
                }

                break;
            case "completed":
                $complete = true;
                $newState = Mage_Sales_Model_Order::STATE_PROCESSING;
                $newStatus = $statusComplete;
                $statusMessage = Mage::helper("msp")->__("Payment Completed");
                //order is paid so set it to paid
                //$order->setTotalPaid($order->getGrandTotal());

                $payment = $order->getPayment();
                $transaction = $payment->getTransaction($this->mspDetails['ewallet']['id']);
                if (is_object($transaction)) {
                    $transaction->setAdditionalInformation(Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS, $this->transdetails);
                    $transaction->save();
                }

                /* if(round($order->getGrandTotal(),2) != $this->_paid && $mspDetails['ewallet']['fastcheckout'] !='YES' && !Mage::getStoreConfigFlag('msp/settings/allow_convert_currency')){
                  $newState = Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW;
                  $newStatus = Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW;
                  $statusMessage = Mage::helper("msp")->__("Payment received for an amount that is not equal to the order total amount. Please verify the paid amount!");
                  } */

                break;
            case "uncleared":
                $newState = Mage_Sales_Model_Order::STATE_NEW;
                $newStatus = $statusUncleared;
                $statusMessage = Mage::helper("msp")->__("Transaction started, waiting for payment");
                break;
            case "void":
                $newState = Mage_Sales_Model_Order::STATE_CANCELED;
                $statusMessage = Mage::helper("msp")->__("Transaction voided");
                $newStatus = $statusVoid;
                if ($order->getState() != 'complete' && !$order->hasInvoices()) {
                    $order->cancel(); // this trigers stock updates
                    $cancel = true;
                    $order->setState($newState, $newStatus, $statusMessage)->save();
                    $orderSaved = true;
                }
                break;
            case "declined":
                $newState = Mage_Sales_Model_Order::STATE_CANCELED;
                $statusMessage = Mage::helper("msp")->__("Transaction declined");
                $newStatus = $statusDeclined;
                if ($order->getState() != 'complete' && !$order->hasInvoices()) {
                    $order->cancel(); // this trigers stock updates
                    $cancel = true;
                    $order->setState($newState, $newStatus, $statusMessage)->save();
                    $orderSaved = true;
                }
                break;
            case "expired":
                $newState = Mage_Sales_Model_Order::STATE_CANCELED;
                $statusMessage = Mage::helper("msp")->__("Transaction is expired");
                $newStatus = $statusExpired;
                if ($order->getState() != 'complete' && !$order->hasInvoices()) {
                    $order->cancel(); // this trigers stock updates
                    $cancel = true;
                    $order->setState($newState, $newStatus, $statusMessage)->save();
                    $orderSaved = true;
                }
                break;
            case "cancelled":
                $newState = Mage_Sales_Model_Order::STATE_CANCELED;
                $statusMessage = Mage::helper("msp")->__("Transaction canceled");
                $newStatus = $statusVoid;
                if ($order->getState() != 'complete' && !$order->hasInvoices()) {
                    $order->cancel(); // this trigers stock updates
                    $cancel = true;
                    $order->setState($newState, $newStatus, $statusMessage)->save();
                    $orderSaved = true;
                }
                break;
            case "refunded":
                $statusMessage = Mage::helper("msp")->__("Transaction refunded");
                $newState = Mage_Sales_Model_Order::STATE_CANCELED;
                $newStatus = $statusRefunded;
                $payment = $order->getPayment();
                $payment->setTransactionId($mspDetails['ewallet']['id']);
                $transaction = $payment->addTransaction('refund', null, false, $statusMessage);
                $transaction->setParentTxnId($mspDetails['ewallet']['id']);
                $transaction->setIsClosed(1);
                $transaction->setAdditionalInformation(Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS, $transdetails);
                $transaction->save();
                break;
            case "partial_refunded":

                $statusMessage = Mage::helper("msp")->__("Transaction partially refunded");
                $newState = Mage_Sales_Model_Order::STATE_PROCESSING;
                $newStatus = $statusPartialRefunded;
                $payment = $order->getPayment();
                $payment->setTransactionId($mspDetails['ewallet']['id']);
                $transaction = $payment->addTransaction('refund', null, false, $statusMessage);
                $transaction->setParentTxnId($mspDetails['ewallet']['id']);
                $transaction->setIsClosed(1);
                $transaction->setAdditionalInformation(Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS, $transdetails);
                $transaction->save();
                break;
            default:
                $statusMessage = Mage::helper("msp")->__("Status not found " . $mspStatus);
                return false;
        }

        // create the status message
        $paymentType = '';
        if (!empty($mspDetails['paymentdetails']['type'])) {
            $paymentType = Mage::helper("msp")->__("Payment Type: <strong>%s</strong>", $mspDetails['paymentdetails']['type']) . '<br/>';
        }

        $statusMessage .= '<br/>' . Mage::helper("msp")->__("Status: <strong>%s</strong>", $mspStatus) . '<br/>' . $paymentType;

        $current_state = $order->getState();
        $canUpdate = false;


        /**
         *     TESTING UNDO CANCEL
         *    Start undo cancel function
         */
        if ($current_state == Mage_Sales_Model_Order::STATE_CANCELED && $newState != Mage_Sales_Model_Order::STATE_CANCELED) {
            foreach ($order->getItemsCollection() as $item) {
                if ($item->getQtyCanceled() > 0) {
                    $item->setQtyCanceled(0)->save();
                }
            }

            $products = $order->getAllItems();

            if (Mage::getStoreConfigFlag('cataloginventory/options/can_subtract')) {
                $products = $order->getAllItems();

                foreach ($products as $itemId => $product) {
                    $id = $product->getProductId();
                    $stock_obj = Mage::getModel('cataloginventory/stock_item')->loadByProduct($id);
                    $stockData = $stock_obj->getData();

                    $new = $stockData['qty'] - $product->getQtyOrdered();
                    $stockData['qty'] = $new;
                    $stock_obj->setData($stockData);
                    $stock_obj->save();
                }
            }

            $order->setBaseDiscountCanceled(0)
                    ->setBaseShippingCanceled(0)
                    ->setBaseSubtotalCanceled(0)
                    ->setBaseTaxCanceled(0)
                    ->setBaseTotalCanceled(0)
                    ->setDiscountCanceled(0)
                    ->setShippingCanceled(0)
                    ->setSubtotalCanceled(0)
                    ->setTaxCanceled(0)
                    ->setTotalCanceled(0);

            $state = 'new';
            $status = 'pending';

            $order->setStatus($status)->setState($state)->save();
            $order->addStatusToHistory($status, 'Order has been reopened because a new transaction was started by the customer!');
        }
        /**
         *    ENDING UNDO CANCEL CODE
         */
        if (!$this->isStatusInHistory($order, $mspStatus)) {
            if ($order->hasInvoices()) {
                $is_already_invoiced = true;
            } else {
                $is_already_invoiced = false;


                if ($complete && $autocreateInvoice) {// && $this->_paid == round($order->getBaseGrandTotal(), 2) && !Mage::getStoreConfigFlag('msp/settings/allow_convert_currency')) {
                    $payment = $order->getPayment();
                    $payment->setTransactionId($mspDetails['ewallet']['id']);
                    $transaction = $payment->addTransaction('capture', null, false, $statusMessage);
                    $transaction->setParentTxnId($mspDetails['ewallet']['id']);
                    $transaction->setIsClosed(1);
                    $transaction->setAdditionalInformation(Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS, $transdetails);
                    $transaction->save();
                    $this->createInvoice($order); // Validate this function with 1.7.0.2 and lower
                    $is_already_invoiced = true;
                } elseif ($complete && $order->getState() == Mage_Sales_Model_Order::STATE_NEW) {
                    $payment = $order->getPayment();
                    $payment->setTransactionId($mspDetails['ewallet']['id']);
                    $transaction = $payment->addTransaction('capture', null, false, $statusMessage);
                    $transaction->setParentTxnId($mspDetails['ewallet']['id']);
                    $transaction->setIsClosed(1);
                    $transaction->setAdditionalInformation(Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS, $transdetails);
                    $transaction->save();
                    /* if(!Mage::getStoreConfigFlag('msp/settings/allow_convert_currency')){
                      $payment->setAmount($this->_paid);
                      $order->setTotalPaid($this->_paid);
                      }else{
                      $payment->setAmount($order->getGrandTotal());
                      $order->setTotalPaid($order->getGrandTotal());
                      } */
                }
            }


            if ($order->getState() == Mage_Sales_Model_Order::STATE_NEW || $order->getState() != 'complete') {
                $canUpdate = true;
            } else {

                $canUpdate = false;
            }

            // update the status if changed
            if ($canUpdate && (($newState != $order->getState()) || ($newStatus != $order->getStatus()))) {

                if (!$cancel) {
                    $order->setState($newState, $newStatus, $statusMessage);
                }

                $send_update_email = $this->getConfigData("send_update_email");

                if ($send_update_email) {
                    $order->sendOrderUpdateEmail(true);
                }


                //$this->createInvoice($order);// Validate this function with 1.7.0.2 and lower
            } else {
                // add status to history if it's not there
                if (!$this->isStatusInHistory($order, $mspStatus) && (ucfirst($order->getState()) != ucfirst(Mage_Sales_Model_Order::STATE_CANCELED))) {
                    $order->addStatusToHistory($order->getStatus(), $statusMessage);
                }
            }

            /**
             *    Fix to activate new order email function to be activated
             */
            $send_order_email = $this->getConfigData("new_order_mail");

            if ($order->getCanSendNewEmailFlag()) {
                if ($send_order_email == 'after_payment') {
                    if (!$order->getEmailSent() && ((ucfirst($order->getState()) == ucfirst(Mage_Sales_Model_Order::STATE_PROCESSING)) || (ucfirst($order->getState()) == ucfirst(Mage_Sales_Model_Order::STATE_COMPLETE)))) {
                        $order->sendNewOrderEmail();
                        $order->setEmailSent(true);
                        $order->save();
                        $orderSaved = true;
                    } elseif (!$order->getEmailSent() && $mspDetails['paymentdetails']['type'] == 'BANKTRANS') {
                        $order->sendNewOrderEmail();
                        $order->setEmailSent(true);
                        $order->save();
                    }
                } elseif ($send_order_email == 'after_notify_without_cancel' && (ucfirst($order->getState()) != ucfirst(Mage_Sales_Model_Order::STATE_CANCELED))) {
                    if (!$order->getEmailSent()) {
                        $order->sendNewOrderEmail();
                        $order->setEmailSent(true);
                        $order->save();
                        $orderSaved = true;
                    }
                } elseif ($send_order_email == 'after_notify_with_cancel') {
                    if (!$order->getEmailSent()) {
                        $order->sendNewOrderEmail();
                        $order->setEmailSent(true);
                        $order->save();
                        $orderSaved = true;
                    }
                }
            }

            if ($mspStatus == 'completed' && $mspDetails['paymentdetails']['type'] == 'KLARNA') {
                $order->addStatusToHistory($order->getStatus(), Mage::helper("msp")->__("Klarna Reservation number: ") . $mspDetails['paymentdetails']['externaltransactionid']);
            }

            // save order if we haven't already
            if (!$orderSaved) {
                $order->save();
            }
        }



        // success
        return true;
    }

    /**
     * Check if a certain MultiSafepay status is already in the order history (to prevent doubles)
     */
    public function isStatusInHistory($order, $mspStatus) {
        $history = $order->getAllStatusHistory();
        foreach ($history as $status) {
            if (strpos($status->getComment(), 'Status: <strong>' . $mspStatus . '</strong>') !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if a certain MultiSafepay status is already in the order history (to prevent doubles)
     */
    public function isCancellationFinal($order, $mspStatus) {
        $history = $order->getAllStatusHistory();

        foreach ($history as $status) {
            if (strpos($status->getComment(), $mspStatus) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get the current Magento version (as integer, 1.4.x.x => 14)
     */
    private function getMagentoVersion() {
        $version = Mage::getVersion();
        $arr = explode('.', $version);
        return $arr[0] . $arr[1];
    }

    /**
     *  Create invoice for order
     */
    protected function createInvoice(Mage_Sales_Model_Order $order) {

        if ($order->getState() == Mage_Sales_Model_Order::STATE_NEW) {
            try {
                if (!$order->canInvoice()) {
                    $order->addStatusHistoryComment('MultiSafepay: Order cannot be invoiced.', false);
                    $order->save();
                    return false;
                }

                //START Handle Invoice
                $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();
                $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
                $invoice->register();
                $invoice->getOrder()->setCustomerNoteNotify(false);
                $invoice->getOrder()->setIsInProcess(true);
                $order->addStatusHistoryComment('Automatically invoiced by MultiSafepay invoicer.', false);
                $transactionSave = Mage::getModel('core/resource_transaction')->addObject($invoice)->addObject($invoice->getOrder());
                $transactionSave->save();

                $payment = $order->getPayment();

                /*$transaction = $payment->getTransaction($this->mspDetails['ewallet']['id']);
                if (is_object($transaction)) {
                    $transaction->setAdditionalInformation(Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS, $this->transdetails);
                    $transaction->save();
                }*/

                if ($this->_config["updatetransaction"]) {
                    $invoiceId = $invoice->getIncrementId();

                    $msp = new MultiSafepay();
                    $msp->test = ($this->_config["test_api"] == 'test');
                    $msp->merchant['account_id'] = $this->_config["account_id"];
                    $msp->merchant['site_id'] = $this->_config["site_id"];
                    $msp->merchant['site_code'] = $this->_config["secure_code"];
                    $msp->transaction['id'] = $_GET['transactionid'];
                    $msp->transaction['invoice_id'] = $invoiceId;
                    $msp->updateInvoice();

                    if ($msp->error) {
                        echo 'update trans error';
                    }
                }

                //END Handle Invoice
                //Send Invoice emails
                $mail_invoice = $this->getConfigData("mail_invoice");
                $send_bno_invoice = $this->getConfigData("bno_no_invoice");
                $gateway = $order->getPayment()->getMethodInstance()->_gateway;


                if ($mail_invoice && $gateway != 'PAYAFTER' && $gateway != 'KLARNA' && $gateway != 'EINVOICE') {
                    $invoice->setEmailSent(true);
                    $invoice->sendEmail();
                    $invoice->save();
                } elseif (($gateway == 'PAYAFTER' || $gateway == 'KLARNA' || $gateway == 'EINVOICE') && $send_bno_invoice && $mail_invoice) {
                    $invoice->setEmailSent(true);
                    $invoice->sendEmail();
                    $invoice->save();
                }


                $order->setTotalPaid($order->getGrandTotal());
            } catch (Exception $e) {
                $order->addStatusHistoryComment('MultiSafepay invoicer: Exception occurred during the creation of the invoice. Exception message: ' . $e->getMessage(), false);
                $order->save();
            }
        }
        return false;
    }

    /**
     *  Get lock file
     */
    protected function _getLockFile() {
        if ($this->_lockFile === null) {
            $varDir = Mage::getConfig()->getVarDir('locks');
            $this->lockFilename = $varDir . DS . $this->_lockCode . '_' . $this->_lockId . '.lock';
            if (is_file($this->lockFilename)) {
                $this->_lockFile = fopen($this->lockFilename, 'w');
            } else {
                $this->_lockFile = fopen($this->lockFilename, 'x');
            }
            fwrite($this->_lockFile, date('r'));
        }
        return $this->_lockFile;
    }

    /**
     *  Set some lock vars
     */
    public function setLockId($id = null) {
        $this->_lockId = $id;
    }

    public function setLockCode($code = null) {
        $this->_lockCode = $code;
    }

    /**
     *  Create lock
     */
    public function lock() {
        $this->_isLocked = true;
        flock($this->_getLockFile($this->_lockId), LOCK_EX | LOCK_NB);

        return $this;
    }

    /**
     *  Prevent deletion of lockfile
     */
    public function preventLockDelete() {
        $this->_lockFile = null;
    }

    /**
     *  Unlock
     */
    public function unlock() {
        $this->_isLocked = false;
        flock($this->_getLockFile($this->_lockId), LOCK_UN);

        return $this;
    }

    /**
     *  Check if locked
     */
    public function isLocked() {
        if ($this->_isLocked !== null) {
            return $this->_isLocked;
        } else {
            $fp = $this->_getLockFile($this->_lockId);
            if (flock($fp, LOCK_EX | LOCK_NB)) {
                flock($fp, LOCK_UN);
                return false;
            }
            return true;
        }
    }

    /**
     *  Destroy lock file on destuct
     */
    public function __destruct() {
        if ($this->_lockFile) {
            fclose($this->_lockFile);
            unlink($this->lockFilename);
        }
    }

}
