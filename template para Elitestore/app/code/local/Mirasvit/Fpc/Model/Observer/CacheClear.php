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



abstract class Mirasvit_Fpc_Model_Observer_CacheClear
{
    /**
     * @var int
     */
    protected $_cacheTagsLevel;

    protected $_prefix;

    public function __construct()
    {
        $config = Mage::getSingleton('fpc/config');
        $this->_cacheTagsLevel = $config->getCacheTagslevelLevel();
        $this->_prefix = $config->getTagPrefix();
    }

    /**
     * Get category tags for current product
     *
     * @param object $product
     * @param array $tags
     * @return array
     */
    public function getCategoryTags($product, $tags)
    {
        if (!is_object($product)) {
            return $tags;
        }
        $categoryIds = $product->getCategoryIds();
        foreach ($categoryIds as $categoryId) {
            $tags[] = $this->_prefix . Mirasvit_Fpc_Model_Config::CATALOG_CATEGORY_TAG . $categoryId;
        }
        return $tags;
    }
}
