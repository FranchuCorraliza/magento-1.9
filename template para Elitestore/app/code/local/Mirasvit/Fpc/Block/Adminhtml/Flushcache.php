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



class Mirasvit_Fpc_Block_Adminhtml_Flushcache extends Mage_Adminhtml_Block_Template
{
    public function getStoresData()
    {
        $storesData = array();
        $stores = Mage::app()->getStores();

        foreach ($stores as $store)
        {
            if ($store->getIsActive()) {
                $storesData[$store->getId()] = $store;
            }
        }

        return $storesData;
    }

}
