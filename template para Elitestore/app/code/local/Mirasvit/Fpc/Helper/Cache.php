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



class Mirasvit_Fpc_Helper_Cache extends Mage_Core_Helper_Abstract
{
    /**
     * @var bool|array
     */
    protected $_custom;

    public function __construct()
    {
        $this->_custom = Mage::helper('fpc/custom')->getCustomSettings();
    }

    /**
     * Clean old cache
     * @return void
     */
    public function cleanOldCache()
    {
        $clean = true;
        if ($this->_custom && in_array('getCleanOldCache', $this->_custom)) {
            $clean = Mage::helper('fpc/customDependence')->getCleanOldCach();
        }

        if($clean) {
            $cache = Mage::getSingleton('fpc/cache')->getCacheInstance();
            $frontend = $cache->getFrontend();
            $backend = $frontend->getBackend();
            $backend->clean('old');
            // Mage::app()->getCache()->getBackend()->clean('old');
            // Mage::getSingleton('core/cache')->getFrontend()->clean(Zend_Cache::CLEANING_MODE_OLD);
        }
    }
}
