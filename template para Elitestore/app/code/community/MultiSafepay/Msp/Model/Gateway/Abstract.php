<?php

/**
 *
 * @category MultiSafepay
 * @package  MultiSafepay_Msp
 */
require_once(Mage::getBaseDir('lib') . DS . 'multisafepay' . DS . 'MultiSafepay.combined.php');

abstract class MultiSafepay_Msp_Model_Gateway_Abstract extends Mage_Payment_Model_Method_Abstract {

    protected $_module = "msp"; // config root (msp or payment)
    protected $_settings = "msp"; // config root for settings (always msp for now)
    protected $_code;             // payment method code
    protected $_model;            // payment model
    public $_gateway;          // msp 'gateway'
    public $_idealissuer;
    protected $_params;
    protected $_loadSettingsConfig = true; // load 'settings' before payment config
    protected $_loadGatewayConfig = true;
    protected $_canCapture = true;
    protected $_canCapturePartial = true;
    protected $_canUseForMultishipping = false;
    protected $_canUseInternal = true;
    protected $_canRefund = true;
    protected $_canRefundInvoicePartial = true;
    public $payment;

    const MSP_GENERAL_CODE = 'msp';
    const MSP_FASTCHECKOUT_CODE = 'mspcheckout';
    const MSP_GENERAL_PAD_CODE = 'msp_payafter';
    const MSP_GENERAL_KLARNA_CODE = 'msp_klarna';
    const MSP_GATEWAYS_CODE_PREFIX = 'msp_';

    public $availablePaymentMethodCodes = array(
        'msp',
        'mspcheckout',
        'msp_ideal',
        'msp_payafter',
        'msp_klarna',
        'msp_mistercash',
        'msp_visa',
        'msp_mastercard',
        'msp_banktransfer',
        'msp_maestro',
        'msp_paypal',
        'msp_webgift',
        'msp_ebon',
        'msp_babygiftcard',
        'msp_boekenbon',
        'msp_erotiekbon',
        'msp_parfumnl',
        'msp_parfumcadeaukaart',
        'msp_degrotespeelgoedwinkel',
        'msp_giropay',
        'msp_multisafepay',
        'msp_directebanking',
        'msp_directdebit',
        'msp_fastcheckout',
        'msp_amex',
        'msp_yourgift',
        'msp_wijncadeau',
        'msp_lief',
        'msp_gezondheidsbon',
        'msp_fashioncheque',
    );

