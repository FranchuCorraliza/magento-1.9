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



class Mirasvit_Fpc_Model_Config extends Varien_Simplexml_Config
{
    const REQUEST_ID_PREFIX = 'FPC_REQUEST_';
    const DEBUG_LOG = 'fpc_debug.log';

    const CACHE_TAGS_LEVEL_FIRST          = 1;
    const CACHE_TAGS_LEVEL_SECOND         = 2;
    const CACHE_TAGS_LEVEL_MINIMAL        = 3;
    const CACHE_TAGS_LEVEL_EMPTY          = 4;
    const CACHE_TAGS_LEVEL_MINIMAL_PREFIX = 5;

    const ALLOW_HDD_FREE_SPACE = 150; // Mb

    const ALLOWED_PEERFORMANCE_SAVE_TIME = 0.1; // local save cache time - 0.0016s
    const ALLOWED_PEERFORMANCE_CLEAN_TIME = 0.25; //clean cache time - 0.019s

    const MAX_SESSION_SIZE = 3; //Mb

    const REGISTER_MODEL_TAG = 'REGISTER_MODEL_TAG_TIME_';
    const REGISTER_PRODUCT_TAG = 'REGISTER_PRODUCT_TAG_TIME_';
    const REGISTER_COLLECTION_TAG = 'REGISTER_COLLECTION_TAG_TIME_';

    const MAX_PRODUCT_REGISTER = 100;

    const CATALOG_MESSAGE = 1;
    const CHECKOUT_MESSAGE = 2;

    const OPTIMAL_CONFIG_MESSAGE = 'fpc_optimal_config_message_enabled';

    const FLUSH_CURRENT_PAGE_CACHE_BUTTON = 1;
    const FLUSH_DEPENDING_TAGS_CACHE_BUTTON = 2;

    const TIME_STATS = 1;
    const TIME_STATS_SMALL = 2;

    const CACHE_TAG = 'FPC';
    const STORE_TAG = 'FPCSTORE_';
    const LOGGED_TAG = 'FPC_LOGGED_IN';
    const NOTLOGGED_TAG = 'FPC_NOTLOGGED_IN';

    const CATALOG_CATEGORY_TAG = 'CATALOG_CATEGORY_';
    const CATALOG_PRODUCT_TAG = 'CATALOG_PRODUCT_';
    const CACHE_PREFIX = 'MV';

    protected $_containers = null;

    public function __construct($data = null)
    {
        parent::__construct($data);

        $cacheConfig = Mage::getConfig()->loadModulesConfiguration('cache.xml');
        // if (Mage::getSingleton('customer/session')->getId()) {
        //     $cacheLoggedConfig = Mage::getConfig()->loadModulesConfiguration('cachelogged.xml');
        //     $cacheConfig->extend($cacheLoggedConfig);
        // }
        $customConfig = Mage::getConfig()->loadModulesConfiguration('custom.xml');
        $cacheConfig->extend($customConfig);
        $this->setXml($cacheConfig->getNode());

        return $this;
    }

    public function getLifetime()
    {
        $lifetime = intval(Mage::getStoreConfig('fpc/general/lifetime'));
        if (!$lifetime) {
            $lifetime = 3600;
        }

        return $lifetime;
    }

    public static function isDebug()
    {
        $options = Mage::app()->getConfig()->getNode('global/cache')->asCanonicalArray();
        if (isset($options['debug']) && $options['debug'] == 1) {
            if (isset($options['ip'])
                && $options['ip'] != '*'
                && !in_array($_SERVER['REMOTE_ADDR'], explode(',', $options['ip']))) {
                return false;
            }

            return true;
        }

        return false;
    }

    public function getMaxCacheSize()
    {
        $size = intval(Mage::getStoreConfig('fpc/general/max_cache_size'));
        if (!$size) {
            $size = 1024;
        }

        $size *= 1024 * 1024;

        return $size;
    }

    public function getMaxCacheNumber()
    {
        $number = intval(Mage::getStoreConfig('fpc/general/max_cache_number'));
        if (!$number) {
            $number = 100000;
        }

        return $number;
    }

    public function getGzcompressLevel()
    {
        return Mage::getStoreConfig('fpc/general/gzcompress_level');
    }

    public function getCacheTagslevelLevel()
    {
        $cacheTagsLevel = Mage::getStoreConfig('fpc/general/cache_tags_level');
        if (!$cacheTagsLevel) {
            $cacheTagsLevel = 1;
        }

        return $cacheTagsLevel;
    }

    public function getCacheEnabled($storeId = null)
    {
        return Mage::getStoreConfig('fpc/general/enabled', $storeId);
    }

    public function getStatus()
    {
        return Mage::getStoreConfig('fpc/crawler/status');
    }

    public function getMaxDepth()
    {
        return Mage::getStoreConfig('fpc/cache_rules/max_depth');
    }

    public function getCacheableActions()
    {
       $key = 'fpc/cache_rules/cacheable_actions';

       return $this->_prepareValues($key);
    }

    public function getIgnoredPages()
    {
        $key = 'fpc/cache_rules/ignored_pages';

        return $this->_prepareValues($key);
    }

