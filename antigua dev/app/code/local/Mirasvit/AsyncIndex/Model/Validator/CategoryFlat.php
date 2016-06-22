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


class Mirasvit_AsyncIndex_Model_Validator_CategoryFlat extends Mirasvit_AsyncIndex_Model_Validator_Abstract
{
    /**
     * ÐÐ°Ð»Ð¸Ð´Ð¸ÑÑÐµÐ¼ Ð¸Ð½Ð´ÐµÐºÑ ÐºÐ°ÑÐµÐ³Ð¾ÑÐ¸Ð¹, Ð¿ÑÑÐµÐ¼ ÑÑÐ°Ð²Ð½ÐµÐ½Ð¸Ñ updated_at
     * Ð² ÑÐ°Ð±Ð»Ð¸ÑÐµ catalog_category_entity Ð¸ catalog_category_flat_x
     * 
     * ÐÑÐ»Ð¸ Ð´Ð°ÑÑ Ð¾ÑÐ»Ð¸ÑÐ°ÑÑÑÑÑ, Ð´Ð¾Ð±Ð°Ð²Ð»ÑÐµÑ ÑÐ»ÐµÐ¼ÐµÐ½Ñ Ð² Ð¾ÑÐµÑÐµÐ´Ñ
     * ÐÐ°ÐºÑÐ¸Ð¼Ð°Ð»ÑÐ½Ð¾Ðµ Ð²Ð¾Ð·Ð¼Ð¾Ð¶Ð½Ð¾Ðµ ÐºÐ¾Ð»-Ð²Ð¾ Ð´Ð¾Ð±Ð°Ð²Ð»ÐµÐ½Ð½ÑÑ ÑÐ»ÐµÐ¼ÐµÐ½ÑÐ¾Ð²
     * Ð² Ð¾ÑÐµÑÐµÐ´Ñ ÑÐºÐ°Ð·ÑÐ²Ð°ÐµÑÑÑÑ Ð² Batch Size
     * 
     * ! Ð Ð°Ð±Ð¾ÑÐ°ÐµÑ ÑÐ¾Ð»ÑÐºÐ¾ ÐµÑÐ»Ð¸ Ð²ÐºÐ»ÑÑÐµÐ½ Flat Catalog Ð´Ð»Ñ ÐºÐ°ÑÐµÐ³Ð¾ÑÐ¸Ð¹
     * 
     * @return object
     */
    public function validate()
    {
        // ÐµÑÐ»Ð¸ Ð¾ÑÐºÐ»ÑÑÐµÐ½ ÑÐ»ÐµÑ ÐºÐ°ÑÐ°Ð»Ð¾Ð³
        if (!Mage::getStoreConfigFlag(Mage_Catalog_Helper_Category_Flat::XML_PATH_IS_ENABLED_FLAT_CATALOG_CATEGORY)) {
            return $this;
        }

        $resource     = Mage::getSingleton('core/resource');
        $adapter      = $resource->getConnection('core_write');
        $rootId       = Mage_Catalog_Model_Category::TREE_ROOT_ID;
        $stores       = Mage::app()->getStores();
        $invalidCount = 0;
        $bind         = array();

        foreach ($stores as $store) {
            $storeId   = $store->getId();
            $suffix    = sprintf('store_%d', $storeId);
            $flatTable = sprintf('%s_%s', $resource->getTableName('catalog/category_flat'), $suffix);

            $select = $adapter->select()
                ->from(array('e' => $resource->getTableName('catalog/category')), array('entity_id'))
                ->joinLeft(
                    array('flat' => $flatTable),
                    'e.entity_id = flat.entity_id',
                    array())
                ->where('flat.updated_at <> e.updated_at OR flat.updated_at IS NULL')
                ->where('e.path = "'.(string)$rootId.'" OR e.path = "'."{$rootId}/{$store->getRootCategoryId()}"
                        .'" OR e.path LIKE "'."{$rootId}/{$store->getRootCategoryId()}/%".'"');


            $countSelect = clone $select;
            $countSelect->reset(Zend_Db_Select::ORDER);
            $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
            $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
            $countSelect->reset(Zend_Db_Select::COLUMNS);
            $countSelect->columns('COUNT(*)');
            $invalidCount = max($invalidCount, intval($adapter->fetchOne($countSelect, $bind)));

            $result = $adapter->fetchAll($select, array());
            foreach ($result as $row) {
                $entityId = $row['entity_id'];

                if ($entityId == 1) {
                    continue;
                }

                $category = Mage::getModel('catalog/category')->load($entityId);

                Mage::getSingleton('index/indexer')->logEvent(
                    $category, Mage_Catalog_Model_Category::ENTITY, Mage_Index_Model_Event::TYPE_SAVE
                );
            }

            break;
        }

        if ($invalidCount > 0) {
            $invalidCount--;
        }

        Mage::helper('asyncindex')->setVariable('invalid_category_count', $invalidCount);

        return $this;
    }
}