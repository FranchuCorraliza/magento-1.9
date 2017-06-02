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



class Mirasvit_Fpc_Model_Processor
{
    const CACHE_TAG = 'FPC';
    const TAGS_THRESHOLD = 100;

    protected $_requestCacheId = null;
    protected $_requestTags = array();
    protected $_canProcessRequest = null;
    protected $_containers = array();
    protected $_isServed = false;
    protected $_currentProduct = false;

    protected $_catalogMessage = false;
    protected $_checkoutMessage = false;

    protected $_storage = null;

    static protected $_isPageFound = null;

    /**
     * @var Mirasvit_Fpc_Helper_Request
     */
    protected $_requestHelper;

    /**
     * @var Mirasvit_Fpc_Helper_Debug
     */
    protected $_debugHelper;

    /**
     * @var Mirasvit_Fpc_Helper_Response
     */
    protected $_responseHelper;

    /**
     * @var Mirasvit_Fpc_Model_Config
     */
    protected $_config;

    /**
     * @var bool|array
     */
    protected $_custom;

    public function __construct()
    {
        $_SERVER['FPC_TIME'] = microtime(true);

        $this->_requestHelper = Mage::helper('fpc/request');
        $this->_responseHelper = Mage::helper('fpc/response');
        $this->_debugHelper = Mage::helper('fpc/debug');
        $this->_requestcacheidHelper = Mage::helper('fpc/processor_requestcacheid');
        $this->_cartexcludeHelper = Mage::helper('fpc/processor_cartexclude');
        $this->_canprocessrequestHelper = Mage::helper('fpc/processor_canprocessrequest');
        $this->_messageHelper = Mage::helper('fpc/message');
        $this->_config = Mage::getSingleton('fpc/config');
        $this->_custom = Mage::helper('fpc/custom')->getCustomSettings();

        $this->addRequestTag(self::CACHE_TAG);
    }

    /**
     * Observer for event `http_response_send_before`
     * @return $this
     */
    public function prepareHtml()
    {
        if (!$this->canProcessRequest(Mage::app()->getRequest())
            && !$this->_requestHelper->isRedirect()
            && !Mage::app()->getRequest()->isXmlHttpRequest()
            && ($response = Mage::app()->getResponse())
        ) {
            $content = $response->getBody();
            $this->_responseHelper->cleanExtraMarkup($content, false);
            $this->_debugHelper->appendDebugInformation($content, 2);
            $response->setBody($content);
        }

        return $this;
    }


    protected function switchCustomerGroup()
    {
        $userAgent = $this->_requestHelper->getUserAgent();
        $loggedUserPattern = '/FpcCrawlerlogged' . Mirasvit_FpcCrawler_Model_Config::USER_AGENT_BEGIN_LABEL . '\d+' . Mirasvit_FpcCrawler_Model_Config::USER_AGENT_END_LABEL . '/';
        $userSession = Mage::getSingleton('customer/session');
        if (preg_match($loggedUserPattern, $userAgent, $userAgentCustomerGroup) && isset($userAgentCustomerGroup[0])) {
            $customerGroupId = preg_replace('/[^0-9]/', '', $userAgentCustomerGroup[0]);
            if (Mage::getSingleton('fpccrawler/config')->isAllowedGroup($customerGroupId)) {
                $collection = Mage::getResourceModel('customer/customer_collection')
                    ->addAttributeToSelect('*')
                    ->addFieldToFilter('group_id', $customerGroupId);
                //Mage_Customer_Model_Resource_Customer _getDefaultAttributes do not have is_active
                $collection->getSelect()->where("is_active = 1")->limit(1);
                $customer = $collection->getFirstItem();
                $userSession->setCustomerGroupId($customerGroupId);
                $userSession->setId($customer->getId());
            }
        } elseif ($this->_requestHelper->isCrawler()) {
            if ($userSession->isLoggedIn()) {
                $userSession->logout();
            }
            $userSession->setCustomerGroupId(0); //NOT LOGGED IN group
        }

        //use in /Mirasvit/Fpc/Model/Observer.php registerModelTag
        if (!Mage::registry('m_fpc_logged_in_info_added')) {
            Mage::register('m_fpc_logged_in_info_added', true);
        }
    }