    public function __construct() {
        if ($this->_code == 'msp') {
            $currencies = explode(',', $this->getConfigData('allowed_currency'));
            $isAllowConvert = $this->getConfigData('allow_convert_currency');

            if ($isAllowConvert) {
                $this->_canUseCheckout = true;
            } else {
                if (in_array(Mage::app()->getStore()->getCurrentCurrencyCode(), $currencies)) {
                    $this->_canUseCheckout = true;
                } else {
                    $this->_canUseCheckout = false;
                }
            }
        } else {
            $currencies = explode(',', Mage::getStoreConfig('msp/' . $this->_code . '/allowed_currency'));
            $isAllowConvert = Mage::getStoreConfigFlag('msp/settings/allow_convert_currency');

            if ($isAllowConvert) {
                $this->_canUseCheckout = true;
            } else {
                if (in_array(Mage::app()->getStore()->getCurrentCurrencyCode(), $currencies)) {
                    $this->_canUseCheckout = true;
                } else {
                    $this->_canUseCheckout = false;
                }
            }
        }
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $customer = Mage::getSingleton('customer/session');
            $data = $customer->getCustomer();

            $group_id = $data->getGroupId();
            $specificgroups = explode(",", $this->getConfigData('specificgroups'));
            $selectedgroup = trim($this->getConfigData('specificgroups'));

            if (!in_array($group_id, $specificgroups) && $selectedgroup !== "") {
                $this->_canUseCheckout = false;
            }
        } else {
            if (trim($this->getConfigData('specificgroups')) !== "") {
                $this->_canUseCheckout = false;
            }
        }
    }

    // For 1.3.2.4
    //disabled, needs testing. Comment mentioned this was for 1.3.x.x and that version is no longer supported by this release
    /* public function isAvailable($quote = null) {
      return $this->getConfigData('active');
      } */

    public function setSortOrder($order) {
        // Magento tries to set the order from payment/, instead of our msp/
        $this->sort_order = $this->getConfigData('sort_order');
    }

    /**
     * Append the current model to the URL
     */
    public function getModelUrl($url) {
        if (!empty($this->_model)) {
            $url .= "/model/" . $this->_model;
        }

        return Mage::getUrl($url, array("_secure" => true));
    }

    /**
     * Magento will use this for payment redirection
     */
    public function getOrderPlaceRedirectUrl() {
        return $this->getModelUrl("msp/standard/redirect");
    }

    /**
     * Get the payment object and set the right config values
     */
    public function getPayment($storeId = null) {
        $payment = Mage::getSingleton("msp/payment");

        // get store id
        if (!$storeId) {
            //$storeId = $this->getStore();
            $storeId = Mage::app()->getStore()->getStoreId();
        }

        $orderId = Mage::app()->getRequest()->getQuery('transactionid');

        if ($orderId) {
            $storeId = Mage::getSingleton('sales/order')->loadByIncrementId($orderId)->getStoreId();
        }



        // basic settings
        $configSettings = array();
        if ($this->_loadSettingsConfig) {
            $configSettings = Mage::getStoreConfig($this->_settings . "/settings", $storeId);
        }

        // load gateway specific config and merge
        $configGateway = array();
        if ($this->_loadGatewayConfig) {
            $configGateway = Mage::getStoreConfig($this->_module . "/" . $this->_code, $storeId);
        }


        // merge
        $config = array_merge($configSettings, $configGateway);

        // payment
        $payment->setConfigObject($config);
        $payment->setNotificationUrl($this->getNotificationUrl());
        $payment->setReturnUrl($this->getReturnUrl());
        $payment->setCancelUrl($this->getCancelUrl());
        $payment->setGateway($this->getGateway());
        $payment->setIdealIssuer($this->getIdealIssuer());
        return $payment;
    }

    /**
     * Start fco xml transaction transaction
     */
    public function startPayAfterTransaction() {
        // pass store (from this getLastOrderId) to the getPayment?
        $payment = $this->getPayment();

        return $payment->startPayAfterTransaction();
    }

    /**
     * Start a transaction
     */
    public function startTransaction() {
        // pass store (from this getLastOrderId) to the getPayment?
        $payment = $this->getPayment();

        return $payment->startTransaction();
    }

    /**
     * Notification
     *
     * @param $id integer|string
     * @return mixed
     */
    public function notification($id) {
        /** @var $order Mage_Sales_Model_Order */
        $order = Mage::getSingleton('sales/order')->loadByIncrementId($id);

        /** @var $payment MultiSafepay_Msp_Model_Payment */
        $payment = $this->getPayment($order->getStore());

        return $payment->notification($id);
    }

    /**
     * @return mixed
     */
    public function getIdealIssuersHTML() {
        $storeId = Mage::app()->getStore()->getStoreId();
        $configSettings = array();
        if ($this->_loadSettingsConfig) {
            $configSettings = Mage::getStoreConfig($this->_settings . "/settings", $storeId);
        }

        //$idealselect = 'test';
        $msp = new MultiSafepay();

        if ($configSettings['test_api'] == 'test') {
            $msp->test = true;
        } else {
            $msp->test = false;
        }

        $msp->merchant['account_id'] = $configSettings['account_id'];
        $msp->merchant['site_id'] = $configSettings['site_id'];
        $msp->merchant['site_code'] = $configSettings['secure_code'];

        $iDealIssuers = $msp->getIdealIssuers();

        if ($configSettings['test_api'] == 'test') {
            return $iDealIssuers['issuers'];
        } else {
            return $iDealIssuers['issuers']['issuer'];
        }
    }

    /**
     * Notification URL of the model
     */
    public function getNotificationUrl() {
        return $this->getModelUrl("msp/standard/notification");
    }

    /**
     * Return URL of the model
     */
    public function getReturnUrl() {
        return Mage::getUrl("msp/standard/return", array("_secure" => true));
    }

    /**
     * Cancel URL of the model
     */
    public function getCancelUrl() {
        return Mage::getUrl("msp/standard/cancel", array("_secure" => true));
    }

    /**
     * Selected 'gateway'
     */
    public function getGateway() {
        return $this->_gateway;
    }

    public function getIdealIssuer() {
        return $this->_idealissuer;
    }

    /**
     * Pass params to the model
     */
    public function setParams($params) {
        $this->_params = $params;
    }

    /**
     * Get config data
     */
    public function getConfigData($field, $storeId = null) {
        if (null === $storeId) {
            //$storeId = Mage::app()->getStore()->getStoreId();//$this->getStore();
            $storeId = $this->getStore();
        }
        $path = $this->_module . "/" . $this->_code . '/' . $field;

        return Mage::getStoreConfig($path, $storeId);
    }

    public function refund(Varien_Object $payment, $amount) {
        $order = $payment->getOrder();






        $payment = $order->getPayment()->getMethodInstance();

        switch ($payment->getCode()) {
            // MSP - Fast Checkout
            case self::MSP_FASTCHECKOUT_CODE:
                $settingsPathPrefix = 'mspcheckout/settings';
                break;

            // General (Main settings in the 'Payment Methods' tab
            case self::MSP_GENERAL_CODE:
                $settingsPathPrefix = 'payment/msp';
                break;

            // MSP - Gateways (Pay After Delivery)
            case self::MSP_GENERAL_PAD_CODE:
                $settingsPathPrefix = 'msp/' . self::MSP_GENERAL_PAD_CODE;
                break;
            case self::MSP_GENERAL_KLARNA_CODE:
                $settingsPathPrefix = 'msp/' . self::MSP_GENERAL_KLARNA_CODE;
                break;

            // MSP - Gateways
            default:
                $settingsPathPrefix = 'msp/settings';
                break;
        }
        $config = Mage::getStoreConfig($settingsPathPrefix, $order->getStoreId());

        // use refund by Credit Memo is enabled
        $pathCreditMemoIsEnabled = (($payment->getCode() == self::MSP_GENERAL_PAD_CODE || $payment->getCode() == self::MSP_GENERAL_KLARNA_CODE)) ? 'msp/settings' : $settingsPathPrefix;
        if (!Mage::getStoreConfigFlag($pathCreditMemoIsEnabled . '/use_refund_credit_memo', $order->getStoreId())) {
            Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('msp')->__('Refund has not been send to MultiSafepay. You need to refund manually at MultiSafepay. Please check if the creditmemo option is configured within the MultiSafepay payment methods configuration!'));
            return $this;
        }

        // check payment method is from MultiSafepayment
        if (!in_array($payment->getCode(), $this->availablePaymentMethodCodes)) {
            Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('msp')->__('Refund has not been send to MultiSafepay. Looks like a non MultiSafepay payment method was selected'));
            return $this;
        }

        // build request
        $mapi = new MultiSafepay();
        $mapi->test = ($config['test_api'] == 'test');
        $mapi->merchant['account_id'] = $config['account_id'];
        $mapi->merchant['site_id'] = $config['site_id'];
        $mapi->merchant['site_code'] = $config['secure_code'];
        $mapi->merchant['api_key'] = $config['api_key'];
        $mapi->transaction['id'] = $order->getIncrementId();
        $mapi->transaction['amount'] = $amount * 100; //$order->getGrandTotal() * 100;
        $mapi->transaction['currency'] = Mage::app()->getStore()->getCurrentCurrencyCode();
        $mapi->signature = sha1($config['site_id'] . $config['secure_code'] . $mapi->transaction['id']);

        $response = $mapi->refundTransaction();


        if ($mapi->error) {
            Mage::getSingleton('adminhtml/session')->addError($mapi->error_code . ' - ' . $mapi->error);
            //return false;
        } else {
            Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('msp')->__('Refund request has been sent successfully to MultiSafepay, your transaction has been refunded.'));
        }
        return $this;
    }

}
