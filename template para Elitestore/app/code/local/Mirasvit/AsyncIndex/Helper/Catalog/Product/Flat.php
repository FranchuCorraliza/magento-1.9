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
 * @package   Fast Asynchronous Re-indexing
 * @version   1.1.6
 * @build     285
 * @copyright Copyright (C) 2014 Mirasvit (http://mirasvit.com/)
 */


/**
 * ÐÐµÑÐµÐ¾Ð¿ÑÐµÐ´ÐµÐ»ÑÐµÐ¼ Ð´ÐµÑÐ¾Ð»ÑÐ½ÑÐ¹ ÑÐµÐ»Ð¿ÐµÑ, Ð´Ð»Ñ ÑÐ¾Ð³Ð¾ ÑÑÐ¾ Ð±Ñ Ð² Ð»ÑÐ±Ð¾Ð¼ ÑÐ»ÑÑÐ°Ðµ Ð¸ÑÐ¿Ð¾Ð»ÑÐ·Ð¾Ð²Ð°Ð»Ð¸ÑÑ flat ÑÐ°Ð±Ð»Ð¸ÑÑ (ÐµÑÐ»Ð¸ Ð¾Ð½Ð¸ Ð²ÐºÐ»ÑÑÐµÐ½Ñ) (catalog_product_flat)
 * Ð² Ð¾ÑÐ¸Ð³Ð¸Ð½Ð°Ð»Ðµ, ÐµÑÐ»Ð¸ Ð¸Ð½Ð´ÐµÐºÑ Ð½Ðµ Ð²Ð°Ð»Ð¸Ð´ÐµÐ½ - ÑÐ°Ð±Ð¾ÑÐ° Ð¸Ð´ÐµÑ Ñ ÑÐ°Ð±Ð»Ð¸ÑÐ°Ð¼Ð¸ catalog_product_entity
 *
 * @category Mirasvit
 * @package  Mirasvit_AsyncIndex
 */
class Mirasvit_AsyncIndex_Helper_Catalog_Product_Flat extends Mage_Catalog_Helper_Product_Flat
{
    public function isEnabled($store = null)
    {
        if (version_compare(Mage::getVersion(), '1.8.0.0', '>=')) {
            return parent::isEnabled($store);
        }

        $store = Mage::app()->getStore($store);
        if ($store->isAdmin()) {
            return false;
        }

        if (!isset($this->_isEnabled[$store->getId()])) {
            if (Mage::getStoreConfigFlag(self::XML_PATH_USE_PRODUCT_FLAT, $store)) {
                $this->_isEnabled[$store->getId()] = true;
            } else {
                $this->_isEnabled[$store->getId()] = false;
            }
        }

        return $this->_isEnabled[$store->getId()];
    }
}