    protected function switchStore()
    {
        if (!$this->_requestHelper->isCrawler()) {
            return false;
        }

        $storePattern = '/' . Mirasvit_FpcCrawler_Model_Config::STORE_ID_BEGIN_LABEL . '\d+' . Mirasvit_FpcCrawler_Model_Config::STORE_ID_END_LABEL . '/';
        if (preg_match($storePattern, $this->_requestHelper->getUserAgent(), $storeIdLabel) && isset($storeIdLabel[0])) {
            $storeId = preg_replace('/[^0-9]/', '', $storeIdLabel[0]);
            Mage::app()->setCurrentStore($storeId);
            Mage::app()->getLocale()->setLocaleCode(Mage::getStoreConfig('general/locale/code', $storeId));
        }
    }

    protected function switchCurrency()
    {
        if (!$this->_requestHelper->isCrawler()) {
            return false;
        }

        $currencyPattern = '/' . Mirasvit_FpcCrawler_Model_Config::CURRENCY_BEGIN_LABEL . '.*?' . Mirasvit_FpcCrawler_Model_Config::CURRENCY_END_LABEL . '/';
        if (preg_match($currencyPattern, $this->_requestHelper->getUserAgent(), $currencyLabel) && isset($currencyLabel[0])) {
            $currencyReplaceLabel = array(Mirasvit_FpcCrawler_Model_Config::CURRENCY_BEGIN_LABEL, Mirasvit_FpcCrawler_Model_Config::CURRENCY_END_LABEL);
            $currency = str_replace($currencyReplaceLabel, '', $currencyLabel[0]);
            Mage::app()->getStore()->setCurrentCurrencyCode($currency);
        }
    }

