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



abstract class Mirasvit_Fpc_Model_Container_Abstract
{
    const CONTAINER_ID_PREFIX = 'FPC_CONTAINER';
    const HTML_NAME_PATTERN = '/<\[!--\{(.*?)\}--\]>/i';
    const EMPTY_VALUE = 'empty';

    protected $_definition = null;
    protected static $_layoutXml = null;
    protected $_hash = null;

    //dependences values
    protected static $_customer = null;
    protected static $_customerGroup = null;
    protected static $_loggedIn = null;
    protected static $_cart = null;
    protected static $_compare = null;
    protected static $_wishlist = null;
    protected static $_product = null;
    protected static $_category = null;
    protected static $_store = null;
    protected static $_currency = null;
    protected static $_locale = null;
    protected static $_isHome = null;
    protected static $_allowSaveCookies = null;
    protected static $_demoNotice = null;
    protected static $_owCookieNotice = null;
    protected static $_amastyPreorder = null;
    protected static $_secure = null;
    protected static $_packageName = null;
    protected static $_cms = null;
    protected static $_globalDependences = null;

    public function __construct($definition, $block)
    {
        $this->_definition = $definition;

        $this->_definition['block_name'] = $block->getNameInLayout();

        $this->_definition['layout'] = Mage::helper('fpc/layout')->generateBlockLayoutXML($block->getNameInLayout());

        return $this;
    }

    public function getDefinition()
    {
        return $this->_definition;
    }

    public function getBlockReplacerHtml($html)
    {
        return $this->_getStartReplacerTag().$html.$this->_getEndReplacerTag();
    }

    protected function _getStartReplacerTag()
    {
        return '<[!--{'.$this->getDefinitionHash().'}--]>';
    }

    protected function _getEndReplacerTag()
    {
        return '<[!--/{'.$this->getDefinitionHash().'}--]>';
    }

    public function getDefinitionHash()
    {
        if ($this->_hash == null) {
            $this->_hash = $this->_definition['block'].'_'.($this->_definition['block_name']);
        }

        return $this->_hash;
    }

    public function saveToCache($content)
    {
        $pattern = '/'.preg_quote($this->_getStartReplacerTag(), '/').'(.*?)'.preg_quote($this->_getEndReplacerTag(), '/').'/ims';
        ini_set('pcre.backtrack_limit', 100000000);

        $matches = array();
        preg_match($pattern, $content, $matches);
        if (isset($matches[1])) {
            $this->saveCache($matches[1]);
        }

        return $this;
    }

    protected function isWhitoutBlockUpdate($withoutBlockUpdate)
    {
        $blocks = array('page/html_welcome', 'wishlist/customer_sidebar', 'page/template_links');
        $result = false;
        if ($withoutBlockUpdate && !$this->getCart() && !$this->getLoggedIn()
            && (in_array($this->_definition['block'], $blocks) || $this->isCartBlock()) ) {
                $result =  true;
        } elseif ($withoutBlockUpdate && !$this->getCart()
            && $this->getLoggedIn() && $this->isCartBlock()) {
                $result =  true;
        }

        return $result;
    }

    protected function isCartBlock()
    {
        if (strpos($this->_definition['block'], 'checkout') !== false
            || strpos($this->_definition['block'], 'cart') !== false) {
                return true;
        }

        return false;
    }

    public function applyToContent(&$content, $withoutBlockUpdate = false)
    {
        Mage::helper('fpc/debug')->startTimer('FPC_BLOCK_' . $this->getDefinitionHash());

        if ($this->isWhitoutBlockUpdate($withoutBlockUpdate)) {
            return true;
        }

        $pattern = '/'.preg_quote($this->_getStartReplacerTag(), '/').'(.*?)'.preg_quote($this->_getEndReplacerTag(), '/').'/ims';
        $html = $this->getBlockHtml();

        if ($html !== false) {
            ini_set('pcre.backtrack_limit', 100000000);
            $replaceCount = 1;
            if ($this->isCartBlock()) { // cart can be added more than one time
                $replaceCount = 3;
            }
            $content = preg_replace($pattern, str_replace('$', '\\$', $html), $content, $replaceCount);

            $result = true;
        } else {
            $result = false;
        }
        Mage::helper('fpc/debug')->stopTimer('FPC_BLOCK_' . $this->getDefinitionHash());

        return $result;
    }

