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



class Mirasvit_Fpc_Model_Observermf extends Varien_Debug
{
    /**
     * @var int
     */
    protected $_registerModelTagCalls = 0;

    /**
     * @var int
     */
    protected $_registerProductTagCounter = 0;

    /**
     * @var Mirasvit_Fpc_Model_Fpcmf
     */
    protected $_fpc;

    /**
     * @var Mirasvit_Fpc_Model_Configmf
     */
    protected $_config;

    /**
     * @var bool
     */
    protected $_isEnabled;

    /**
     * @var bool
     */
    protected $_isAdmin;

    public function __construct()
    {
        $this->_fpc = Mage::getSingleton('fpc/fpcmf');
        $this->_config = Mage::getSingleton('fpc/configmf');
        $this->_isEnabled = Mage::app()->useCache('fpc');
        $this->_isAdmin = $this->isAdmin();
    }

    /**
     * @return bool
     */
    protected function isAdmin() {
        $node = Mage::getConfig()->getNode('admin/routers/adminhtml/args/frontName');
        if ($node && is_object($node) && ($adminName = $node->__toString())) {
            if (strpos(Mage::helper('core/url')->getCurrentUrl(), "/".$adminName) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isAllowed()
    {
        return $this->_isEnabled;
    }

    /**
     * @return void
     */
    public function onHttpResponseSendBefore($observer)
    {
        $this->_fpc->cacheResponse($observer);
    }

    /**
     * Clean full page cache.
     * @return void
     */
    public function cleanCache($observer)
    {
        if ($observer->getEvent()->getType() == 'fpc') {
            Mirasvit_Fpc_Model_Cachemf::getCacheInstance()->clean(Mirasvit_Fpc_Model_Configmf::CACHE_TAG);
        }

        return $this;
    }

    /**
     * @return void
     */
    public function flushCache($observer)
    {
        Mirasvit_Fpc_Model_Cachemf::getCacheInstance()->flush();
    }

    /**
     * @return void
     */
    public function registerModelTag($observer)
    {
        if ($this->_isAdmin || !$this->isAllowed()) {
            return $this;
        }

        if (Mirasvit_Fpc_Model_Configmf::CACHE_TAGS_LEVEL == 1 && $this->_registerModelTagCalls > 0) {
            return $this;
        }

        $object = $observer->getEvent()->getObject();
        if ($object && $object->getId()) {
            $tags = $object->getCacheIdTags();
            if ($tags) {
                if (Mirasvit_Fpc_Model_Configmf::CACHE_TAGS_LEVEL == 1) {
                    if (count($tags) > 0) {
                        $tags = array($tags[0]);
                    }
                }
                foreach ($tags as $tagKey => $tagValue) {
                    if (strpos($tagValue, 'quote') !== false) {
                        unset($tags[$tagKey]);
                    }
                }
                $this->_fpc->addRequestTag($tags);
                $this->_registerModelTagCalls++;
            }
        }

        return $this;
    }

    /**
     * @return void
     */
    public function registerProductTags($observer)
    {
        if ($this->_isAdmin || !$this->isAllowed()) {
            return $this;
        }

        if ($this->_registerProductTagCounter > Mirasvit_Fpc_Model_Config::MAX_PRODUCT_REGISTER) {
            return $this;
        }

        $object = $observer->getEvent()->getProduct();
        if ($object && $object->getId()) {
            $tags = $object->getCacheIdTags();
            if ($tags) {
                $this->_fpc->addRequestTag($tags);
            }
        }
    }

    /**
     * @return void
     */
    public function registerCollectionTag($observer)
    {
        if ($this->_isAdmin) {
            return $this;
        }
        if (!$this->isAllowed() || Mirasvit_Fpc_Model_Configmf::CACHE_TAGS_LEVEL == 1) {
            return $this;
        }

        $collection = $observer->getEvent()->getCollection();
        if ($collection) {
            foreach ($collection as $object) {
                $tags = $object->getCacheIdTags();
                if ($tags) {
                    $this->_fpc->addRequestTag($tags);
                }
            }
        }

        return $this;
    }

    /**
     * @return void
     */
    public function onStockItemSaveAfter($observer)
    {
        if ($observer->getDataObject() && $observer->getDataObject()->getProductId()) {
            $productId = $observer->getDataObject()->getProductId();
            Mirasvit_Fpc_Model_Cachemf::getCacheInstance()->clean('CATALOG_PRODUCT_'.$productId);
        }
    }

    /**
     * @return void
     */
    public function onLayoutRenderBefore($observer)
    {
        $request = Mage::app()->getRequest();
        if ($request->getParam('fpc_blocks')) {
            $blocks = Mage::helper('core')->jsonDecode($request->getParam('fpc_blocks'));

            $result = array();

            $layout = Mage::app()->getLayout();
            foreach ($layout->getAllBlocks() as $layoutBlock) {
                $blockType = $layoutBlock->getType();
                $blockName = $layoutBlock->getNameInLayout();

                foreach ($blocks as $id => $key) {
                    if ($key == $blockType.$blockName || $key == $blockType) {
                        $html = $layoutBlock->toHtml();
                        Mage::helper('fpc/content')->clearWrappers($html);
                        $result[$id] = $html;
                    }
                }
            }

            Mage::app()->getResponse()->setBody(Mage::helper('core')->jsonEncode($result))
                ->sendResponse();
            exit;
        }

        return $this;
    }
}
