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



class Mirasvit_Fpc_Model_Cachemf
{
    /**
     * @var Mage_Core_Model_Cache
     */
    protected static $_cache = null;

    /**
     * @var string
     */
    public static $cacheDir = null;

    /**
     * @return Mage_Core_Model_Cache
     */
    public static function getCacheInstance()
    {
        if (is_null(self::$_cache)) {
            $options = Mage::app()->getConfig()->getNode('global/fpc');
            if (!$options) {
                self::$_cache = Mage::app()->getCacheInstance();
                return self::$_cache;
            }

            $options = $options->asArray();

            foreach (array('backend_options', 'slow_backend_options') as $tag) {
                if (!empty($options[$tag]['cache_dir'])) {
                    self::$cacheDir = Mage::getBaseDir('var').DS.$options[$tag]['cache_dir'];
                    $options[$tag]['cache_dir'] = self::$cacheDir;
                    Mage::app()->getConfig()->getOptions()->createDirIfNotExists($options[$tag]['cache_dir']);
                }
            }

            self::$_cache = Mage::getModel('core/cache', $options);
        }

        return self::$_cache;
    }

    /**
     * @return Mirasvit_Fpc_Model_Configmf
     */
    public function getConfig()
    {
        return Mage::getSingleton('fpc/configmf');
    }

    /**
     * @return void
     */
    public function cleanByLimits()
    {
        if (Mage::helper('fpc/fpcmf_datamf')->getCacheSize() > Mage::getSingleton('fpc/configmf')->getMaxCacheSize()
            || Mage::helper('fpc/fpcmf_datamf')->getCacheNumber() > Mage::getSingleton('fpc/configmf')->getMaxCacheNumber()) {
            if ($this->getConfig()->isDebugLogEnabled()) {
                Mage::log('Reached max cache limits ', null, Mirasvit_Fpc_Model_Config::DEBUG_LOG);
            }

            $this->clearAll();

            if (Mage::helper('fpc/fpcmf_datamf')->getCacheSize() > Mage::getSingleton('fpc/config')->getMaxCacheSize()
                || Mage::helper('fpc/fpcmf_datamf')->getCacheNumber() > Mage::getSingleton('fpc/config')->getMaxCacheNumber()) {
                self::getCacheInstance()->flush();
            }
        }

        return $this;
    }

    /**
     * @return void
     */
    public function clearAll()
    {
        try {
            $allTypes = Mage::app()->useCache();
            foreach ($allTypes as $type => $blah) {
                Mage::app()->getCacheInstance()->cleanType($type);
            }
        } catch (Exception $e) {
        }

        if ($this->getConfig()->isDebugLogEnabled()) {
            Mage::log('Clearing all cache ', null, Mirasvit_Fpc_Model_Config::DEBUG_LOG);
        }
    }

    /**
     * @return void
     */
    public function onCleanCache($observer)
    {
        self::getCacheInstance()->clean($observer->getTags());

        return $this;
    }

}
