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


class Mirasvit_AsyncIndex_Block_Adminhtml_Notifications extends Mage_Adminhtml_Block_Template
{
    public function validateQueueSize()
    {
        $processes = Mage::getSingleton('index/indexer')->getProcessesCollection();

        $collection = Mage::getModel('index/event')->getCollection()
            ->addProcessFilter($processes->getAllIds(), 'new');
        $collection->getSelect()
            ->group('entity')
            ->group('entity_pk')
            ->order('created_at asc');

        $size = $collection->getSize();

        if ($size > Mage::getStoreConfig('asyncindex/general/danger_queue_size')) {
            return $size;
        }

        return true;
    }

    public function validateCronStatus()
    {
        return Mage::helper('asyncindex')->getCronStatus();
    }
}