    public function getBlockHtml()
    {
		$startTime = microtime(true);
        $fromCache = 1;
        $html = $this->loadCache();
		if (!$this->inApp() && !trim($this->_definition['depends'])) {
			try {
                $html = Mage::helper('fpc/layout')->renderBlock($this->_definition);
                $fromCache = 0;
            } catch (Exception $e) {
                $html = $this->loadCache();
            }
        }

        if ($html) {
		    Mage::helper('fpc/debug')->appendDebugInformationToBlock($html, $this, $fromCache, $startTime);
        } else {
            if ($this->inApp()) {
		        return false;
            } else {
		        $html = Mage::helper('fpc/layout')->renderBlock($this->_definition);
                $this->saveCache($html);
            }
        }
		if ($html == self::EMPTY_VALUE) {
		    $html = '';
        }
		return $html;
    }

    public function inApp()
    {
        return isset($this->_definition['in_app']) && $this->_definition['in_app'] == true;
    }

    public function inRegister()
    {
        if (isset($this->_definition['in_app']) && $this->_definition['in_register']) {
            return $this->_definition['in_register'];
        }

        return false;
    }

    public function inSession()
    {
        return isset($this->_definition['in_session']) && $this->_definition['in_session'] == true;
    }

    protected function _getCacheId()
    {
        if ($identifier = $this->_getIdentifier()) {
            return self::CONTAINER_ID_PREFIX.'_'.md5($this->getDefinitionHash().($identifier));
        }

        return false;
    }

    protected function _getIdentifier()
    {
        return false;
    }

    public function getCacheId()
    {
        return $this->_getCacheId();
    }

    public function saveCache($blockContent)
    {
        $cacheId = $this->_getCacheId();
        if ($cacheId !== false) {
            if ($this->inSession() && Mage::helper('fpc')->getSessionSize()
                && Mage::helper('fpc')->getSessionSize() < Mirasvit_Fpc_Model_Config::MAX_SESSION_SIZE) {
                    $this->_saveSessionCache($blockContent, $cacheId);
            } else {
                $this->_saveCache($blockContent, $cacheId);
            }
        }

        return $this;
    }

    public function loadCache()
    {
        $id = $this->_getCacheId();
        if ($this->inSession() && ($cacheHtml = $this->_getSessionCache($id))) {
            return $cacheHtml;
        }

        // echo 'loadCache: ' . $this->_definition['block'] . "_________" . $id . "<br/>";
        // echo 'loadCache: ' . $this->getDefinitionHash() . "_________" . $this->_getIdentifier() . "<br/>";

        return Mirasvit_Fpc_Model_Cache::getCacheInstance()->load($id);
    }

    protected function _saveCache($data, $id, $tags = array(), $lifetime = null)
    {
        $tags[] = Mirasvit_Fpc_Model_Processor::CACHE_TAG;
        if (is_null($lifetime)) {
            $lifetime = $this->_definition['cache_lifetime'] ?
                $this->_definition['cache_lifetime'] : false;
        }

        if (!$lifetime) {
            $lifetime = Mage::getSingleton('fpc/config')->getLifetime();
        }

        if ($data == '') {
            $data = ' ';
        }

        Mirasvit_Fpc_Model_Cache::getCacheInstance()->save($data, $id, $tags, $lifetime);

        return $this;
    }

     protected function _getSessionCache($id) {
        if ($cache = Mage::getSingleton('core/session')->getData($id)) {
            $html = $cache->getHtml();
            $html = preg_replace('/\/uenc\/(.*?)\//ims', '/uenc/' . Mage::helper('core/url')->getEncodedUrl() . '/', $html); //need for cart

            // echo '_getSessionCache: ' . $this->_definition['block'] . "_________" . $id . "<br/>";
            // echo '_getSessionCache: ' . $this->getDefinitionHash() . "_________" . $this->_getIdentifier() . "<br/>";

            return $html;
        }

        return false;
    }

    protected function _saveSessionCache($data, $id) {
        $blockObject = new Varien_Object();
        $blockObject->html = $data;
        Mage::getSingleton('core/session')->setData($id, $blockObject);

        return true;
    }

