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


class Mirasvit_AsyncIndex_Model_Processing_PerIndex extends Mirasvit_AsyncIndex_Model_Processing_Abstract
{
    public function reindexQueue()
    {
        foreach ($this->getProcessCollection() as $process) {
            $unprocessedEventsCount = $process->getUnprocessedEventsCollection()->getSize();

            if ($unprocessedEventsCount && !$process->isLocked()) {
                $uid = $this->_helper->start('Partial reindex of the index "'.$process->getIndexer()->getName().'"');

                $result = $this->execute('reindexQueueIndex', array($uid, $process->getId()), true);
            }
        }

        // Ñ.Ðµ. Ð¼Ñ Ð´ÐµÑÐ¶Ð¸Ð¼ Ð»Ð¾Ðº Ð¿Ð¾ÐºÐ° Ð²ÑÐµ Ð¸Ð½Ð´ÐµÐºÑÑ Ð½Ðµ ÑÐ°Ð·Ð»Ð¾ÑÐ°ÑÑÑÑ
        $collection = $this->getProcessCollection();
        $lock = true;
        while ($lock == true) {
            sleep(1);
            $lock = false;
            foreach ($collection as $process) {
                if ($process->isLocked()) {
                    $lock = true;
                }
            }
        }
    }

    public function reindexQueueIndex($uid, $indexId)
    {
        $process = Mage::getModel('index/process')->load($indexId);

        if (!$process->isLocked() && $process->getUnprocessedEventsCollection()->getSize()) {
            $process->setMode(Mage_Index_Model_Process::MODE_REAL_TIME);
            $process->setStatus('pending');

            $process->lock();
            $process->getResource()->startProcess($process);

            $collection = $process->getUnprocessedEventsCollection();
            $collection->getSelect()->limit(10000)->order('rand()');

            foreach ($collection as $event) {
                $process->fastProcessEvent($event);
                $event->save();
            }

            $process->getResource()->endProcess($process);
            $process->unlock();
        }

        $this->_helper->finish($uid);
    }
}