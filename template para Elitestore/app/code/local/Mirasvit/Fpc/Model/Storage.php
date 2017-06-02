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



class Mirasvit_Fpc_Model_Storage extends Varien_Object
{
    public function getConfig()
    {
        return Mage::getSingleton('fpc/config');
    }

    public function save()
    {
        $cache = Mirasvit_Fpc_Model_Cache::getCacheInstance();
        $data = serialize($this->getData());
        if (($compressLevel = $this->getConfig()->getGzcompressLevel()) && $this->_isFilecache()) {
            $data = gzcompress($data, $compressLevel);
        }
        $cache->save($data, $this->getCacheId(), $this->getCacheTags(), $this->getCacheLifetime());

        return $this;
    }

    public function load()
    {
        $cache = Mirasvit_Fpc_Model_Cache::getCacheInstance();
        $content = $cache->load($this->getCacheId());
        if ($content) {
            if (($compressLevel = $this->getConfig()->getGzcompressLevel()) && $this->_isFilecache()) {
                $content = gzuncompress($content);
            }
            $data = unserialize($content);
            $this->setData($data);

            return $this;
        }

        return false;
    }

    protected function _isFilecache()
    {
        $cache = Mage::getSingleton('fpc/cache')->getCacheInstance();
        $frontend = $cache->getFrontend();
        $backend = $frontend->getBackend();
        if ($backend instanceof Zend_Cache_Backend_File) {
            return true;
        }

        return false;
    }
}