    public function getDependenceHash($dependences)
    {
        $hash = array();

        if (!is_array($dependences)) {
            $dependences = explode(',', $dependences);
        }

        foreach ($dependences as $dependence) {
            $hash[] = $dependence;
            switch ($dependence) {
                case 'customer':
                    $hash[] = $this->getCustomer();
                    break;

                case 'customer_group':
                    $hash[] = $this->getCustomerGroup();
                    break;

                case 'logged_in':
                    $hash[] = (string) $this->getLoggedIn();
                    break;

                case 'cart':
                    $hash[] = $this->getCart();
                    break;

                case 'compare':
                    $hash[] = $this->getCompare();
                    break;

                case 'wishlist':
                    $hash[] = $this->getWishlist();
                    break;

                case 'product':
                    $hash[] = $this->getProduct();
                    break;

                case 'category':
                    $hash[] = $this->getCategory();
                    break;

                case 'store':
                    $hash[] = $this->getStore();
                    break;

                case 'currency':
                    $hash[] = $this->getCurrency();
                    break;

                case 'locale':
                    $hash[] = $this->getLocale();
                    break;

                case 'rotator':
                    $hash[] = 'rotator_'.rand(0, 5);
                    break;

                case 'is_home':
                    $hash[] = $this->isHome();
                    break;

                case 'allow_save_cookies':
                    $hash[] = $this->getAllowSaveCookies();
                    break;

                case 'demo_notice':
                    $hash[] = $this->getDemoNotice();
                    break;

                case 'ow_cookie_notice':
                    $hash[] = $this->getOwCookieNotice();
                    break;

                case 'get':
                    $hash[] = implode('', $_GET);
                    break;

                case 'amasty_preorder':
                    $hash[] = $this->getAmastyPreorder();
                    break;

                case 'secure':
                    $hash[] = $this->getSecure();
                    break;

                case 'package_name':
                    $hash[] = $this->getPackageName();
                    break;

                case 'cms':
                    $hash[] = $this->getCms();
                    break;

                default:
                    break;
            }
        }

        $hash[] = $this->getGlobalDependences();

        return implode(' | ', $hash);
    }

    public function getConfig()
    {
        return Mage::getSingleton('fpc/config');
    }

    /**
     * @param string $dependenceName
     * @return string
     */
    protected function _prepareDependenceName($dependenceName)
    {
        return str_replace('get', '', $dependenceName);
    }

    /**
     * @return int
     */
    protected function getCustomer()
    {
        if (self::$_customer === null) {
            Mage::helper('fpc/debug')->startTimer('FPC_DEPENDENCES_' . $this->_prepareDependenceName(__FUNCTION__));
            $customer = Mage::getSingleton('customer/session');
            self::$_customer = $customer->getCustomerId();
            Mage::helper('fpc/debug')->stopTimer('FPC_DEPENDENCES_' . $this->_prepareDependenceName(__FUNCTION__));
        }

        return self::$_customer;
    }

    /**
     * @return int
     */
    protected function getCustomerGroup()
    {
        if (self::$_customerGroup === null) {
            Mage::helper('fpc/debug')->startTimer('FPC_DEPENDENCES_' . $this->_prepareDependenceName(__FUNCTION__));
            $customer = Mage::getSingleton('customer/session');
            self::$_customerGroup = $customer->getCustomerGroupId();
            Mage::helper('fpc/debug')->stopTimer('FPC_DEPENDENCES_' . $this->_prepareDependenceName(__FUNCTION__));
        }

        return self::$_customerGroup;
    }

    /**
     * @return int
     */
    protected function getLoggedIn()
    {
        if (self::$_loggedIn === null) {
            Mage::helper('fpc/debug')->startTimer('FPC_DEPENDENCES_' . $this->_prepareDependenceName(__FUNCTION__));
            self::$_loggedIn = Mage::getSingleton('customer/session')->isLoggedIn();
            if (preg_match('/FpcCrawlerlogged/', Mage::helper('core/http')->getHttpUserAgent())) { //need for logged in user Magecrawler
                self::$_loggedIn = 1;
            }
            Mage::helper('fpc/debug')->stopTimer('FPC_DEPENDENCES_' . $this->_prepareDependenceName(__FUNCTION__));
        }

        return self::$_loggedIn;
    }

    /**
     * @return string
     */
    protected function getCart()
    {
        if (self::$_cart === null) {
            Mage::helper('fpc/debug')->startTimer('FPC_DEPENDENCES_' . $this->_prepareDependenceName(__FUNCTION__));
            $checkout = Mage::getSingleton('checkout/session');
            $cartItemsCollection = $checkout->getQuote()->getItemsCollection();
            if ($cartItemsCollection->getSize() > 0) {
                Mage::getSingleton('core/resource_iterator')->walk(
                    $cartItemsCollection->getSelect(),
                    array(array($this, 'callbackValidateCartItem'))
                );
            }
            Mage::helper('fpc/debug')->stopTimer('FPC_DEPENDENCES_' . $this->_prepareDependenceName(__FUNCTION__));
        }

        return self::$_cart;
    }

