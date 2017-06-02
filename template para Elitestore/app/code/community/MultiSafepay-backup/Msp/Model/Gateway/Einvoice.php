<?php

/**
 *
 * @category MultiSafepay
 * @package  MultiSafepay_Msp
 */
class MultiSafepay_Msp_Model_Gateway_Einvoice extends MultiSafepay_Msp_Model_Gateway_Abstract {

    protected $_code = "msp_einvoice";
    public $_model = "einvoice";
    public $_gateway = "EINVOICE";
    // protected $_formBlockType = 'msp/einvoice';
    protected $_canUseCheckout = true;
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
        $availableByIP = true;

        if (Mage::getStoreConfig('msp_gateways/msp_einvoice/ip_check')) {
            if ($this->_isTestMode()) {
                $data = Mage::getStoreConfig('msp_gateways/msp_einvoice/ip_filter_test');
            } else {
                $data = Mage::getStoreConfig('msp_gateways/msp_einvoice/ip_filter');
            }

            if (!in_array($_SERVER["REMOTE_ADDR"], explode(';', $data))) {
                $availableByIP = false;
            }
        }
        
        
        



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
            $availableByCurrency = true;
        } else {
            if (in_array(Mage::app()->getStore()->getCurrentCurrencyCode(), $currencies)) {
                $availableByCurrency = true;
            } else {
                $availableByCurrency = false;
            }
        }
        $isavailablebygroup= true;
        $group_id = 0; // If not logged in, customer group id is 0
        if (Mage::getSingleton('customer/session')->isLoggedIn()) { // If logged in, set customer group id
            $group_id = Mage::getSingleton('customer/session')->getCustomer()->getGroupId();
        }
        $option = trim(Mage::getStoreConfig($this->_configCode . '/' . $this->_code . '/specificgroups'));
        $specificgroups = explode(",", $option);
        // If customer group is not in available groups and config option is not empty, disable this gateway
        if (!in_array($group_id, $specificgroups) && $option !== "") {
            $isavailablebygroup = false;
        }

        $this->_canUseCheckout = $availableByIP && $availableByCurrency && $isavailablebygroup;
    }

    public function getOrderPlaceRedirectUrl() {
        if (isset($_POST['payment']['birthday'])) {
            $birthday = $_POST['payment']['birthday'];
        } else {
            $birthday = '';
        }

        if (isset($_POST['payment']['accountnumber'])) {
            $accountnumber = $_POST['payment']['accountnumber'];
        } else {
            $accountnumber = '';
        }


        if (isset($_POST['payment']['phonenumber'])) {
            $phonenumber = $_POST['payment']['phonenumber'];
        } else {
            $phonenumber = '';
        }

        $url = $this->getModelUrl("msp/standard/redirect/issuer/" . $this->_issuer);
        if (!strpos($url, "?"))
            $url .= '?birthday=' . $birthday . '&accountnumber=' . $accountnumber . '&phonenumber=' . $phonenumber;
        else
            $url .= '&birthday=' . $birthday . '&accountnumber=' . $accountnumber . '&phonenumber=' . $phonenumber;
        return $url;
    }

    /**
     * Is Test Mode
     *
     * @param null|integer|Mage_Core_Model_Store $store
     * @return bool
     */
    protected function _isTestMode($store = null) {
        $mode = Mage::getStoreConfig('msp_gateways/msp_einvoice/test_api_pad', $store);

        return $mode == MultiSafepay_Msp_Model_Config_Sources_Accounts::TEST_MODE;
    }

}
