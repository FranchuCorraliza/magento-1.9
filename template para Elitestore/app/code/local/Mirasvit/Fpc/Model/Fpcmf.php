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



class Mirasvit_Fpc_Model_Fpcmf
{
    /**
     * @var array
     */
    protected $_requestTags = null;

    /**
     * @var bool
     */
    protected $_canCacheResponse = null;

    /**
     * @var array
     */
    protected $_blocks = array();

    /**
     * @var Mirasvit_Fpc_Model_Storagemf
     */
    protected $_storage = null;

    /**
     * @var Mirasvit_Fpc_Helper_Fpcmf_Debugmf
     */
    protected $_debug;

    public function __construct()
    {
        $this->_requestTags = array(Mirasvit_Fpc_Model_Configmf::CACHE_TAG);
        $this->_debug = new Mirasvit_Fpc_Helper_Fpcmf_Debugmf();
    }

    /**
     * @param string $definition
     * @param object $block
     * @return void
     */
    public function addBlocks($definition, $block)
    {
        $this->_blocks[$definition] = $block;

        return $this;
    }

    /**
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function cacheResponse($observer)
    {
        $response = Mage::app()->getResponse();

        if ($this->canCacheResponse()) {
            Mage::getSingleton('fpc/observermf_sessionmf')->onHttpResponseSendBefore(); # we update session variables before save to cache
            $storage = Mirasvit_Fpc_Model_Storagemf::getInstance();
            $content = $response->getBody();

            $cacheId = Mage::getSingleton('fpc/requestmf_processormf')->getRequestCacheId();

            $storage->setCacheId($cacheId)
                ->setCacheTags($this->getRequestTags())
                ->setCacheLifetime($this->getConfig()->getLifetime())
                ->setBlocks($this->_blocks)
                ->setContent($content)
                ;

            $storage->setRequestAliases(Mage::app()->getRequest()->getAliases())
                ->setRequestRouteName(Mage::app()->getRequest()->getRequestedRouteName())
                ->setRequestControllerName(Mage::app()->getRequest()->getRequestedControllerName())
                ->setRequestActionName(Mage::app()->getRequest()->getRequestedActionName())
                ;

            $session = Mage::getSingleton('core/session');

            $storage->setSessionName($session->getSessionName())
                ->setSessionLifetime($session->getCookie()->getLifetime())
                ->setSessionPath($session->getCookie()->getPath())
                ->setSessionDomain($session->getCookie()->getDomain())
                ->setSessionIsSecure($session->getCookie()->isSecure())
                ->setSessionHttponly($session->getCookie()->getHttponly())
                ;

            $storage->save();

            try {
                $response->setHeader('Fpc-Cache-Id', $cacheId, true);
            } catch (Exception $e) {
            }

            $content = $content;

            foreach ($this->_blocks as $block) {
                $block->saveToCache($content);
            }

            Mage::helper('fpc/fpcmf_contentmf')->clearWrappers($content);

            $this->_debug->appendDebugInformation($content, $storage, Mirasvit_Fpc_Model_Configmf::MISS);
            Mirasvit_Fpc_Model_Logmf::log($storage, Mirasvit_Fpc_Model_Configmf::MISS);

            $response->setBody($content);
        }
    }

    /**
     * Wrap html output of block defined at mst_fpc.xml with <fpc definition="..."></fpc>.
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function wrapBlock($observer)
    {
        if (!Mage::getSingleton('fpc/fpcmf')->canCacheResponse()) {
            return $this;
        }

        $blocks = $this->getConfig()->getBlocks();

        $transport = $observer->getEvent()->getTransport();

        $block = $observer->getEvent()->getBlock();
        $blockType = $block->getType();
        $blockName = $block->getNameInLayout();

        $blockDefinition = null;

        if (isset($blocks[$blockType.$blockName])) {
            $blockDefinition = $blocks[$blockType.$blockName];
        } elseif (isset($blocks[$blockType])) {
            $blockDefinition = $blocks[$blockType];
        }

        if ($blockDefinition) {
            $blockClass = new $blockDefinition['class']($blockDefinition, $block);
            $replacerHtml = $blockClass->getWrappedHtml($transport->getHtml());

            $transport->setHtml($replacerHtml);

            $this->addBlocks($blockClass->getDefinitionHash(), $blockClass);
        }

        return $this;
    }

    /**
     * @param array $tags
     * @return void
     */
    public function addRequestTag($tags)
    {
        if (count($this->_requestTags) > Mirasvit_Fpc_Model_Configmf::MAX_NUMBER_OF_TAGS) {
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
     * @return Mage_Core_Model_Cache
     */
    public function getCache()
    {
        return Mirasvit_Fpc_Model_Cachemf::getCacheInstance();
    }

    /**
     * @return Mirasvit_Fpc_Model_Configmf
     */
    public function getConfig()
    {
        return Mage::getSingleton('fpc/configmf');
    }

    /**
     * Check if this request are allowed for process.
     *
     * @return bool
     */
    public function canCacheResponse($request = null)
    {
        $request = Mage::app()->getRequest();
        $response = Mage::app()->getResponse();

        if ($this->_canCacheResponse !== null) {
            return $this->_canCacheResponse;
        }

        if ($response->getHttpResponseCode() != 200) {
            $this->_canCacheResponse = false;

            return $this->_canCacheResponse;
        }

        if ($_SERVER['REQUEST_METHOD'] != 'GET') {
            return false;
        }

        if (isset($_GET['fpc_block'])) {
            return false;
        }

        foreach ($response->getHeaders() as $header) {
            if ($header['name'] == 'Location') {
                $this->_canCacheResponse = false;

                return $this->_canCacheResponse;
            }
        }

        if ($request && $request->getActionName() == 'noRoute') {
            $this->_canCacheResponse = false;

            return $this->_canCacheResponse;
        }

        $result = Mage::app()->useCache('fpc');

        if ($result) {
            $result = !isset($_GET['no_cache']);
        }

        if ($result) {
            $result = !(count($_POST) > 0);
        }

        if ($result) {
            $result = Mage::app()->getStore()->getId() != 0;
        }

        if ($result) {
            $result = $this->getConfig()->getCacheEnabled(Mage::app()->getStore()->getId());
        }

        if ($result && isset($_GET) && isset($_GET['no_cache'])) {
            $result = false;
        }

        if ($result) {
            $regExps = $this->getConfig()->getIgnoredPages();
            foreach ($regExps as $exp) {
                if (preg_match($exp, Mage::helper('fpc/fpcmf_datamf')->getNormlizedUrl())) {
                    $result = false;
                }
            }
        }

        if ($request) {
            $action = $request->getModuleName().'/'.$request->getControllerName().'_'.$request->getActionName();
            if ($result && count($this->getConfig()->getCacheableActions())) {
                $result = in_array($action, $this->getConfig()->getCacheableActions());
            }
        }

        if ($result && isset($_GET)) {
            $maxDepth = $this->getConfig()->getMaxDepth();
            $result = count($_GET) <= $maxDepth;
        }

        $messageTotal = Mage::getSingleton('core/session')->getMessages()->count()
                + Mage::getSingleton('checkout/session')->getMessages()->count()
                + Mage::getSingleton('customer/session')->getMessages()->count()
                + Mage::getSingleton('catalog/session')->getMessages()->count();

        if ($layout = Mage::app()->getLayout()) {
            if ($block = $layout->getBlock('messages')) {
                $messageTotal += $block->getMessageCollection()->count();
            }
            if ($block = $layout->getBlock('global_messages')) {
                $messageTotal += $block->getMessageCollection()->count();
            }
        }

        if ($result && $messageTotal) {
            $result = false;
        }

        $this->_canCacheResponse = $result;

        return $this->_canCacheResponse;
    }

    /**
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

    /**
     * @return void
     */
    protected function _processActions()
    {
        $config = $this->getConfig();
        $request = Mage::app()->getRequest();
        $key = $request->getModuleName()
            .'_'.$request->getControllerName()
            .'_'.$request->getActionName();
        $params = new Varien_Object($request->getParams());

        if (($actions = $config->getNode('actions/'.$key)) != null) {
            foreach ($actions->children() as $action) {
                $class = (string) $action->class;
                $method = (string) $action->method;
                if (!$class) {
                    call_user_func(array($this, $method), $params);
                } else {
                    call_user_func(array($class, $method), $params);
                }
            }
        }
    }

    /**
     * @return void
     */
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
        if ($storage->getCacheId()) {
            // need restore
            foreach ($paramsMap as $sessionParam) {
                if ($storage->hasData('catalog_session_'.$sessionParam)) {
                    $value = $storage->getData('catalog_session_'.$sessionParam);
                    Mage::getSingleton('catalog/session')->setData($sessionParam, $value);
                }
            }
        } else {
            // need save
            foreach ($paramsMap as $sessionParam) {
                if (isset($data[$sessionParam])) {
                    $storage->setData('catalog_session_'.$sessionParam, $data[$sessionParam]);
                }
            }
        }
    }

    /**
     * @return void
     */
    protected function _loadRegisters($storage)
    {
        if ($storage->getCurrentCategory() && !Mage::registry('current_category')) {
            $category = Mage::getModel('catalog/category')->load($storage->getCurrentCategory());
            Mage::register('current_category', $category);
            Mage::register('current_entity_key', $category->getPath());
        }

        if ($storage->getCurrentProduct() && !Mage::registry('current_product')) {
            $product = Mage::getModel('catalog/product')->load($storage->getCurrentProduct());
            Mage::register('current_product', $product);
        }

        return $this;
    }
}
