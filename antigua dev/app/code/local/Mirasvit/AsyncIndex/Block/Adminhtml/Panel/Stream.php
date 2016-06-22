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
 * ÐÐ»Ð¾Ðº Ð²ÑÐ²Ð¾Ð´Ð° Ð¿Ð¾ÑÐ¾ÐºÐ° (Ð¼Ð°ÑÐ¸Ð²Ð°) ÑÐ¾Ð¾Ð±ÑÐµÐ½Ð¸Ð¹,
 * ÐºÐ¾ÑÐ¾ÑÑÐµ Ð¿Ð¸ÑÑÑÑÑ Ð²Ð¾ Ð²ÑÐµÐ¼Ñ ÑÐ°Ð±Ð¾ÑÑ Ð¼Ð¾Ð´ÑÐ»Ñ
 *
 * @category Mirasvit
 * @package  Mirasvit_AsyncIndex
 */
class Mirasvit_AsyncIndex_Block_Adminhtml_Panel_Stream extends Mage_Adminhtml_Block_Template
{
    protected $_stream     = null;
    protected $_splitQueue = null;

    public function _prepareLayout()
    {
        $this->setTemplate('asyncindex/panel/stream.phtml');

        return parent::_prepareLayout();
    }

    /**
     * ÐÐ¾Ð·Ð²ÑÐ°ÑÐ°ÐµÑ Ð¿Ð¾Ð´Ð³Ð¾ÑÐ¾Ð²Ð»ÐµÐ½Ð½ÑÐ¹ Ð¼Ð°ÑÐ¸Ð² ÑÐµÐºÑÑÐµÐ¹ Ð¾ÑÐµÑÐµÐ´Ð¸ (5 ÐµÐ»ÐµÐ¼ÐµÐ½ÑÐ¾Ð²)
     *
     * @return array
     */
    public function getQueue()
    {
        $ts    = microtime(true);
        $queue = array();

        foreach ($this->getProcessCollection() as $process) {
            if ($process->getStatus() == Mirasvit_AsyncIndex_Model_Process::STATUS_WAIT) {
                $queue[] = 'Full reindex "'.$process->getIndexer()->getName().'"';
            }
        }

        $collection = $this->getIndexEventCollection();
        foreach ($collection as $event) {
            $status = 'new';
            if ($event->getId() == Mage::helper('asyncindex')->getVariable('current_event')) {
                $status = 'processing';
            }

            $event->setStatus($status);

            $queue[] = $event;
        }

        return $queue;
    }

    public function splitQueue()
    {
        if ($this->_splitQueue == null) {
            $this->_splitQueue = array();

            $processes = Mage::getSingleton('index/indexer')->getProcessesCollection();

            $collection = Mage::getModel('index/event')->getCollection()
                ->addProcessFilter($processes->getAllIds(), 'new');
            $collection->getSelect()
                ->group('main_table.entity')
                ->group('main_table.type')
                ->columns(array(
                    'cnt_event'         => 'COUNT(DISTINCT(main_table.event_id))',
                    'cnt_process_event' => 'COUNT(process_event.event_id)'
                ));

            foreach ($collection as $event) {
                $entity = $event->getEntity();
                $type   = $event->getType();

                $this->_splitQueue[$entity][$type]['events']    = $event->getCntEvent();
                $this->_splitQueue[$entity][$type]['processes'] = $event->getCntProcessEvent();
            }
        }

        return $this->_splitQueue;
    }

    public function getQueueSize()
    {
        $size = 0;
        foreach ($this->splitQueue() as $entity => $types) {
            foreach ($types as $type => $counts) {
                if ($entity == 'catalog_proudct' && $type == 'save') {
                    $size += $counts['events'] * 2;
                } else {
                    $size += $counts['events'];
                }
            }
        }

        return $size;
    }

    /**
     * ÐÐ¾Ð·Ð²ÑÐ°ÑÐ°ÐµÑ ÐºÐ¾Ð»Ð»ÐµÐºÑÐ¸Ñ ÑÐµÐºÑÑÐµÐ¹ Ð¾ÑÐµÑÐµÐ´Ð¸
     *
     * @return object
     */
    public function getIndexEventCollection()
    {
        $processes = Mage::getSingleton('index/indexer')->getProcessesCollection();

        $collection = Mage::getModel('index/event')->getCollection()
            ->addProcessFilter($processes->getAllIds(), Mage_Index_Model_Process::EVENT_STATUS_NEW);
        $collection->getSelect()
            ->limit(5)
            ->group('entity_pk')
            ->group('entity')
            ->order('created_at asc');

        return $collection;
    }

    public function ucString($string)
    {
        $string = uc_words($string);
        $string = str_replace('_', ' ', $string);

        return $string;
    }

