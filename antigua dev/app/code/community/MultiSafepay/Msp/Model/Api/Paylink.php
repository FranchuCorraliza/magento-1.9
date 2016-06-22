<?php

/**
 *
 * @category MultiSafepay
 * @package  MultiSafepay_Msp
 */
require_once(Mage::getBaseDir('lib') . DS . 'multisafepay' . DS . 'MultiSafepay.combined.php');

class MultiSafepay_Msp_Model_Api_Paylink {

    public $test = false;
    public $custom_api;
    public $extrapublics = '';
    public $use_shipping_xml;
    public $use_shipping_notification = false;
    // merchant data
    public $merchant = array(
        'account_id' => '',
        'site_id' => '',
        'api_key' => '',
        'security_code' => '',
    );
    // transaction data
    public $transaction = array(
        'id' => '',
        'currency' => '',
        'amount' => '',
        'days_active' => '',
    );
    public $signature;
    public $api_url;
    public $request_xml;
    public $reply_xml;
    public $payment_url;
    public $status;
    public $error_code;
    public $error;
    public $debug;
    public $parsed_xml;
    public $parsed_root;
    protected $_logFileName = 'msp_paylink.log';
    public $availablePaymentMethodCodes = array(
        'msp' => '',
        'msp_ideal' => 'IDEAL',
        //'msp_payafter', for now we dont allow payafter manual transaction requests
        'msp_mistercash' => 'MISTERCASH',
        'msp_visa' => 'VISA',
        'msp_mastercard' => 'MASTERCARD',
        'msp_banktransfer' => 'BANKTRANS',
        'msp_maestro' => 'MAESTRO',
        'msp_paypal' => 'PAYPAL',
        'msp_webgift' => 'WEBSHOPGIFTCARD',
        'msp_ebon' => 'EBON',
        'msp_babygiftcard' => 'BABYGIFTCARD',
        'msp_boekenbon' => 'BOEKENBON',
        'msp_erotiekbon' => 'EROTIEKBON',
        'msp_parfumnl' => 'PARFUMNL',
        'msp_parfumcadeaukaart' => 'PARFUMCADEAUKAART',
        'msp_degrotespeelgoedwinkel' => 'DEGROTESPEELGOEDWINKEL',
        'msp_giropay' => 'GIROPAY',
        'msp_multisafepay' => 'WALLET',
        'msp_directebanking' => 'DIRECTBANK',
        'msp_directdebit' => 'DIRDEB',
        'msp_amex',
        'msp_yourgift',
        'msp_wijncadeau',
        'msp_lief',
        'msp_gezondheidsbon',
        'msp_fashioncheque',
    );

