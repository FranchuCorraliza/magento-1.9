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
    protected $_formBlockType = 'msp/default';
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
    public $_configCode = 'msp';

    const MSP_GENERAL_CODE = 'msp';
    const MSP_FASTCHECKOUT_CODE = 'mspcheckout';
    const MSP_GENERAL_PAD_CODE = 'msp_payafter';
    const MSP_GENERAL_KLARNA_CODE = 'msp_klarna';
    const MSP_GENERAL_EINVOICE_CODE = 'msp_einvoice';
    const MSP_GATEWAYS_CODE_PREFIX = 'msp_';

    public $availablePaymentMethodCodes = array(
        'msp',
        'mspcheckout',
        'msp_ideal',
        'msp_creditcard',
        'msp_dotpay',
        'msp_payafter',
        'msp_einvoice',
        'msp_klarna',
        'msp_mistercash',
        'msp_visa',
        'msp_eps',
        'msp_ferbuy',
        'msp_mastercard',
        'msp_banktransfer',
        'msp_maestro',
        'msp_paypal',
        'msp_webgift',
        'msp_ebon',
        'msp_babygiftcard',
        'msp_podium',
        'msp_vvvgiftcard',
        'msp_sportenfit',
        'msp_beautyandwellness',
        'msp_boekenbon',
        'msp_erotiekbon',
        'msp_giveacard',
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
        'msp_fashiongiftcard',
    );
    public $giftcards = array(
        'msp_webgift',
        'msp_ebon',
        'msp_babygiftcard',
        'msp_boekenbon',
        'msp_erotiekbon',
        'msp_giveacard',
        'msp_parfumnl',
        'msp_parfumcadeaukaart',
        'msp_degrotespeelgoedwinkel',
        'msp_yourgift',
        'msp_wijncadeau',
        'msp_lief',
        'msp_gezondheidsbon',
        'msp_fashioncheque',
        'msp_fashiongiftcard',
        'msp_podium',
        'msp_vvvgiftcard',
        'msp_sportenfit',
        'msp_beautyandwellness',
    );
    public $gateways = array(
        'msp_ideal',
        'msp_creditcard',
        'msp_dotpay',
        'msp_payafter',
        'msp_einvoice',
        'msp_klarna',
        'msp_mistercash',
        'msp_visa',
        'msp_eps',
        'msp_ferbuy',
        'msp_mastercard',
        'msp_banktransfer',
        'msp_maestro',
        'msp_paypal',
        'msp_giropay',
        'msp_multisafepay',
        'msp_directebanking',
        'msp_directdebit',
        'msp_amex',
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
            $isAllowConvert = false;
            $currencies = array();
            if (in_array($this->_code, $this->gateways)) {
                $this->_configCode = 'msp_gateways';
                $this->_module = 'msp_gateways';
                $currencies = explode(',', Mage::getStoreConfig('msp_gateways/' . $this->_code . '/allowed_currency'));
                $isAllowConvert = Mage::getStoreConfigFlag('msp/settings/allow_convert_currency');
            } elseif (in_array($this->_code, $this->giftcards)) {
                $this->_configCode = 'msp_giftcards';
                $this->_module = 'msp_giftcards';
                $currencies = explode(',', Mage::getStoreConfig('msp_giftcards/' . $this->_code . '/allowed_currency'));
                $isAllowConvert = Mage::getStoreConfigFlag('msp/settings/allow_convert_currency');
            }


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



        $group_id = 0; // If not logged in, customer group id is 0
        if (Mage::getSingleton('customer/session')->isLoggedIn()) { // If logged in, set customer group id
            $group_id = Mage::getSingleton('customer/session')->getCustomer()->getGroupId();
        }
        $option = trim(Mage::getStoreConfig($this->_configCode . '/' . $this->_code . '/specificgroups'));
        $specificgroups = explode(",", $option);
        // If customer group is not in available groups and config option is not empty, disable this gateway
        if (!in_array($group_id, $specificgroups) && $option !== "") {
            $this->_canUseCheckout = false;
        }
    }

    // For 1.3.2.4
    //disabled, needs testing. Comment mentioned this was for 1.3.x.x and that version is no longer supported by this release
    /* public function isAvailable($quote = null) {
      return $this->getConfigData('active');
      } */

    public function setSortOrder($order) {
        // Magento tries to set the order from payment/, instead of our msp/

        $this->sort_order = Mage::getStoreConfig($this->_configCode . '/' . $this->_code . '/sort_order');
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
       
	    $data = Mage::app()->getRequest()->getPost('creditmemo');
	    
	    
	    if(isset($data['servicecost'])){
	    	$refunded_servicecost = $data['servicecost'];
			if ($refunded_servicecost != $order->getServicecost()) {
		       	$amount = $amount - $order->getServicecost() + $refunded_servicecost;
		   	}
	    }


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
                $settingsPathPrefix = 'msp_gateways/' . self::MSP_GENERAL_PAD_CODE;
                break;
            case self::MSP_GENERAL_KLARNA_CODE:
                $settingsPathPrefix = 'msp_gateways/' . self::MSP_GENERAL_KLARNA_CODE;
                break;
            case self::MSP_GENERAL_EINVOICE_CODE:
                $settingsPathPrefix = 'msp_gateways/' . self::MSP_GENERAL_EINVOICE_CODE;
                break;

            // MSP - Gateways
            default:
                $settingsPathPrefix = 'msp/settings';
                break;
        }

        $config = Mage::getStoreConfig($settingsPathPrefix, $order->getStoreId());

        // use refund by Credit Memo is enabled
        $pathCreditMemoIsEnabled = (($payment->getCode() == self::MSP_GENERAL_PAD_CODE || $payment->getCode() == self::MSP_GENERAL_KLARNA_CODE || $payment->getCode() == self::MSP_GENERAL_EINVOICE_CODE)) ? 'msp/settings' : $settingsPathPrefix;
        if (!Mage::getStoreConfigFlag($pathCreditMemoIsEnabled . '/use_refund_credit_memo', $order->getStoreId())) {
            Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('msp')->__('Refund has not been send to MultiSafepay. You need to refund manually at MultiSafepay. Please check if the creditmemo option is configured within the MultiSafepay payment methods configuration!'));
            return $this;
        }

        // check payment method is from MultiSafepayment
        if (!in_array($payment->getCode(), $this->availablePaymentMethodCodes)) {
            Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('msp')->__('Refund has not been send to MultiSafepay. Looks like a non MultiSafepay payment method was selected'));
            return $this;
        }


        //get account data from correct gateway settings for pad klarna and coupons.
        if (isset($config['test_api_pad'])) {
            if ($config['test_api_pad'] == 'test') {
                $config['test_api'] = 'test';
                $config['account_id'] = $config['account_id_pad_test'];
                $config['site_id'] = $config['site_id_pad_test'];
                $config['secure_code'] = $config['secure_code_pad_test'];
                $config['api_key'] = $config['api_key_pad_test'];
            } else {
                $config['account_id'] = $config['account_id_pad'];
                $config['site_id'] = $config['site_id_pad'];
                $config['secure_code'] = $config['secure_code_pad'];
                $config['api_key'] = $config['api_key_pad'];
            }
        }
        
        //This is a PAD/Klarna/Einvoice refund so we need to update the checkout data. We will be using the JSON API instead
        if($payment->getCode() == self::MSP_GENERAL_PAD_CODE || $payment->getCode() == self::MSP_GENERAL_KLARNA_CODE || $payment->getCode() == self::MSP_GENERAL_EINVOICE_CODE){
	        if($config['test_api'] == 'test'){
		    	$mspurl = 'https://testapi.multisafepay.com/v1/json/';
			}else{
                    $mspurl = 'https://api.multisafepay.com/v1/json/';
            }	        
	    
	        	require_once dirname(__FILE__) . "/../Api/Client.php";
	        
	        	$msp = new Client;
	        	$msp->setApiKey($config['api_key']);
                $msp->setApiUrl($mspurl);
	        	$msporder = $msp->orders->get($type = 'orders', $order->getIncrementId(), $body = array(), $query_string = false);
	        	
	        	
	        	$originalCart = $msporder->shopping_cart;
                
                $refundData = array();
                //$refundData['checkout_data']['items'];
                
             
                foreach ($originalCart->items as $key => $item) {
                    if ($item->unit_price > 0) {
                        $refundData['checkout_data']['items'][] = $item;
                    }
                    
                    foreach ($order->getCreditmemosCollection() as $creditmemo) {
	                    foreach($creditmemo->getAllItems() as $product){
		                   
	                        $product_id = $product->getData('order_item_id');
	                        
	                        if ($product_id == $item->merchant_item_id) {
	                            $qty_refunded = $product->getData('qty');
	                            if ($qty_refunded > 0) {
	                                if ($item->unit_price > 0) {
	                                    $refundItem = (OBJECT) Array();
	                                    $refundItem->name = $item->name;
	                                    $refundItem->description = $item->description;
	                                    $refundItem->unit_price = '-' . $item->unit_price;
	                                    $refundItem->quantity = round($qty_refunded);
	                                    $refundItem->merchant_item_id = $item->merchant_item_id;
	                                    $refundItem->tax_table_selector = $item->tax_table_selector;
	                                    $refundData['checkout_data']['items'][] = $refundItem;
	                                }
	                            }
	                        }
	                    }
                    }
                    
                    foreach($data['items'] as $productid => $proddata){
	                    if($item->merchant_item_id == $productid){
		                    if($proddata['qty'] > 0){
			                    $refundItem = (OBJECT) Array();
		                        $refundItem->name = $item->name;
		                        $refundItem->description = $item->description;
		                        $refundItem->unit_price = '-' . $item->unit_price;
		                        $refundItem->quantity = round($proddata['qty']);
		                        $refundItem->merchant_item_id = $item->merchant_item_id;
		                        $refundItem->tax_table_selector = $item->tax_table_selector;
		                        $refundData['checkout_data']['items'][] = $refundItem;
		                    }
	                    }
                    }
                    
                    //The complete shipping cost is refunded also so we can remove it from the checkout data and refund it
                    if($item->merchant_item_id == 'msp-shipping'){
	                    if($data['shipping_amount'] == $order->getShippingAmount()){
		                    $refundItem = (OBJECT) Array();
			                $refundItem->name = $item->name;
			                $refundItem->description = $item->description;
			                $refundItem->unit_price = '-' . $item->unit_price;
			                $refundItem->quantity = '1';
			                $refundItem->merchant_item_id = $item->merchant_item_id;
			                $refundItem->tax_table_selector = $item->tax_table_selector;
			                $refundData['checkout_data']['items'][] = $refundItem;
		                }else{
			                if($data['shipping_amount'] != 0){
				                Mage::getSingleton('adminhtml/session')->addError('MultiSafepay: Refund not processed online as it did not match the complete shipping cost');
			                	$order->addStatusHistoryComment('MultiSafepay: Refund not processed online as it did not match the complete shipping cost', false);
			                	$order->save();
				                return $this;
				            }
		                }
		            }
		            if($item->name == $order->getShippingDescription() && $item->unit_price <0){
			            $refundItem = (OBJECT) Array();
			                $refundItem->name = $item->name;
			                $refundItem->description = $item->description;
			                $refundItem->unit_price = $item->unit_price;
			                $refundItem->quantity = '1';
			                $refundItem->merchant_item_id = $item->merchant_item_id;
			                $refundItem->tax_table_selector = $item->tax_table_selector;
			                $refundData['checkout_data']['items'][] = $refundItem;
		            }
                }
                
                
                

				//print_r($originalCart);
				//print_r($refundData);exit;

                $endpoint = 'orders/' . $order->getIncrementId() . '/refunds';
                try {
                    $mspreturn = $msp->orders->post($refundData, $endpoint);
                    Mage::log($mspreturn, null, 'MultiSafepay-Refunds.log');
                    Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('msp')->__('Refund request has been sent successfully to MultiSafepay, your transaction has been refunded.'));
                } catch (Exception $e) {
                    Mage::log($mspreturn, null, 'MultiSafepay-Refunds.log');
                   
					Mage::getSingleton('adminhtml/session')->addError('Online processing of the refund failed, reason: ' . $e->getMessage());
					$order->addStatusHistoryComment('Online processing of the refund failed, reason: ' . $e->getMessage(), false);
			        $order->save();
                }
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

        Mage::log($mapi, null, 'MultiSafepay-Refunds.log');
              
        $response = $mapi->refundTransaction();
        
        Mage::log($response, null, 'MultiSafepay-Refunds.log');

        if ($mapi->error) {
            Mage::getSingleton('adminhtml/session')->addError($mapi->error_code . ' - ' . $mapi->error);
            //return false;
        } else {
            Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('msp')->__('Refund request has been sent successfully to MultiSafepay, your transaction has been refunded.'));
        }
        return $this;
    }

}