    /**
     * Observer for event `controller_action_predispatch`
     *
     * @return bool
     */
    public function serveResponse()
    {
        if ($this->_custom && in_array('beforeServeResponse', $this->_custom)) {
            Mage::helper('fpc/customDependence')->beforeServeResponse();
        }

        $this->_debugHelper->startTimer('SELF_TIME');

        $this->_debugHelper->startTimer('SWITCH_FOR_CRAWLER_TIME');
        $this->switchCustomerGroup();
        $this->switchStore();
        $this->switchCurrency();
        $this->_debugHelper->stopTimer('SWITCH_FOR_CRAWLER_TIME');

        if (!$this->canProcessRequest(Mage::app()->getRequest())) {
            return false;
        }

        $cacheId = $this->getRequestCacheId();

        $this->_storage = Mage::getModel('fpc/storage');
        $this->_storage->setCacheId($cacheId);
        if ($this->_storage->load()) {
            if ($this->_custom && in_array('getWithoutBlockUpdate', $this->_custom)) {
                $withoutBlockUpdate = Mage::helper('fpc/customDependence')->getWithoutBlockUpdate();
            }
            $this->_processActions();

            $response = Mage::app()->getResponse();
            $content = $this->_storage->getResponse()->getBody();
            $storageContainers = $this->_storage->getContainers();

            if ($this->_storage->getCurrentCategory()) {
                if (!Mage::registry('current_category_id')) {
                    Mage::register('current_category_id', $this->_storage->getCurrentCategory());
                }
                if (Mage::helper('mstcore')->isModuleInstalled('Mirasvit_Seo')) {
                    $category = Mage::getModel('catalog/category')->load($this->_storage->getCurrentCategory());
                    if (!Mage::registry('current_category')) {
                        Mage::register('current_category', $category);
                    }
                    if (!Mage::registry('current_entity_key')) {
                        Mage::register('current_entity_key', $category->getPath());
                    }
                }
            }

            if ($this->_storage->getCurrentProduct()) {
                if (!Mage::registry('current_product_id')) {
                    Mage::register('current_product_id', $this->_storage->getCurrentProduct());
                }
                if (Mage::helper('mstcore')->isModuleInstalled('Mirasvit_Seo')) {
                    $product = Mage::getModel('catalog/product')->load($this->_storage->getCurrentProduct());
                    if (!Mage::registry('current_product')) {
                        Mage::register('current_product', $product);
                    }
                }
            }

            if (($emThemeObject = $this->_storage->getCurrentEmTheme())
                && !Mage::registry('em_current_theme')) {
                    Mage::register('em_current_theme', $emThemeObject);
            }

            // restore design settings
            Mage::getSingleton('core/design_package')->setTheme('layout', $this->_storage->getThemeLayout())
                ->setTheme('template', $this->_storage->getThemeTemplate())
                ->setTheme('skin', $this->_storage->getThemeSkin())
                ->setTheme('locale', $this->_storage->getThemeLocale());

            $containers = array();
            preg_match_all(
                Mirasvit_Fpc_Model_Container_Abstract::HTML_NAME_PATTERN,
                $content, $containers, PREG_PATTERN_ORDER
            );
            $containers = array_unique($containers[1]);
            if ($key = array_search('page/html_header_header', $containers)) { // header have to be first because inside can be one more block
                list($containers[0], $containers[$key]) = array($containers[$key], $containers[0]);
            }
            $max = ($containers) ? max(array_keys($containers)) : 0;
            for ($i = 0; $i <= $max; $i++) {
                if (isset($containers[$i])) {
                    $definition = $containers[$i];
                    if (isset($storageContainers[$definition])) {
                        $container = $storageContainers[$definition];

                        if (!$container->inApp()
                            && ($inRegister = $container->inRegister()) ) {
                                $this->_loadRegisters($this->_storage, $inRegister);
                        }

                        $withoutBlockUpdate = false;//(isset($withoutBlockUpdate)) ? $withoutBlockUpdate : $this->_storage->getWithoutBlockUpdate();

                        // if cache for current block not exists, we render whole page (and save updated block to cache)
                        if (!$container->applyToContent($content, $withoutBlockUpdate)
                            && strpos($definition, 'page/switch') === false //if block "page/switch" exist, but empty, we will not render whole page
                            && strpos($definition, 'reports/product_viewed') === false
                        ) {
                            // echo $definition;
                            // die('x');
                            $this->_unregister();

                            return true;
                        }
                    }
                }
            }

            $this->_responseHelper->cleanExtraMarkup($content);

            if (!$content
                || ($this->_custom
                    && in_array('cancelServeResponse', $this->_custom)
                    && Mage::helper('fpc/customDependence')->cancelServeResponse($content))) {
                        $this->_unregister();

                        return true;
            }

            Mage::helper('fpc')->prepareMwDailydealTimer($content);

            //Simple_Forum extension compatibility
            // $content = Mage::helper('fpc/simpleforum')->prepareContent($content);

            if ($this->_custom && in_array('updateFormKey', $this->_custom)) {
                Mage::helper('fpc/customDependence')->updateFormKey($content);
            } else {
                $this->_responseHelper->updateFormKey($content);
            }
            if ($this->_custom && in_array('updateWelcomeMessage', $this->_custom)) {
                Mage::helper('fpc/customDependence')->updateWelcomeMessage($content);
            } else {
                $this->_responseHelper->updateWelcomeMessage($content);
            }
            $this->_responseHelper->updateZopimInfo($content);
            $this->_addMessageText($content);
            if ($this->_custom && in_array('afterServeResponse', $this->_custom)) {
                Mage::helper('fpc/customDependence')->afterServeResponse($content);
            }

            $this->_debugHelper->stopTimer('SELF_TIME');
            $this->_debugHelper->appendDebugInformation($content, 1, $this->_storage);
            $this->_debugHelper->startTimer('FPC_SEND_CONTENT_TIME');
            $response->setBody($content);

            foreach ($this->_storage->getResponse()->getHeaders() as $header) {
                if ($header['name'] != 'Location') {
                    try {
                        $response->setHeader($header['name'], $header['value'], $header['replace']);
                    } catch (Exception $e) { }
                }
            }

            $this->_isServed = true;
            $response->sendResponse();

            Mage::getSingleton('fpc/log')->log($cacheId, 1);
            $this->_debugHelper->stopTimer('FPC_SEND_CONTENT_TIME');
            echo $this->_debugHelper->getSendContentTime();
            exit;
        }
    }

