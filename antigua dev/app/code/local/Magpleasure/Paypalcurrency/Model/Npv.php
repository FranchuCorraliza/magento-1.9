<?php
/**
 * MagPleasure Ltd.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE-CE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magpleasure.com/LICENSE-CE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * MagPleasure does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Magpleasure does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   MagPleasure
 * @package    Magpleasure_Paypalcurrency
 * @version    1.0.1
 * @copyright  Copyright (c) 2012 MagPleasure Ltd. (http://www.magpleasure.com)
 * @license    http://www.magpleasure.com/LICENSE-CE.txt
 */

class Magpleasure_Paypalcurrency_Model_Npv extends Mage_Core_Model_Abstract
{

    const SENDBOX_API_USERNAME = 'platfo_1255077030_biz_api1.gmail.com';
    const SENDBOX_API_PASSWORD = '1255077037';
    const SENDBOX_API_SIGNATURE = 'Abg0gYcQyxQvnf2HDJkKtA-p6pqhA1k-KTYE0Gcy1diujFio4io5Vqjf';
    const SENDBOX_API_ENDPOINT = 'https://svcs.sandbox.paypal.com/';
    const SANDBOX_APPLICATION_ID = 'APP-80W284485P519543T';

    const DEVICE_IPADDRESS = '127.0.0.1';
    const REQUEST_FORMAT = 'NV';
    const RESPONSE_FORMAT = 'NV';
    const X_PAYPAL_REQUEST_SOURCE = 'PHP_NVP_SDK_V1.1';


    protected function _useSandBox()
    {
        return $this->_helper()->confPaypalSandBox();
    }

    /**
     * Helper
     *
     * @return Magpleasure_Paypalcurrency_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('paypalcurrency');
    }

    public function getEndPoint()
    {
        return $this->_useSandBox() ? 'https://svcs.sandbox.paypal.com/' : 'https://svcs.paypal.com/';
    }

    public function getApiUsername()
    {
        return $this->_useSandBox() ? self::SENDBOX_API_USERNAME : $this->_helper()->confPaypalUsername();
    }

    public function getApiPassword()
    {
        return $this->_useSandBox() ? self::SENDBOX_API_PASSWORD : $this->_helper()->confPaypalPassword();
    }

    public function getApiSignature()
    {
        return $this->_useSandBox() ? self::SENDBOX_API_SIGNATURE : $this->_helper()->confPaypalSignature();
    }

    public function getAppId()
    {
        return $this->_useSandBox() ? self::SANDBOX_APPLICATION_ID : $this->_helper()->confPaypalXComAppId();
    }

    public function callService($methodName, $nvpStr, $sandboxEmailAddress = '')
    {
        //declaring of global variables

        $URL= $this->getEndPoint().$methodName;
        //setting the curl parameters.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$URL);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);

        //turning off the server and peer verification(TrustManager Concept).
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POST, 1);

        //if USE_PROXY constant set to TRUE in Constants.php, then only proxy will be enabled.
        //Set proxy name to PROXY_HOST and port number to PROXY_PORT in constants.php
//        if(self::USE_PROXY){
//            curl_setopt ($ch, CURLOPT_PROXY, self::PROXY_HOST.":".self::PROXY_PORT);
//        }

        $headers_array = $this->setupHeaders();
//        if(!empty($sandboxEmailAddress)) {
//            $headers_array[] = "X-PAYPAL-SANDBOX-EMAIL-ADDRESS: ".$sandboxEmailAddress;
//        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers_array);
        curl_setopt($ch, CURLOPT_HEADER, false);
        //setting the nvpreq as POST FIELD to curl
        curl_setopt($ch,CURLOPT_POSTFIELDS,$nvpStr);


        //getting response from server
        $response = curl_exec($ch);
//
//        if (curl_errno($ch) == 60) {
//            curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/cacert.pem');
//            $response = curl_exec($ch);
//        }

        //convrting NVPResponse to an Associative Array
        $nvpResArray=$this->deformatNVP($response);
        //$nvpReqArray=deformatNVP($nvpreq);
        //$_SESSION['nvpReqArray']=$nvpReqArray;

        if (curl_errno($ch)) {
            // moving to display page to display curl errors
            $errno=curl_errno($ch) ;
            $message=curl_error($ch);

            Mage::throwException("{$errno}: {$message}");
        } else {
            //closing the curl
            curl_close($ch);
        }

        return $nvpResArray;
    }

    public function deformatNVP($nvpstr)
    {
        $intial=0;
        $nvpArray = array();

        while(strlen($nvpstr)){
            //postion of Key
            $keypos= strpos($nvpstr,'=');
            //position of value
            $valuepos = strpos($nvpstr,'&') ? strpos($nvpstr,'&'): strlen($nvpstr);

            /*getting the Key and Value values and storing in a Associative Array*/
            $keyval=substr($nvpstr,$intial,$keypos);
            $valval=substr($nvpstr,$keypos+1,$valuepos-$keypos-1);
            //decoding the respose
            $nvpArray[urldecode($keyval)] =urldecode( $valval);
            $nvpstr=substr($nvpstr,$valuepos+1,strlen($nvpstr));
        }
        return $nvpArray;
    }

    public function setupHeaders()
    {
        $headers_arr = array();
        $headers_arr[] = "X-PAYPAL-SECURITY-SIGNATURE: ".$this->getApiSignature();
        $headers_arr[] = "X-PAYPAL-SECURITY-USERID:  ".$this->getApiUsername();
        $headers_arr[] = "X-PAYPAL-SECURITY-PASSWORD: ".$this->getApiPassword();
        $headers_arr[] = "X-PAYPAL-APPLICATION-ID: ".$this->getAppId();
        $headers_arr[] = "X-PAYPAL-REQUEST-DATA-FORMAT: ".self::REQUEST_FORMAT;
        $headers_arr[] = "X-PAYPAL-RESPONSE-DATA-FORMAT: " .self::RESPONSE_FORMAT;
        $headers_arr[] = "X-PAYPAL-DEVICE-IPADDRESS: ".self::DEVICE_IPADDRESS;
        $headers_arr[] = "X-PAYPAL-REQUEST-SOURCE: ".self::X_PAYPAL_REQUEST_SOURCE;
        return $headers_arr;

    }

    
}