    public function callbackValidateCartItem($args)
    {
        if (isset($args['row']['item_id'])) {
            self::$_cart .= $args['row']['item_id'] . '/';
        }
        if (isset($args['row']['qty'])) {
            self::$_cart .= $args['row']['qty'];
        }
    }


    /**
     * @return string
     */
    protected function getCompare()
    {
        if (self::$_compare === null) {
            Mage::helper('fpc/debug')->startTimer('FPC_DEPENDENCES_' . $this->_prepareDependenceName(__FUNCTION__));
            $count = Mage::helper('catalog/product_compare')->getItemCount();
            if ($count > 0) {
                $items = Mage::helper('catalog/product_compare')->getItemCollection();
                foreach ($items as $item) {
                    self::$_compare .= $item->getId();
                }
            } else {
                self::$_compare = $count;
            }
            Mage::helper('fpc/debug')->stopTimer('FPC_DEPENDENCES_' . $this->_prepareDependenceName(__FUNCTION__));
        }

        return self::$_compare;
    }

    /**
     * @return string
     */
    protected function getWishlist()
    {
        if (self::$_wishlist === null) {
            Mage::helper('fpc/debug')->startTimer('FPC_DEPENDENCES_' . $this->_prepareDependenceName(__FUNCTION__));
            $wishlistHelper = Mage::helper('wishlist');

            if ($wishlistHelper->hasItems()) {
                $items = $wishlistHelper->getItemCollection();
                foreach ($items as $item) {
                    self::$_wishlist .= $item->getId();
                }
            }
            Mage::helper('fpc/debug')->stopTimer('FPC_DEPENDENCES_' . $this->_prepareDependenceName(__FUNCTION__));
        }

        return self::$_wishlist;
    }

    /**
     * @return mixed
     */
    protected function getProduct()
    {
        if (self::$_product === null && Mage::registry('current_product_id')) {
            Mage::helper('fpc/debug')->startTimer('FPC_DEPENDENCES_' . $this->_prepareDependenceName(__FUNCTION__));
            self::$_product = Mage::registry('current_product_id');
            Mage::helper('fpc/debug')->stopTimer('FPC_DEPENDENCES_' . $this->_prepareDependenceName(__FUNCTION__));
        } elseif (self::$_product === null && Mage::registry('current_product')) {
            Mage::helper('fpc/debug')->startTimer('FPC_DEPENDENCES_' . $this->_prepareDependenceName(__FUNCTION__));
            self::$_product = Mage::registry('current_product')->getId();
            Mage::helper('fpc/debug')->stopTimer('FPC_DEPENDENCES_' . $this->_prepareDependenceName(__FUNCTION__));
        }

        return self::$_product;
    }

    /**
     * @return mixed
     */
    protected function getCategory()
    {
        if (self::$_category === null && Mage::registry('current_category_id')) {
            Mage::helper('fpc/debug')->startTimer('FPC_DEPENDENCES_' . $this->_prepareDependenceName(__FUNCTION__));
            self::$_category = Mage::registry('current_category_id');
            Mage::helper('fpc/debug')->stopTimer('FPC_DEPENDENCES_' . $this->_prepareDependenceName(__FUNCTION__));
        } elseif (self::$_category === null && Mage::registry('current_category')) {
            Mage::helper('fpc/debug')->startTimer('FPC_DEPENDENCES_' . $this->_prepareDependenceName(__FUNCTION__));
            self::$_category = Mage::registry('current_category')->getId();
            Mage::helper('fpc/debug')->stopTimer('FPC_DEPENDENCES_' . $this->_prepareDependenceName(__FUNCTION__));
        }

        return self::$_category;
    }

    /**
     * @return string
     */
    protected function getStore()
    {
        if (self::$_store === null) {
            Mage::helper('fpc/debug')->startTimer('FPC_DEPENDENCES_' . $this->_prepareDependenceName(__FUNCTION__));
            self::$_store = Mage::app()->getStore()->getCode();
            Mage::helper('fpc/debug')->stopTimer('FPC_DEPENDENCES_' . $this->_prepareDependenceName(__FUNCTION__));
        }

        return self::$_store;
    }

