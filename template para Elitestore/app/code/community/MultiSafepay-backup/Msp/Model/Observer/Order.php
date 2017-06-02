<?php

/**
 *
 * @category MultiSafepay
 * @package  MultiSafepay_Msp
 */
class MultiSafepay_Msp_Model_Observer_Order extends MultiSafepay_Msp_Model_Observer_Abstract {

    const MSP_GENERAL_CODE = 'msp';
    const MSP_FASTCHECKOUT_CODE = 'mspcheckout';
    const MSP_GENERAL_PAD_CODE = 'msp_payafter';
    const MSP_GENERAL_EINVOICE_CODE = 'msp_einvoice';
    const MSP_GENERAL_KLARNA_CODE = 'msp_klarna';
    const MSP_GATEWAYS_CODE_PREFIX = 'msp_';

    public $availablePaymentMethodCodes = array(
        'msp',
        //'mspcheckout', dont allow fco
        'msp_ideal',
        'msp_creditcard',
        'msp_dotpay',
        //'msp_payafter', for now we dont allow payafter manual transaction requests
        //'msp_einvoice', for now we dont allow payafter manual transaction requests
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
        'msp_yourgift',
        'msp_wijncadeau',
        'msp_lief',
        'msp_amex',
        'msp_paypal',
        'msp_gezondheidsbon',
        'msp_fashioncheque',
        'msp_fashiongiftcard',
        'msp_podium',
        'msp_vvvgiftcard',
        'msp_sportenfit',
        'msp_beautyandwellness',
    );

    public function sales_order_place_after(Varien_Event_Observer $observer) {

        if (!Mage::app()->getStore()->isAdmin()) {
            return $this;
        }
        /** @var $event Varien_Event */
        $event = $observer->getEvent();

        $orderId = $observer->getEvent()->getOrder()->getId();


        /** @var $order Mage_Sales_Model_Order */
        $order = $observer->getEvent()->getOrder();

		if($order->getEditIncrement()){
		    return true;
	    }


        /** @var $payment Mage_Payment_Model_Method_Abstract */
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
            case self::MSP_GENERAL_EINVOICE_CODE:
                $settingsPathPrefix = 'msp/' . self::MSP_GENERAL_EINVOICE_CODE;
                break;
            // MSP - Gateways
            default:
                $settingsPathPrefix = 'msp/settings';
                break;
        }

        if (!in_array($payment->getCode(), $this->availablePaymentMethodCodes)) {
            return $this;
        }

        // check order's payment method  is enabled now
        if (!in_array($payment->getCode(), $this->_getAllActivePaymentMethods($order->getStoreId()))) {
            return $this;
        }

        $baseGrandTotal = floatval($order->getBaseGrandTotal());

        $config = Mage::getStoreConfig($settingsPathPrefix, $order->getStoreId());

        $api = Mage::getModel('msp/api_paylink');
        $configMain = Mage::getStoreConfig('msp/settings', $order->getStoreId());
        
        if(!$config['paylink_create']){
	        return $this;
        }
        
        if (!$api->isPaymentLinkCreated($order)) {
            if ($payment->getCode() == self::MSP_GENERAL_PAD_CODE || $payment->getCode() == self::MSP_GENERAL_KLARNA_CODE || $payment->getCode() == self::MSP_GENERAL_EINVOICE_CODE) {

			



                $api->test = ($config['test_api_pad'] == 'test');
                $suffix = '';

                if ($api->test) {
                    $suffix = '_test';
                }

                $api->merchant['account_id'] = $config['account_id_pad' . $suffix];
                $api->merchant['site_id'] = $config['site_id_pad' . $suffix];
                $api->merchant['security_code'] = $config['secure_code_pad' . $suffix];
                $api->merchant['api_key'] = $configMain['api_key'];
                $api->transaction['id'] = $configMain['daysactive'];
                $api->debug = $configMain['debug'];
            } else {
                $api->test = ($config['test_api'] == 'test');
                $api->merchant['account_id'] = $config['account_id'];
                $api->merchant['site_id'] = $config['site_id'];
                $api->merchant['security_code'] = $config['secure_code'];
                $api->merchant['api_key'] = $config['api_key'];
                $api->transaction['id'] = $configMain['daysactive'];
                $api->debug = $config['debug'];
            }
            $api->transaction['gateway_reset'] = $configMain['gateway_reset'];
            if ($payment->getCode() == self::MSP_FASTCHECKOUT_CODE) {
                $api->transaction['id'] = $order->getQuoteId();
            } else {
                $api->transaction['id'] = $order->getIncrementId();
            }

            $api->transaction['amount'] = $order->getGrandTotal() * 100;
            $currency = $order->getOrderCurrency();
            $currencyCode = $currency->getCurrencyCode();


            $api->transaction['currency'] = $currencyCode;



            $response = $api->getPaymentLink($order);

            Mage::getSingleton('core/session')->getMessages(true);


            if ($response['error']) {
                Mage::getSingleton('adminhtml/session')->addError($response['code'] . ' - ' . $response['description'] . ' - Order has been created but no paymenlink could be generated. Please generate a payment link manualy within your MultiSafepay account');
            } else {
                Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('msp')->__('MultiSafepay transaction created, the payment link is added to the comment section'));
                $order->addStatusToHistory($order->getStatus(), Mage::helper("msp")->__("Manual Payment link:") . '<br/>' . $response['url']);
            }
        }
        return $this;
    }

}