    /**
     * Starts a transaction and returns the payment url
     *
     * @return string
     */
    public function getPaymentLink($order) {
        $this->log('Request payment link for manual order');

        $this->checkSettings();
        $this->createSignature();

        $payment = $order->getPayment()->getMethodInstance();
        $pm_code = $payment->getCode();
        $storename = Mage::app()->getStore()->getName();
        $billing = $order->getBillingAddress();
        $shipping = $order->getShippingAddress();

        $items = "<ul>\n";
        foreach ($order->getAllVisibleItems() as $item) {
            $items .= "<li>" . ($item->getQtyOrdered() * 1) . " x : " . $item->getName() . "</li>\n";
        }
        $items .= "</ul>\n";


        // build request
        $mapi = new MultiSafepay();
        $mapi->plugin_name = 'Magento';
        $mapi->version = Mage::getConfig()->getNode('modules/MultiSafepay_Msp/version');
        $mapi->use_shipping_notification = false;
        $mapi->merchant['account_id'] = $this->merchant['account_id'];
        $mapi->merchant['site_id'] = $this->merchant['site_id'];
        $mapi->merchant['site_code'] = $this->merchant['security_code'];

        $mapi->test = $this->test;
        $mapi->merchant['notification_url'] = Mage::getUrl("msp/standard/notification") . '&type=initial';
        $mapi->merchant['cancel_url'] = Mage::getUrl("msp/standard/cancel", array("_secure" => true));
        $mapi->merchant['redirect_url'] = Mage::getUrl("msp/standard/return", array("_secure" => true));

        $mapi->parseCustomerAddress($billing->getStreet(1));

        if ($mapi->customer['housenumber'] == '') {
            $mapi->customer['housenumber'] = $billing->getStreet(2);
            $mapi->customer['address1'] = $billing->getStreet(1);
        }

        $mapi->customer['locale'] = Mage::app()->getLocale()->getLocaleCode(); //Mage::app()->getLocale()->getDefaultLocale();
        $mapi->customer['firstname'] = $billing->getFirstname();
        $mapi->customer['lastname'] = $billing->getLastname();
        $mapi->customer['zipcode'] = $billing->getPostcode();
        $mapi->customer['city'] = $billing->getCity();
        $mapi->customer['state'] = $billing->getState();
        $mapi->customer['country'] = $billing->getCountry();
        $mapi->customer['phone'] = $billing->getTelephone();
        $mapi->customer['email'] = $order->getCustomerEmail();
        $mapi->customer['ipaddress'] = $_SERVER['REMOTE_ADDR'];
        $mapi->transaction['id'] = $this->transaction['id'];
        $mapi->transaction['amount'] = $this->transaction['amount'];
        $mapi->transaction['currency'] = $this->transaction['currency'];
        $mapi->transaction['var3'] = Mage::app()->getStore()->getStoreId();
        $mapi->transaction['description'] = 'Order #' . $this->transaction['id'] . ' at ' . $storename;
        $mapi->transaction['gateway'] = $this->availablePaymentMethodCodes[$pm_code];
        $mapi->transaction['items'] = $items;
        $mapi->transaction['daysactive'] = $this->transaction['days_active'];

        $url = $mapi->startTransaction();

        if ($mapi->error) {
            return array(
                'error' => true,
                'code' => $mapi->error_code,
                'description' => $mapi->error
            );
        }

        return array('error' => false, 'url' => $url);
    }

    /**
     * Check the settings before using them
     *
     * @return void
     */
    public function checkSettings() {
        $this->merchant['account_id'] = trim($this->merchant['account_id']);
        $this->merchant['site_id'] = trim($this->merchant['site_id']);
        $this->merchant['api_key'] = trim($this->merchant['api_key']);
        $this->merchant['security_code'] = trim($this->merchant['security_code']);
    }

    /**
     * Creates the signature
     *
     * @return void
     */
    public function createSignature() {
        $this->signature = sha1(
                $this->merchant['site_id'] .
                $this->merchant['security_code'] .
                $this->transaction['id']
        );
    }

    /**
     * Returns the api url
     *
     * @return string
     */
    public function getApiUrl() {
        if ($this->custom_api) {
            return $this->custom_api;
        }

        if ($this->test) {
            return "https://testapi.multisafepay.com/ewx/";
        } else {
            return "https://api.multisafepay.com/ewx/";
        }
    }

    /**
     * Check if a certain MultiSafepay status is already in the order history (to prevent doubles)
     */
    public function isPaymentLinkCreated($order) {
        $history = $order->getAllStatusHistory();
        foreach ($history as $status) {
            if (strpos($status->getComment(), 'Manual Payment link') !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Logging functions
     *
     * @return mixed
     */
    public function isDebug() {
        return $this->getConfigData('debug');
    }

    /**
     * @return void
     */
    public function log() {
        $argv = func_get_args();
        $data = array_shift($argv);

        if (is_string($data)) {
            $logData = @vsprintf($data, $argv);

            // if vsprintf failed, just use the data
            if (!$logData) {
                $logData = $data;
            }
        } else {
            $logData = $data;
        }

        if ($this->debug) {
            Mage::log($logData, null, $this->_logFileName);
        }
    }

}