    /**
     * @return string
     */
    protected function getCurrency()
    {
        if (self::$_currency === null) {
            Mage::helper('fpc/debug')->startTimer('FPC_DEPENDENCES_' . $this->_prepareDependenceName(__FUNCTION__));
            self::$_currency = Mage::app()->getStore()->getCurrentCurrencyCode();
            Mage::helper('fpc/debug')->stopTimer('FPC_DEPENDENCES_' . $this->_prepareDependenceName(__FUNCTION__));
        }

        return self::$_currency;
    }

    /**
     * @return string
     */
    protected function getLocale()
    {
        if (self::$_locale === null) {
            Mage::helper('fpc/debug')->startTimer('FPC_DEPENDENCES_' . $this->_prepareDependenceName(__FUNCTION__));
            self::$_locale = Mage::app()->getLocale()->getLocaleCode();
            Mage::helper('fpc/debug')->stopTimer('FPC_DEPENDENCES_' . $this->_prepareDependenceName(__FUNCTION__));
        }

        return self::$_locale;
    }

    /**
     * @return string
     */
    protected function isHome()
    {
        if (self::$_isHome === null) {
            Mage::helper('fpc/debug')->startTimer('FPC_DEPENDENCES_' . $this->_prepareDependenceName(__FUNCTION__));
            if (Mage::getBlockSingleton('page/html_header')->getIsHomePage()) {
                self::$_isHome = 'home';
            }
            Mage::helper('fpc/debug')->stopTimer('FPC_DEPENDENCES_' . $this->_prepareDependenceName(__FUNCTION__));
        }

        return self::$_isHome;
    }

    /**
     * @return string
     */
    protected function getAllowSaveCookies()
    {
        if (self::$_allowSaveCookies === null) {
            Mage::helper('fpc/debug')->startTimer('FPC_DEPENDENCES_' . $this->_prepareDependenceName(__FUNCTION__));
            if(version_compare(Mage::getVersion(), '1.7.0.1', '>=')) {
                self::$_allowSaveCookies = Mage::helper('core/cookie')->isUserNotAllowSaveCookie();
            }
            Mage::helper('fpc/debug')->stopTimer('FPC_DEPENDENCES_' . $this->_prepareDependenceName(__FUNCTION__));
        }

        return self::$_allowSaveCookies;
    }

    /**
     * @return string
     */
    protected function getDemoNotice()
    {
        if (self::$_demoNotice === null) {
            Mage::helper('fpc/debug')->startTimer('FPC_DEPENDENCES_' . $this->_prepareDependenceName(__FUNCTION__));
            self::$_demoNotice = Mage::getStoreConfig('design/head/demonotice');
            Mage::helper('fpc/debug')->stopTimer('FPC_DEPENDENCES_' . $this->_prepareDependenceName(__FUNCTION__));
        }

        return self::$_demoNotice;
    }

    /**
     * @return string
     */
    protected function getOwCookieNotice()
    {
        if (self::$_owCookieNotice === null) {
            Mage::helper('fpc/debug')->startTimer('FPC_DEPENDENCES_' . $this->_prepareDependenceName(__FUNCTION__));
            if (isset($_COOKIE) && isset($_COOKIE['ow_cookie_notice'])) {
                self::$_owCookieNotice = $_COOKIE['ow_cookie_notice'];
            }
            Mage::helper('fpc/debug')->stopTimer('FPC_DEPENDENCES_' . $this->_prepareDependenceName(__FUNCTION__));
        }

        return self::$_owCookieNotice;
    }

    /**
     * @return string
     */
    protected function getAmastyPreorder()
    {
        if (self::$_amastyPreorder === null) {
            Mage::helper('fpc/debug')->startTimer('FPC_DEPENDENCES_' . $this->_prepareDependenceName(__FUNCTION__));
            $preorder = false;
            if (Mage::registry('current_product')) {
                $product = Mage::registry('current_product');
            } elseif (Mage::registry('current_product_id')) {
                $product = Mage::getModel('catalog/product')->load(Mage::registry('current_product_id'));
            }
            if ($product) {
                $preorder = Mage::helper('ampreorder')->getIsProductPreorder($product);
            }
            $currentUrl = Mage::helper('core/url')->getCurrentUrl();
            $currentUrl = strtok($currentUrl, '?');
            if ($preorder) {
                self::$_amastyPreorder = $product->getId().$currentUrl;
            } else {
                self::$_amastyPreorder = $currentUrl;
            }
            Mage::helper('fpc/debug')->stopTimer('FPC_DEPENDENCES_' . $this->_prepareDependenceName(__FUNCTION__));
        }

        return self::$_amastyPreorder;
    }