    public function getUserAgentSegmentation()
    {
        $key = 'fpc/cache_rules/user_agent_segmentation';

        return $this->_prepareValues($key);
    }

    public function getIgnoredUrlParams()
    {
        $key = 'fpc/cache_rules/ignored_url_params';

        return $this->_prepareValues($key);
    }

    protected function _prepareValues($key) {
        $result = array();

        if ($values = Mage::getStoreConfig($key)) {
            $values = unserialize($values);
            foreach ($values as $value) {
                if (count($value) == 1) {
                    $result[] = array_pop($value);
                } else {
                    $result[] = $value;
                }
            }
        }

        return $result;
    }

    public function getMobileDetect()
    {
         return Mage::getStoreConfig('fpc/cache_rules/mobile_detect');
    }

    public function getContainers()
    {
        if ($this->_containers === null) {
            $this->_containers = array();
            foreach ($this->getNode('containers')->children() as $container) {
                $containerName = (string) $container->name;
                $containerBlock = (string) $container->block;
                $containerBlockId = isset($container->block_id) ? (string) $container->block_id : false;
                $containerTemplate = isset($container->template) ? (string) $container->template : false;

                $containerData = array(
                        'container' => (string) $container->container,
                        'block' => $containerBlock,
                        'cache_lifetime' => (int) $container->cache_lifetime,
                        'name' => (string) $container->name,
                        'depends' => (string) $container->depends,
                        'in_register' =>  isset($container->in_register) ? (string) $container->in_register : false,
                        'in_session' =>  isset($container->in_session) ? ((trim($container->in_session) !== 'true') ? intval($container->in_session) : true) : false,
                        'in_app' => isset($container->in_app) ? intval($container->in_app) : intval($container->in_app) + 1,
                        'block_id' => $containerBlockId,
                        'replacer_tag_begin' => isset($container->replacer_tag_begin) ? (string) $container->replacer_tag_begin : false,
                        'replacer_tag_end' => isset($container->replacer_tag_end) ? (string) $container->replacer_tag_end : false,
                        'template' => $containerTemplate,
                        'set_id' => isset($container->set_id) ? (int) $container->set_id : false, // for Mirasvit_Fpc_Model_Container_PhtmlblockAm
                    );

                if ($containerTemplate) {
                    $this->_containers[$containerBlock][$containerTemplate] = $containerData;
                } elseif ($containerBlock == 'cms/block' && $containerBlockId) {
                    $this->_containers[$containerBlock][$containerBlockId] = $containerData;
                } elseif (!empty($containerName)) {
                    $this->_containers[$containerBlock][$containerName] = $containerData;
                } else {
                    $this->_containers[$containerBlock] = $containerData;
                }
            }
        }

        return $this->_containers;
    }

    public function isDebugHintsEnabled($storeId = null)
    {
        if (!self::isDebugAllowed()) {
            return false;
        }

        return Mage::getStoreConfig('fpc/debug/hints', $storeId);
    }

    public function isDebugInfoEnabled($storeId = null)
    {
        if (!self::isDebugAllowed()) {
            return false;
        }

        return Mage::getStoreConfig('fpc/debug/info', $storeId);
    }

    public function isDebugLogEnabled($storeId = null)
    {
        if (!self::isDebugAllowed()) {
            return false;
        }

        return Mage::getStoreConfig('fpc/debug/log', $storeId);
    }

    public function isDebugAllowed($storeId = null)
    {
        if (Mage::app()->getRequest()->isXmlHttpRequest()
            || (strpos(Mage::helper('core/url')->getCurrentUrl(), 'api/') !== false
                && strpos(Mage::helper('core/url')->getCurrentUrl(), 'soap') !== false)
            || (Mage::helper('mstcore')->isModuleInstalled('Nexcessnet_Turpentine')
                && !Mage::helper('turpentine/varnish ')->isBypassEnabled()) ) {
                    return false;
        }

        $userAgent = Mage::helper('core/http')->getHttpUserAgent();
        if (preg_match('/testmirasvit/', $userAgent)) {
            return true;
        }

        $ips = Mage::getStoreConfig('fpc/debug/allowed_ip', $storeId);
        if ($ips == '') {
            return true;
        }

        $clientIp = false;

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $clientIp = $_SERVER['HTTP_CLIENT_IP'];
        } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $clientIp = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $clientIp = $_SERVER['REMOTE_ADDR'];
        }

        if (!$clientIp) {
            return false;
        }

        $ips = explode(',', $ips);
        $ips = array_map('trim',$ips);

        return in_array($clientIp, $ips);
    }

    /**
     * @return int
     */
    public function getDebugButtonConfiguration()
    {
        return Mage::getStoreConfig('fpc/debug/flush_cache_button');
    }

    /**
     * @return string
     */
    public function getTagPrefix()
    {
        $cachePrefix = '';
        if ($this->getCacheTagslevelLevel() == self::CACHE_TAGS_LEVEL_MINIMAL_PREFIX) {
            $cachePrefix = self::CACHE_PREFIX;
        }

        return $cachePrefix;
    }
}
