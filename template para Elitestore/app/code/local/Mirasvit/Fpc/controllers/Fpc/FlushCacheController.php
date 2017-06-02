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



class Mirasvit_Fpc_Fpc_FlushCacheController extends Mage_Core_Controller_Front_Action
{
    /**
     * @var Mirasvit_Fpc_Model_Cache
     */
    protected $_cache;

    /**
     * @var Mirasvit_Fpc_Model_Config
     */
    protected $_config;

     /**
     * @var Mage_Core_Controller_Request_Http
     */
    protected $_request;

    public function _construct()
    {
         $this->_cache = Mage::getSingleton('fpc/cache');
         $this->_config = Mage::getSingleton('fpc/config');
         $this->_request = Mage::app()->getRequest();
    }

    public function flushAction()
    {
        $debugButtonConfiguration = $this->_config->getDebugButtonConfiguration();
        $fpcTags = $this->_request->getParam('fpcTags');
        $cacheId = $this->_request->getParam('cacheId');

        if(!$debugButtonConfiguration || (!$fpcTags && !$cacheId)) {
            $this->norouteAction();
            return false;
        }

        if ($debugButtonConfiguration == Mirasvit_Fpc_Model_Config::FLUSH_DEPENDING_TAGS_CACHE_BUTTON && $fpcTags) {
                $fpcTags = json_decode($fpcTags);
                $fpcTags = $this->prepareTags($fpcTags);
                $this->_cache->clearCacheByTags($fpcTags);
                print_r($fpcTags);
        } elseif ($debugButtonConfiguration == Mirasvit_Fpc_Model_Config::FLUSH_CURRENT_PAGE_CACHE_BUTTON && $cacheId) {
                $cacheId = $cacheId;
                $this->_cache->clearCacheById($cacheId);
                echo $cacheId;
        }

        return true;
    }

    /**
     * @param array $fpcTags
     * @return array
     */
    protected function prepareTags($fpcTags)
    {
        $ignoredTags = array(Mirasvit_Fpc_Model_Config::CACHE_TAG,
            Mirasvit_Fpc_Model_Config::LOGGED_TAG,
            Mirasvit_Fpc_Model_Config::NOTLOGGED_TAG,
        );

        $storeTag =  Mirasvit_Fpc_Model_Config::STORE_TAG;

        foreach ($fpcTags as $tagKey => $tagValue) {
            if (in_array($tagValue, $ignoredTags)
                || stripos($tagValue, $storeTag) !== false) {
                    unset($fpcTags[$tagKey]);
            }
        }

        return $fpcTags;
    }
}
