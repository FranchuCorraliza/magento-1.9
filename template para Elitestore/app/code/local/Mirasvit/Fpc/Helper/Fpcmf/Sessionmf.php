<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Full Page Cache
 * @version   1.0.32
 * @build     662
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_Fpc_Helper_Fpcmf_Sessionmf
{
    /**
     * @var string
     */
    protected $_prefix = 'FPC_';

    /**
     * @var array
     */
    protected static $_cache = array();

    /**
     * @var Mage_Core_Model_Resource_Store_Collection
     */
    protected static $_storeCollection = null;

    /**
     * @var Mirasvit_Fpc_Helper_Request
     */
    protected $_requestHelper;

    /**
     * @var Mirasvit_Fpc_Helper_Fpcmf_Datamf
     */
    protected $_dataHelper;

    /**
     * @var Mage_Core_Model_Config_Data
     */
    protected $_configData;

    public function __construct()
    {
        $this->_requestHelper = new Mirasvit_Fpc_Helper_Request();
        $this->_dataHelper = new Mirasvit_Fpc_Helper_Fpcmf_Datamf();
        $this->_configData = new Mage_Core_Model_Config_Data();
    }

    /**
     * @param string $key
     * @param string $data
     * @return void
     */
    public function set($key, $data)
    {
        if (is_array($data)) {
            $data = serialize($data);
        }

        $_SESSION[$this->_prefix.$key] = $data;

        return $this;
    }

    /**
     * @param string $key
     * @param string|bool $method
     * @return array
     */
    public function get($key, $method = false)
    {
        if (isset(self::$_cache[$key.$method])) {
            return self::$_cache[$key.$method];
        }

        $result = false;

        if (isset($_SESSION[$this->_prefix.$key]) && !$method) {
            $result = $_SESSION[$this->_prefix.$key];
        } elseif ($method) {
            $methodName = 'get'.ucfirst($key);
            if (method_exists($this, $methodName)) {
                $result = call_user_func(array($this, $methodName));
            }
        }

        if ($key == 'cart' && strpos($result, 'a:0:{') !== false) { //empty cart
            $result = '';
        }

        self::$_cache[$key.$method] = $result;

        return self::$_cache[$key.$method];
    }

    /**
     * @param string $key
     * @param string|bool $method
     * @return int
     */
    public function getCustomer($key, $method = false)
    {
        if (isset($_SESSION))
        {
            foreach ($_SESSION as $key => $section)
            {
                if (preg_match('/customer(_\w+)?/', $key))
                {
                    if (isset($section['customer_group_id']))
                        return $section['customer_group_id'];
                }
            }
        }

        return Mage_Customer_Model_Group::NOT_LOGGED_IN_ID;
    }

    /**
     * @return int
     */
    public function getCustomerGroupId()
    {
        return $this->getCustomer('customer_group_id');
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        $currencyCode =  false;

        if ($this->_requestHelper->isCrawler()) {
            $currencyPattern = '/' . Mirasvit_FpcCrawler_Model_Config::CURRENCY_BEGIN_LABEL . '.*?' . Mirasvit_FpcCrawler_Model_Config::CURRENCY_END_LABEL . '/';
            if (preg_match($currencyPattern, $this->_requestHelper->getUserAgent(), $currencyLabel) && isset($currencyLabel[0])) {
                $currencyReplaceLabel = array(Mirasvit_FpcCrawler_Model_Config::CURRENCY_BEGIN_LABEL, Mirasvit_FpcCrawler_Model_Config::CURRENCY_END_LABEL);
                $currencyCode = str_replace($currencyReplaceLabel, '', $currencyLabel[0]);
            }
        }

        if (!$currencyCode && $this->get('currency_code')) {
            $currencyCode = $this->get('currency_code');
        }

        if (!$currencyCode && isset($_COOKIE['currency'])) {
            $currencyCode = $_COOKIE['currency'];
        }

        if (!$currencyCode) {
            $currencyCode = $this->getCurrencyCode();;
        }

        return $currencyCode;
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        $storeId = false;

        if ($this->_requestHelper->isCrawler()) {
            $storePattern = '/' . Mirasvit_FpcCrawler_Model_Config::STORE_ID_BEGIN_LABEL . '\d+' . Mirasvit_FpcCrawler_Model_Config::STORE_ID_END_LABEL . '/';
            if (preg_match($storePattern, $this->_requestHelper->getUserAgent(), $storeIdLabel) && isset($storeIdLabel[0])) {
                $storeId = preg_replace('/[^0-9]/', '', $storeIdLabel[0]);
            }
        } else {
            $storeCode = false;
            $store = isset($_COOKIE['store']) ? $_COOKIE['store'] : false;
            if (isset($_GET['___store'])) {
                $store = $_GET['___store'];
            }

            if ($store != false) {
                $storeCode = $store;
            }

            if(!$storeCode && $this->get('store_code')) {
                $storeCode =  trim($this->get('store_code'));
            }

            if($storeCode) {
                $storeId = Mage::getModel('core/store')->load($storeCode, 'code')->getId();
            }

            if(!$storeId) {
                $storeId = $this->getCurrentStoreId();
            }
        }

        return $storeId;
    }

    /**
     * @return string
     */
    protected function getCurrencyCode() {
        $currency = false;
        $storeId = $this->getStoreId();
        $store = array();

        $storeCollection = $this->getStoreCollection();

        foreach ($storeCollection as $storeData) {
            $store[$storeData['store_id']] = $storeData['website_id'];
        }

        $websiteId = $store[$storeId];

        $data = $this->_configData->getCollection()->addFieldToFilter('path','currency/options/allow');

        $currencyData = $data->getData();
        $defaultCurrency = false;
        $storeCurrency = false;
        $websiteCurrency = false;
        foreach ($currencyData as $currencyConfig) {
            if ($currencyConfig['scope'] == 'default') {
                $defaultCurrency = $currencyConfig['value'];
            } elseif ($currencyConfig['scope'] == 'stores'
                && $currencyConfig['scope_id'] == $storeId) {
                     $storeCurrency = $currencyConfig['value'];
            } elseif ($currencyConfig['scope'] == 'websites'
                && $currencyConfig['scope_id'] == $websiteId) {
                     $websiteCurrency = $currencyConfig['value'];
            }
        }

        if ($storeCurrency) {
            $currency = $storeCurrency;
        } elseif($websiteCurrency) {
            $currency = $websiteCurrency;
        } elseif($defaultCurrency) {
            $currency = $defaultCurrency;
        }

        return $currency;
    }

    /**
     * @return int
     */
    protected function getCurrentStoreId() {
        $storeId = false;
        $data = $this->_configData->getCollection()->addFieldToFilter('path','web/unsecure/base_url');

        $urlData = $data->getData();

        foreach ($urlData as $site) {
            if (strpos($site['value'], $_SERVER['HTTP_HOST']) !== false) {
                $siteData =  $site;
                break;
            }
        }

        $storeCollection = $this->getStoreCollection();

        $website = array();
        foreach ($storeCollection as $store) {
            $website[$store['website_id']] = $store['store_id'];
            if ($store['code'] == 'default') {
                $website['default'] = $store['store_id'];
            }
        }

        if ($siteData && $siteData['scope'] == 'stores' && $siteData['scope_id'] != 0) {
            $storeId = $siteData['scope_id'];
        } elseif ($siteData && $siteData['scope'] == 'websites' && $siteData['scope_id'] != 0) {
            $storeId = $website[$siteData['scope_id']];
        } elseif ($siteData && $siteData['scope'] == 'default') {
            $storeId = $website['default'];
        }

        return $storeId;
    }

    /**
     * @return Mage_Core_Model_Resource_Store_Collection
     */
    protected function getStoreCollection() {
        if (self::$_storeCollection === null) {
            $storeCore = new Mage_Core_Model_Store;
            self::$_storeCollection = $storeCore->getCollection();
        }

        return self::$_storeCollection;
    }

    /**
     * @return int
     */
    public function getLocale()
    {
        return $this->getStoreId();
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->_dataHelper->getNormlizedUrl(true);
    }

    /**
     * @return bool
     */
    public function getIsCustomerLoggedIn()
    {
        return $this->getCustomerId() ? true : false;
    }

    /**
     * @return int
     */
    public function getCustomerId()
    {
        return $this->get('customer_id');
    }

    /**
     * @return string
     */
    public function getCart()
    {
        return $this->get('cart');
    }

    /**
     * @return string
     */
    public function getCatalogCompare()
    {
        return $this->get('catalog_compare');
    }

    /**
     * @return string
     */
    public function getWishlist()
    {
        return $this->get('wishlist');
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->get('message');
    }

    /**
     * @return bool
     */
    public function getIsHome()
    {
        if ($_SERVER['REQUEST_URI'] == '/' || $_SERVER['REQUEST_URI'] == '/index.php') {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function getAllowSaveCookies()
    {
        return false;
    }

    public function getCategory()
    {
    }

    public function getProduct()
    {
    }

    /**
     * @return string
     */
    public static function getFormKey()
    {
        if (!isset($_SESSION['core']['_form_key']) || !$_SESSION['core']['_form_key']) {
            $coreHelper = new Mage_Core_Helper_Data();
            $_SESSION['core']['_form_key'] = $coreHelper->getRandomString(16);
        }

        return $_SESSION['core']['_form_key'];
    }
}