    /**
     * Add message text
     * @return bool
     */
    protected function _addMessageText(&$content)
    {
        if ($this->_catalogMessage) {
            $this->_messageHelper->addMessage($content, $this->_catalogMessage, Mirasvit_Fpc_Model_Config::CATALOG_MESSAGE);
            $this->_catalogMessage = false;
        } elseif ($this->_checkoutMessage) {
            $this->_messageHelper->addMessage($content, $this->_checkoutMessage, Mirasvit_Fpc_Model_Config::CHECKOUT_MESSAGE);
            $this->_checkoutMessage = false;
        } else {
            $this->_messageHelper->addMessage($content);
        }

        return true;
    }


    /**
     * Unregister variables
     * @return bool
     */
    protected function _unregister()
    {
        Mage::unregister('current_category');
        Mage::unregister('current_entity_key');
        Mage::unregister('current_product');
        if (!Mage::registry('m_fpc_don_t_check_if_cache_exist')) {
            Mage::register('m_fpc_don_t_check_if_cache_exist', true);
        }

        return true;
    }

    /**
     * Observer for event `http_response_send_before`
     */
    public function cacheResponse()
    {
        $request = Mage::app()->getRequest();
        $response = Mage::app()->getResponse();

        if (!$this->canProcessRequest($request) || $this->_isServed) {
            return;
        }

        if (!$this->isPageFound()) {
            return false;
        }

        $this->_storage = Mage::getModel('fpc/storage');

        $this->_processActions();

        $cacheId = $this->getRequestCacheId();

        $createdBy = $this->_requestHelper->isCrawler() ? 'Crawler' : 'Visitor';

        $withoutBlockUpdate = ($this->_requestHelper->getCartSize() == 0) ? true : false;

        $this->_storage
            ->setCacheId($cacheId)
            ->setCacheTags($this->getRequestTags())
            ->setCacheLifetime($this->_config->getLifetime())
            ->setContainers($this->_containers)
            ->setResponse($response)
            ->setCreatedAt(time())
            ->setCreatedBy($createdBy)
            ->setWithoutBlockUpdate($withoutBlockUpdate);

        if (Mage::registry('current_category')) {
            $this->_storage->setCurrentCategory(Mage::registry('current_category')->getId());
        }
        if (Mage::registry('current_product')) {
            $this->_storage->setCurrentProduct(Mage::registry('current_product')->getId());
        }
        if (Mage::getSingleton('cms/page')->getId()) {
            $this->_storage->setCurrentCmsPage(Mage::getSingleton('cms/page')->getId());
        }
        if (Mage::registry('em_current_theme')) {
            $this->_storage->setCurrentEmTheme(Mage::registry('em_current_theme'));
        }

        // save design settings
        $design = Mage::getSingleton('core/design_package');
        $this->_storage->setThemeLayout($design->getTheme('layout'))
            ->setThemeTemplate($design->getTheme('template'))
            ->setThemeSkin($design->getTheme('skin'))
            ->setThemeLocale($design->getTheme('locale'));

        try {
            $response->setHeader('Fpc-Cache-Id', $cacheId, true);
        } catch (Exception $e) {
        }

        $this->_storage->save();

        $content = $response->getBody();

        $containers = array();
        preg_match_all(
            Mirasvit_Fpc_Model_Container_Abstract::HTML_NAME_PATTERN,
            $content, $containers, PREG_PATTERN_ORDER
        );
        $containers = array_unique($containers[1]);
        $max = ($containers) ? max(array_keys($containers)) : 0;
        for ($i = 0; $i <= $max; $i++) {
            if (isset($containers[$i])) {
                $definition = $containers[$i];
                if (isset($this->_containers[$definition])) {
                    $container = $this->_containers[$definition];
                    $container->saveToCache($content);
                }
            }
        }

        $this->_responseHelper->cleanExtraMarkup($content);

        $this->_debugHelper->appendDebugInformation($content, 0, $this->_storage);

        $response->setBody($content);

        if ($this->_config->isDebugLogEnabled()) {
            Mage::log('Cache URL: ' . Mage::helper('fpc')->getNormalizedUrl(), null, Mirasvit_Fpc_Model_Config::DEBUG_LOG);
        }

        Mage::getSingleton('fpc/log')->log($cacheId, 0);
        if ($this->_requestHelper->isCrawler()
            && isset($_SESSION)
            && session_id() != '') { //need for delete crawler session files
                session_destroy();
        }
    }

