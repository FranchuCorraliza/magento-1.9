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



class Mirasvit_Fpc_Model_Configmf extends Varien_Simplexml_Config
{
    const HIT = 'hit';
    const MISS = 'miss';

    const CACHE_TAG = 'FPC';
    const REQUEST_ID_PREFIX = 'FPC_REQUEST_';
    const DEBUG_FILE = 'fpc_debug.log';
    const LOG_FILE = 'fpc.log';
    const MAX_NUMBER_OF_TAGS = 100;

    const HTML_NAME_PATTERN_OPEN = '/<fpc definition="(.*?)">/i';
    const HTML_NAME_PATTERN_CLOSE = '/<\/fpc definition="(.*?)">/i';

    const CACHE_TAGS_LEVEL = 1; //1 - only primary tags; 2 - all tags

    /**
     * @var array
     */
    protected static $_blocks = null;

    /**
     * @var array
     */
    protected static $_config = null;

    /**
     * @var array
     */
    protected static $_storeConfig = null;

    /**
     * @param string|Varien_Simplexml_Element $data
     */
    public function __construct($data = null)
    {
        parent::__construct($data);

        if (Mage::app()->getConfig()->getNode('global/mst_fpc')) {
            self::$_config = Mage::app()->getConfig()->getNode('global/mst_fpc')->asArray();
        }

        return $this;
    }

    /**
     * @param string $path
     * @return string
     */
    public function getStoreConfig($path)
    {
        if (self::$_storeConfig === null) {
            $resource = Mage::getSingleton('core/resource');
            $adapter = $resource->getConnection('core_read');

            $select = $adapter->select()
                ->from($resource->getTableName('core/config_data'), array('path', 'value'))
                ->where('path LIKE "%"', 'fpc/%')
                ->where('scope_id=?', 0)
            ;

            self::$_storeConfig = $adapter->fetchAll($select, array(), PDO::FETCH_KEY_PAIR);
        }

        if (isset(self::$_storeConfig[$path])) {
            return self::$_storeConfig[$path];
        }

        return false;
    }

    /**
     * @return array
     */
    public function getRequestDependencies()
    {
        $dependencies = self::$_config['request_dependencies'];

        return array_filter(explode(',', $dependencies));
    }

    /**
     * @return int
     */
    public function getLifetime()
    {
        $lifetime = intval($this->getStoreConfig('fpc/general/lifetime'));

        if (!$lifetime) {
            $lifetime = 3600;
        }

        return $lifetime;
    }

    /**
     * @return bool
     */
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

    /**
     * @return int
     */
    public function getMaxCacheSize()
    {
        $size = intval($this->getStoreConfig('fpc/general/max_cache_size'));
        if (!$size) {
            $size = 2048;
        }

        $size *= 1024 * 1024;

        return $size;
    }

    /**
     * @return int
     */
    public function getMaxCacheNumber()
    {
        $number = intval($this->getStoreConfig('fpc/general/max_cache_number'));
        if (!$number) {
            $number = 100000;
        }

        return $number;
    }

    /**
     * @param int $storeId
     * @return bool
     */
    public function getCacheEnabled($storeId = null)
    {
        return $this->getStoreConfig('fpc/general/enabled', $storeId);
    }