    /**
     * ÐÐ¾Ð·Ð²ÑÐ°ÑÐ°ÐµÑ Ð¼Ð°ÑÐ¸Ð² ÑÐ¾Ð¾Ð±ÑÐµÐ½Ð¸Ðµ (ÑÐ¶Ðµ Ð¿ÑÐ¾Ð¸Ð½Ð´ÐµÐºÑÐ¸ÑÐ¾Ð²Ð°Ð½Ð½ÑÐµ ÑÐ»ÐµÐ¼ÐµÐ½ÑÑ)
     *
     * @return array
     */
    public function getStream()
    {
        if ($this->_stream == null) {
            $this->_stream = array();

            $collection = Mage::getModel('mstcore/logger')->getCollection()
                ->addFieldToFilter('module', 'AsyncIndex')
                ->setOrder('log_id', 'asc')
                ->setPageSize(1000);

            foreach ($collection as $log) {
                $info = @unserialize($log->getContent());

                $item = new Varien_Object();

                $item->setId($log->getId());
                $item->setTitle(@$info['text']);
                $item->setStatus(@$info['status']);
                $item->setChilds(new Varien_Data_Collection());

                if (!isset($info['finished_at'])) {
                    $info['finished_at'] = microtime(true);
                }

                $item->setProcessingTime(@$info['finished_at'] - @$info['created_at']);

                if (isset($info['message'])) {
                    $item->setMessage($info['message']);
                    $item->setTitle($item->getTitle().PHP_EOL.$item->getMessage());
                }

                if (@$info['status'] != 'start') {
                    $since = Mage::helper('asyncindex')->timeSince(microtime(true) - $info['finished_at']);
                    $item->setSince($since.' ago');
                }

                if (isset($info['parent_id']) && isset($this->_stream[$info['parent_id']])) {
                    $this->_stream[$info['parent_id']]->getChilds()->addItem($item);
                } else {
                    $this->_stream[$item->getId()] = $item;
                }
            }

            $this->_stream = array_reverse($this->_stream);
        }

        if ($this->getStatus() == 'waiting' || $this->getStatus() == 'success') {
            foreach ($this->_stream as $idx => $itm) {
                if ($this->_stream[$idx]->getStatus() == 'start') {
                    $this->_stream[$idx]->setStatus('error')
                        ->setProcessingTime(0);
                }
            }
        }

        return $this->_stream;
    }

    /**
     * Ð¢ÐµÐºÑÑÐ¸Ð¹ ÑÑÐ°ÑÑÑ Ð¼Ð¾Ð´ÑÐ»Ñ
     * success - Ð¾ÑÐµÑÐµÐ´Ñ Ð¿ÑÑÑÐ°
     * waiting - Ð¶Ð´ÐµÑ Ð·Ð°Ð¿ÑÑÐºÐ°
     * processing - ÑÐ°Ð±Ð¾ÑÐ°ÐµÑ (Ð¾Ð±ÑÐ°Ð±Ð°ÑÑÐ²Ð°ÐµÑ Ð¾ÑÐµÑÐµÐ´Ñ, ÑÐµÐ¸Ð½Ð´ÐµÐºÑ Ð¿ÑÐ¾Ð²ÐµÑÐºÑ)
     * error - Ð¿ÑÐ¾Ð¸Ð·Ð¾ÑÐ»Ð° Ð¾ÑÐ¸Ð±ÐºÐ° Ð²Ð¾ Ð²ÑÐµÐ¼Ñ Ð¿Ð¾ÑÐ»ÐµÐ´Ð½ÐµÐ³Ð¾ Ð·Ð°Ð¿ÑÑÐºÐ°
     *
     * @return string
     */
    public function getStatus()
    {
        $helper = Mage::helper('asyncindex');
        $status = 'success';

        if ($this->getIndexEventCollection()->getSize() > 0) {
            $status = 'waiting';
        }

        if ($helper->isProcessing()) {
            $status = 'processing';
        }

        return $status;
    }

    public function getErrorMessage()
    {
        $job = Mage::getModel('cron/schedule')->getCollection()
            ->addFieldToFilter('job_code', 'asyncindex')
            ->addFieldToFilter('status', 'error')
            ->setOrder('scheduled_at', 'desc')
            ->getFirstItem();
        if ($job->getId()) {
            return $job->getMessages();
        }

        $log = Mage::getModel('mstcore/logger')->getCollection()
            ->addFieldToFilter('module', 'AsyncIndex')
            ->addFieldToFilter('level', 16)
            ->setOrder('created_at', 'desc')
            ->getFirstItem();
        if ($log->getId()) {
            return nl2br($log->getMessage()."\n".$log->getContent());
        }
    }

    /**
     * Ð¡ÐºÐ¾Ð»ÑÐºÐ¾ Ð¿ÑÐ¾ÑÐ»Ð¾ Ð²ÑÐµÐ¼ÐµÐ½Ð¸ Ñ Ð¼Ð¾Ð¼ÐµÐ½ÑÐ° Ð·Ð°Ð¿ÑÑÐºÐ° Ð¾Ð±ÑÐ°Ð±Ð¾ÑÐºÐ¸ Ð¾ÑÐµÑÐµÐ´Ð¸
     *
     * @return string
     */
    public function getProcessingTime()
    {
        $startTime = Mage::helper('asyncindex')->getVariable('start_time');

        return Mage::helper('asyncindex')->timeSince(Mage::getSingleton('core/date')->gmtTimestamp() - $startTime);
    }

    /**
     * ÐÐ¾Ð»Ð»ÐµÐºÑÐ¸Ñ Ð¸Ð½Ð´ÐµÐºÑÐ¾Ð² magento
     *
     * @return object
     */
    public function getProcessCollection()
    {
        return Mage::getModel('index/process')->getCollection();
    }

    public function getInvalidProductCount()
    {
        $cnt = Mage::helper('asyncindex')->getVariable('invalid_product_count');

        return intval($cnt);
    }

    public function getInvalidCategoryCount()
    {
        $cnt = Mage::helper('asyncindex')->getVariable('invalid_category_count');

        return intval($cnt);
    }
}