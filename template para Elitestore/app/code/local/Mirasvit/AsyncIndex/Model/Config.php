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
 * @category Mirasvit
 * @package  Mirasvit_AsyncIndex
 */
class Mirasvit_AsyncIndex_Model_Config
{
    const STATUS_OK                        = 'OKAY';

    const XML_PATH_FULL_REINDEX            = 'asyncindex/general/full_reindex';
    const XML_PATH_CHANGE_REINDEX          = 'asyncindex/general/change_reindex';
    const XML_PATH_PROCESSING_MODE         = 'asyncindex/general/processing_mode';
    const XML_PATH_VALIDATE_PRODUCT_INDEX  = 'asyncindex/general/validate_product_index';
    const XML_PATH_VALIDATE_CATEGORY_INDEX = 'asyncindex/general/validate_category_index';
    const XML_PATH_QUEUE_BATCH_SIZE        = 'asyncindex/general/queue_batch_size';
    const XML_PATH_CRONJOB                 = 'asyncindex/general/cronjob';
    const XML_PATH_IGNORED_INDEX           = 'asyncindex/general/ignored_index';


    public function isFullReindexAllowed()
    {
        return (bool) Mage::getStoreConfig(self::XML_PATH_FULL_REINDEX);
    }

    public function isChangeReindexAllowed()
    {
        return (bool) Mage::getStoreConfig(self::XML_PATH_CHANGE_REINDEX);
    }

    public function getProcessingMode()
    {
        return Mage::getStoreConfig(self::XML_PATH_PROCESSING_MODE);
    }

    public function isProductValidationAllowed()
    {
        return (bool) Mage::getStoreConfig(self::XML_PATH_VALIDATE_PRODUCT_INDEX);
    }

    public function isCategoryValidationAllowed()
    {
        return (bool) Mage::getStoreConfig(self::XML_PATH_VALIDATE_CATEGORY_INDEX);
    }

    public function getQueueBatchSize()
    {
        return intval(Mage::getStoreConfig(self::XML_PATH_QUEUE_BATCH_SIZE));
    }

    public function isCronjobAllowed()
    {
        return (bool) Mage::getStoreConfig(self::XML_PATH_CRONJOB);
    }

    public function getIgnoredIndexes()
    {
        $indexes = explode(',', Mage::getStoreConfig(self::XML_PATH_IGNORED_INDEX));

        return $indexes;
    }
}