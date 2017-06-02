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



class Mirasvit_Fpc_Model_Logmf extends Mage_Core_Model_Abstract
{
    const LOG_FILE = 'fpc.log';

    protected function _construct()
    {
        $this->_init('fpc/log');
    }

    /**
     * @param Mirasvit_Fpc_Model_Storagemf $storage
     * @param string $loadType
     * @return void
     */
    public static function log($storage, $loadType)
    {
        $sessionHelper = new Mirasvit_Fpc_Helper_Fpcmf_Sessionmf();

        if (Mage::helper('core/http')->getHttpUserAgent() == 'FpcCrawler'
            || (($storeId = $sessionHelper->getStoreId()) && !Mage::getStoreConfig('fpc/general/enabled', $storeId)) ) {
                return;
        }

        if ($loadType == 'hit') {
            $loadType = 1;
        } elseif ($loadType == 'miss') {
            $loadType = 0;
        }

        $data = array(
            $loadType,
            round(microtime(true) - $_SERVER['FPC_TIME'], 5),
            $sessionHelper->getUrl(),
            $storage->getCacheId(),
            $storage->getRequestRouteName().'/'.$storage->getRequestControllerName().'_'.$storage->getRequestActionName(),
            1000,
            0,
            $sessionHelper->getStoreId(),
            $sessionHelper->getCurrency(),
            Mirasvit_FpcCrawler_Model_Config::COMPUTER_GROUP,
        );

        Mage::log(implode('|', $data), null, Mirasvit_Fpc_Model_Configmf::LOG_FILE, true);
    }
}
