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



class Mirasvit_Fpc_Model_Observer_MassSaveCacheClear extends Mirasvit_Fpc_Model_Observer_CacheClear
{

    /**
     * Flush cache after mass product save if use minimal set of tags
     *
     * @param Varien_Event_Observer $e
     * @return void
     */
    public function updateCache($e)
    {
        if ($this->_cacheTagsLevel != Mirasvit_Fpc_Model_Config::CACHE_TAGS_LEVEL_MINIMAL
            && $this->_cacheTagsLevel != Mirasvit_Fpc_Model_Config::CACHE_TAGS_LEVEL_MINIMAL_PREFIX) {
            return true;
        }

        $productIds = $e->getProductIds();

        if (!$productIds) {
            return true;
        }

        foreach ($productIds as $productId) {
            $tags = array();
            $tags[] = $this->_prefix . Mirasvit_Fpc_Model_Config::CATALOG_PRODUCT_TAG . $productId;
            $product = Mage::getModel('catalog/product')->load($productId);
            if ($product->getVisibility() == Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE) {
                continue;
            }

            $tags = $this->getCategoryTags($product, $tags);

            if ($tags) {
                $tags = array_unique($tags);
                Mage::app()->getCache()->clean('matchingAnyTag', $tags);
            }
        }
    }
}