    /**
     * Observer for event `core_block_abstract_to_html_after`
     * @param Varien_Object $observer
     * @return bool
     */
    public function markContainer($observer)
    {
        if (!$this->canProcessRequest(Mage::app()->getRequest())) {
            return false;
        }

        if (!$this->isPageFound()) {
            return false;
        }

        $block = $observer->getEvent()->getBlock();
        $transport = $observer->getEvent()->getTransport();
        $containers = $this->_config->getContainers();
        $blockType = $block->getType();
        $blockName = $block->getNameInLayout();
        $blockTemplate = $block->getTemplate();
        $applyBlock = false;

        if ($this->_custom && in_array('addCartContainerToExclude', $this->_custom)) {
            $containers = Mage::helper('fpc/customDependence')->addCartContainerToExclude($containers, $blockType, $blockName);
        } else {
            $containers = $this->_cartexcludeHelper->addCartContainerToExclude($containers, $blockType, $blockName);
        }

        if (isset($containers[$blockType][$blockTemplate])) {
            $definition = $containers[$blockType][$blockTemplate];
            $applyBlock = true;
        } elseif ($blockType == 'cms/block'
            && ($blockId = $block->getBlockId())
            && isset($containers[$blockType][$blockId])) {
                $definition = $containers[$blockType][$blockId];
                $applyBlock = true;
        } elseif (isset($containers[$blockType][$blockName])) {
            if (!empty($containers[$blockType][$blockName]['name'])
                && $containers[$blockType][$blockName]['name'] != $block->getNameInLayout()
            ) {
                return false;
            }

            $definition = $containers[$blockType][$blockName];
            $applyBlock = true;
        } elseif (isset($containers[$blockType]) && !empty($containers[$blockType]['container'])) {
            if (!empty($containers[$blockType]['name'])
                && $containers[$blockType]['name'] != $block->getNameInLayout()
            ) {
                return false;
            }

            $definition = $containers[$blockType];
            $applyBlock = true;
        }

        if ($applyBlock) {
            $container = new $definition['container']($definition, $block);

            $replacerHtml = $container->getBlockReplacerHtml($transport->getHtml());

            $transport->setHtml($replacerHtml);

            $this->_containers[$container->getDefinitionHash()] = $container;
        }
    }

    /**
     * Cache id for current request (md5)
     *
     * @return string
     */
    public function getRequestCacheId()
    {
        if ($this->_requestCacheId == null) {
            $this->_requestCacheId = $this->_requestcacheidHelper->getRequestCacheId();
        }

        return $this->_requestCacheId;
    }

    public function addRequestTag($tags)
    {
        if (count($this->_requestTags) > self::TAGS_THRESHOLD) {
            return $this;
        }

        if (!is_array($tags)) {
            $tags = array($tags);
        }

        foreach ($tags as $tag) {
            $this->_requestTags[] = $tag;
        }

        return $this;
    }

    /**
     * Check if this request is allowed for process
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @return bool
     */
    public function canProcessRequest($request = null)
    {
        $this->_debugHelper->startTimer('CHECK_PROCESS_REQUEST_TIME');

        if ($this->_canProcessRequest !== null) {
            return $this->_canProcessRequest;
        }

        if ($this->_custom
            && in_array('isMfCache', $this->_custom)
            && !Mage::helper('fpc/customDependence')->isMfCache(false)) {
                $this->_canProcessRequest = false;
                return $this->_canProcessRequest;
        }

        $result = $this->_canprocessrequestHelper->canProcessRequest($request);

        if ($result) {
            $messageTotal = Mage::getSingleton('core/session')->getMessages()->count()
                + Mage::getSingleton('customer/session')->getMessages()->count();

            $catalogMessageCount = Mage::getSingleton('catalog/session')->getMessages()->count();
            $this->_catalogMessage = $this->_messageHelper->getMessage($catalogMessageCount, false);
            if (!$this->_catalogMessage) {
                $messageTotal += $catalogMessageCount;
            }

            $checkoutMessageCount = Mage::getSingleton('checkout/session')->getMessages()->count();
            $this->_checkoutMessage = $this->_messageHelper->getMessage(false, $checkoutMessageCount);
            if (!$this->_checkoutMessage) {
                $messageTotal += $checkoutMessageCount;
            }

            if ($messageTotal) {
                $result = false;
            }
        }

        $this->_canProcessRequest = $result;
        $this->_debugHelper->stopTimer('CHECK_PROCESS_REQUEST_TIME');

        return $this->_canProcessRequest;
    }