    /**
     * @return array
     */
    public function getAllowedPages()
    {
        $result = array();
        $key = 'fpc/cache_rules/allowed_pages';

        if ($values = $this->getStoreConfig($key)) {
            $values = unserialize($values);
            foreach ($values as $value) {
                $useValue = true;
                if (isset($value['url_regexp'])) {
                    $checkIfEmpty = trim($value['url_regexp']);
                    if (empty($checkIfEmpty)) {
                        $useValue = false;
                    }
                }

                if ($useValue) {
                    if (count($value) == 1) {
                        $result[] = array_pop($value);
                    } else {
                        $result[] = $value;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getCacheableActions()
    {
        $result = array();
        $key = 'fpc/cache_rules/cacheable_actions';

        if ($values = $this->getStoreConfig($key)) {
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

    /**
     * @return array
     */
    public function getIgnoredPages()
    {
        $result = array();
        $key = 'fpc/cache_rules/ignored_pages';

        if ($values = $this->getStoreConfig($key)) {
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

    /**
     * @return array
     */
    public function getUserAgentSegmentation()
    {
        $result = array();

        $key = 'fpc/cache_rules/user_agent_segmentation';

        if ($values = $this->getStoreConfig($key)) {
            $values = unserialize($values);
            foreach ($values as $value) {
                if (count($value) == 1) {
                    $result = array_pop($value);
                } else {
                    $result[] = $value;
                }
            }
        }

        return $result;
    }

    /**
     * @param string $key
     * @return array
     */
    public function getIgnoredUrlParams()
    {
        $key = 'fpc/cache_rules/ignored_url_params';

        return $this->_prepareValues($key);
    }

    /**
     * @return array
     */
    protected function _prepareValues($key) {
        $result = array();

        if ($values = $this->getStoreConfig($key)) {
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

    /**
     * @return string
     */
    public function getMaxDepth()
    {
        return $this->getStoreConfig('fpc/cache_rules/max_depth');
    }

    /**
     * @return array
     */
    public function getBlocks()
    {
        if (self::$_blocks === null && isset(self::$_config['blocks'])) {
            self::$_blocks = array();

            foreach (self::$_config['blocks'] as $name => $block) {
                $blockData = array(
                    'block_name' => $name,
                    'class' => isset($block['class'])
                        ? (string) $block['class']
                        : 'Mirasvit_Fpc_Model_Blockmf_Base',
                    'block_type' => (string) $block['block'],
                    'lifetime' => isset($block['lifetime'])
                        ? (int) $block['lifetime']
                        : Mage::getSingleton('fpc/configmf')->getLifetime(),
                    'name_in_layout' => isset($block['name']) ? $block['name'] : false,
                    'dependencies' => isset($block['dependencies'])
                        ? array_filter(explode(',', $block['dependencies']))
                        : array(),
                    'behavior' => isset($block['behavior']) ? $block['behavior'] : 'onpage',
                );

                self::$_blocks[$blockData['block_type'].$blockData['name_in_layout']] = $blockData;
            }
        }

        return self::$_blocks;
    }

    /**
     * @param int $storeId
     * @return bool
     */
    public function isDebugHintsEnabled($storeId = null)
    {
        if (!self::isDebugAllowed()) {
            return false;
        }

        return $this->getStoreConfig('fpc/debug/hints', $storeId);
    }

    /**
     * @param int $storeId
     * @return bool
     */
    public function isDebugInfoEnabled($storeId = null)
    {
        if (!self::isDebugAllowed()) {
            return false;
        }

        return $this->getStoreConfig('fpc/debug/info', $storeId);
    }

    /**
     * @param int $storeId
     * @return bool
     */
    public function isDebugLogEnabled($storeId = null)
    {
        if (!self::isDebugAllowed()) {
            return false;
        }

        return $this->getStoreConfig('fpc/debug/log', $storeId);
    }

    /**
     * @param int $storeId
     * @return bool
     */
    public function isDebugAllowed($storeId = null)
    {
        $userAgent = Mage::helper('core/http')->getHttpUserAgent();
        if (preg_match('/testmirasvit/', $userAgent)) {
            return true;
        }

        $ips = $this->getStoreConfig('fpc/debug/allowed_ip', $storeId);
        if ($ips == '') {
            return true;
        }

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $clientIp = $_SERVER['HTTP_CLIENT_IP'];
        } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $clientIp = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
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
     * @return string
     */
    public function getSessionSavePath()
    {
        if ($sessionSavePath = Mage::app()->getConfig()->getNode('global/session_save_path')) {
            return $sessionSavePath;
        }

        return Mage::getBaseDir('session');
    }

    /**
     * @return string
     */
    public function getSessionSaveMethod()
    {
        return Mage::app()->getConfig()->getNode('global/session_save');
    }

    /**
     * @return int
     */
    public function getDebugButtonConfiguration()
    {
        return $this->getStoreConfig('fpc/debug/flush_cache_button');
    }
}
