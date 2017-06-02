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



class Mirasvit_AsyncIndex_Model_Processing_PerEntity extends Mirasvit_AsyncIndex_Model_Processing_Abstract
{
    public function reindexQueue()
    {
        $processes = $this->getProcessCollection();


        foreach ($processes as $process) {
            $process->setStatus('pending')->save();
        }

        $indexer = Mage::getSingleton('index/indexer');

        while ($this->_getEventsCollection(1)->getFirstItem()->getId()) {
            $uid = $this->_helper->start('Reindex Queue');
            $result = $this->execute('processEvents', array(), false);

            if ($result !== Mirasvit_AsyncIndex_Model_Config::STATUS_OK) {
                $this->_helper->error($uid, $result);
            }

            $uid = $this->_helper->finish($uid);
        }

        if ($this->_eeIndex) {
            $uid = $this->_helper->start('Enterprise Index Refresh');
            Mage::getModel('enterprise_index/observer')->refreshIndex();
            $this->_helper->finish($uid);
        }
    }

    public function processEvents()
    {
        $processes = $this->getProcessCollection();

        foreach ($this->_getEventsCollection(100) as $event) {
            $this->_helper->setVariable('current_event', $event->getId());
            $eUid = $this->_helper->start($this->_helper->getEventDescription($event));

            try {
                foreach ($processes as $process) {
                    $pUid = $this->_helper->start($process->getIndexer()->getName(), $eUid);

                    try {
                        $process->fastProcessEvent($event);
                    } catch (Exception $e) {
                        $event->addProcessId($process->getId(), Mirasvit_AsyncIndex_Model_Process::EVENT_STATUS_DONE);

                        Mage::logException($e);
                    }

                    $this->_helper->finish($pUid);
                }

                $pUid = $this->_helper->start('Save Event', $eUid);
                $event->save();
                $this->_helper->finish($pUid);

                $pUid = $this->_helper->start('Apply Price Rules', $eUid);
                $this->_applyPriceRule($event);
                $this->_helper->finish($pUid);

                $pUid = $this->_helper->start('Clear Cache', $eUid);
                $this->_clearCache($event, true);
                $this->_helper->finish($pUid);

                $this->_helper->finish($eUid);
            } catch (Exception $e) {
                $this->_helper->error($eUid, $e);
            }
        }

        return Mirasvit_AsyncIndex_Model_Config::STATUS_OK;
    }

    protected function _getEventsCollection($limit = 1)
    {
        $processes = $this->getProcessCollection();

        $eventsCollection = Mage::getResourceModel('index/event_collection');
        $eventsCollection->addProcessFilter($processes->getAllIds(), Mage_Index_Model_Process::EVENT_STATUS_NEW);
        $eventsCollection->getSelect()
            ->group('entity_pk')
            ->group('entity')
            ->order('created_at asc')
            ->limit($limit);

        return $eventsCollection;
    }
}