    /**
     * @return string
     */
    protected function getSecure()
    {
        if (self::$_secure === null) {
            Mage::helper('fpc/debug')->startTimer('FPC_DEPENDENCES_' . $this->_prepareDependenceName(__FUNCTION__));
            self::$_secure = Mage::app()->getStore()->isCurrentlySecure();
            Mage::helper('fpc/debug')->stopTimer('FPC_DEPENDENCES_' . $this->_prepareDependenceName(__FUNCTION__));
        }

        return self::$_secure;
    }

    /**
     * @return string
     */
    protected function getPackageName()
    {
        if (self::$_packageName === null) {
            Mage::helper('fpc/debug')->startTimer('FPC_DEPENDENCES_' . $this->_prepareDependenceName(__FUNCTION__));
            self::$_packageName = Mage::getSingleton('core/design_package')->getPackageName();
            Mage::helper('fpc/debug')->stopTimer('FPC_DEPENDENCES_' . $this->_prepareDependenceName(__FUNCTION__));
        }

        return self::$_packageName;
    }

    /**
     * @return string
     */
    protected function getCms()
    {
        if (self::$_cms === null) {
            Mage::helper('fpc/debug')->startTimer('FPC_DEPENDENCES_' . $this->_prepareDependenceName(__FUNCTION__));
            $request = Mage::app()->getRequest();
            $action = $request->getModuleName().'/'.$request->getControllerName().'_'.$request->getActionName();
            if ($action == 'cms/page_view'
                && ($cmsPage = $request->getParams('page_id'))
                && isset($cmsPage['page_id'])) {
                    self::$_cms = $cmsPage['page_id'];
            }
            Mage::helper('fpc/debug')->stopTimer('FPC_DEPENDENCES_' . $this->_prepareDependenceName(__FUNCTION__));
        }

        return self::$_cms;
    }

    /**
     * @return string
     */
    protected function getGlobalDependences()
    {
        if (self::$_globalDependences === null) {
            Mage::helper('fpc/debug')->startTimer('FPC_DEPENDENCES_' . $this->_prepareDependenceName(__FUNCTION__));
            $hash = array();
            foreach ($this->getConfig()->getUserAgentSegmentation() as $segment) {
                if ($segment['useragent_regexp']
                    && preg_match($segment['useragent_regexp'], Mage::helper('core/http')->getHttpUserAgent())) {
                        $hash[] = $segment['cache_group'];
                }
            }

            if (Mage::helper('mstcore')->isModuleInstalled('AW_Mobile2')
                && Mage::helper('aw_mobile2')->isCanShowMobileVersion()) {
                $hash[] = 'awMobileGroup';
            }

            if ($deviceType = Mage::helper('fpc/mobile')->getMobileDeviceType()) {
                $hash[] = $deviceType;
            }

            $hash[] = Mage::app()->getStore()->isCurrentlySecure();
            // if (Mage::getBlockSingleton('page/html_header')->getIsHomePage()) {
            //     $hash[] = 'home';
            // }
            $hash[] = Mage::app()->getStore()->getCode();
            $hash[] = $this->getCurrency();
            $hash[] = $this->getLocale();
            $hash[] = $this->getCustomDependences();
            $hash[] = Mage::getSingleton('core/design_package')->getTheme('frontend');
            $hash[] = Mage::getDesign()->getTheme('layout');

            self::$_globalDependences = implode(' | ', $hash);
            Mage::helper('fpc/debug')->stopTimer('FPC_DEPENDENCES_' . $this->_prepareDependenceName(__FUNCTION__));
        }

        return self::$_globalDependences;
    }

    /**
     * Get data from class Mirasvit_Fpc_Helper_CustomDependence public function getCustomDependence() if class exist
     * @return string
     */
    protected function getCustomDependences()
    {
        $customDependences = '';
        Mage::helper('fpc/debug')->startTimer('FPC_DEPENDENCES_' . $this->_prepareDependenceName(__FUNCTION__));

        $custom = Mage::helper('fpc/custom')->getCustomSettings();
        if ($custom && in_array('getCustomDependence', $custom)) {
            $customDependences = Mage::helper('fpc/customDependence')->getCustomDependence();
        }

        Mage::helper('fpc/debug')->stopTimer('FPC_DEPENDENCES_' . $this->_prepareDependenceName(__FUNCTION__));

        return $customDependences;
    }
}