    /**
     * Cache tags for current request
     *
     * @return array
     */
    public function getRequestTags()
    {
        $this->_requestTags = array_unique($this->_requestTags);

        foreach ($this->_requestTags as $idx => $tag) {
            $this->_requestTags[$idx] = strtoupper($tag);
        }

        return $this->_requestTags;
    }

    protected function _processActions()
    {
        $config = $this->_config;
        $request = Mage::app()->getRequest();
        $key = $request->getModuleName()
            . '_' . $request->getControllerName()
            . '_' . $request->getActionName();
        $params = new Varien_Object($request->getParams());

        if (($actions = $config->getNode('actions/' . $key)) != null) {
            foreach ($actions->children() as $action) {
                $class = (string)$action->class;
                $method = (string)$action->method;
                if (!$class) {
                    call_user_func(array($this, $method), $params);
                } else {
                    call_user_func(array($class, $method), $params);
                }
            }
        }
    }

    protected function saveSessionVariables()
    {
        $data = Mage::getSingleton('catalog/session')->getData();
        $params = array();
        $paramsMap = array(
            'display_mode',
            'limit_page',
            'sort_order',
            'sort_direction',
        );
        if ($this->_storage->getCacheId()) {
            // need restore
            foreach ($paramsMap as $sessionParam) {
                if ($this->_storage->hasData('catalog_session_' . $sessionParam)) {
                    $value = $this->_storage->getData('catalog_session_' . $sessionParam);
                    Mage::getSingleton('catalog/session')->setData($sessionParam, $value);
                }
            }
        } else {
            // need save
            foreach ($paramsMap as $sessionParam) {
                if (isset($data[$sessionParam])) {
                    $this->_storage->setData('catalog_session_' . $sessionParam, $data[$sessionParam]);
                }
            }
        }
    }

    protected function _loadRegisters($storage, $inRegister)
    {
        $inRegister = explode(",",$inRegister);
        $inRegister = array_map('trim',$inRegister);

        if ($storage->getCurrentCategory() && !Mage::registry('current_category') && in_array('current_category',$inRegister)) {
            $category = Mage::getModel('catalog/category')->load($storage->getCurrentCategory());
            Mage::register('current_category', $category);
            Mage::register('current_entity_key', $category->getPath());
        }

        if ($storage->getCurrentProduct() && !Mage::registry('current_product') && in_array('current_product',$inRegister)) {
            $product = $this->_loadCurrentProduct($storage->getCurrentProduct());
            Mage::register('current_product', $product);
        }

        if ($storage->getCurrentProduct() && !Mage::registry('product') && in_array('product',$inRegister)) {
            $product = $this->_loadCurrentProduct($storage->getCurrentProduct());
            Mage::register('product', $product);
        }

        return $this;
    }

    protected function _loadCurrentProduct($currentProduct)
    {
        if (!$this->_currentProduct) {
            $this->_currentProduct = Mage::getModel('catalog/product')->load($currentProduct);
        }

        return $this->_currentProduct;
    }

    /**
     * Check if noroute page (some nginx servers return incorrect actions)
     *
     * @return bool
     */
    public function isPageFound()
    {
        if(self::$_isPageFound !== null) {
            return self::$_isPageFound;
        }

        self::$_isPageFound = true;
        foreach (Mage::app()->getResponse()->getHeaders() as $header) {
            if ($header['value'] == '404 Not Found') {
                self::$_isPageFound = false;
                $this->_canProcessRequest = false;
                break;
            }
        }

        return self::$_isPageFound;
    }
}
