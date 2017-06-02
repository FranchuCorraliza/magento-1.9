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


abstract class Mirasvit_AsyncIndex_Model_Processing_Abstract
{
    protected $_shellScript  = null;
    protected $_phpBin       = null;
    protected $_helper       = null;
    protected $_controlModel = null;
    protected $_eeCache      = false;
    protected $_eeIndex      = false;

    abstract public function reindexQueue();

    public function __construct()
    {
        $modules = (array) Mage::getConfig()->getNode('modules')->children();
        if (isset($modules['Enterprise_PageCache'])) {
            $this->_eeCache = true;
        }

        if (isset($modules['Enterprise_Index']) && isset($modules['Enterprise_Mview'])) {
            $this->_eeIndex = true;
        }

        $this->_helper      = Mage::helper('asyncindex');
        $this->_shellScript = Mage::getBaseDir().DS.'shell'.DS.'asyncindex.php';
        $this->_phpBin      = $this->_helper->getPhpBin();
    }

    public function setControl($control)
    {
        $this->_controlModel = $control;
    }

    public function getControl()
    {
        return $control;
    }

    public function getProcessCollection()
    {
        return Mage::getModel('index/process')->getCollection();
    }

    public function fullReindex()
    {
        $collection = Mage::getModel('index/process')->getCollection()
            ->addFieldToFilter('status', array('', Mirasvit_AsyncIndex_Model_Process::STATUS_WAIT));

        foreach ($collection as $process) {
            $process = $process->load($process->getId());
            if (($process->getStatus() == ''|| $process->getStatus() == Mirasvit_AsyncIndex_Model_Process::STATUS_WAIT)
                && !$process->isLocked()) {

                $uid = $this->_helper->start(sprintf(__('Full reindex "%s"'), $process->getIndexer()->getName()));

                $result = $this->execute('reindexIndex', array($uid, $process->getId()), false);

                if ($result !== Mirasvit_AsyncIndex_Model_Config::STATUS_OK) {
                    $this->_helper->error($uid, $result);
                } else {
                    $this->_helper->finish($uid);
                }
            }
        }

        return $this;
    }

    public function reindexIndex($uid, $indexId)
    {
        $process = Mage::getModel('index/process')->load($indexId);
        $process->getResource()->updateStatus($process, Mage_Index_Model_Process::STATUS_PENDING, true);
        $process->reindexAll(true);

        return Mirasvit_AsyncIndex_Model_Config::STATUS_OK;
    }

    public function execute($method, $args = array(), $async = false)
    {
        $result = true;

        if (!is_array($args)) {
            $args = array($args);
        }

        //if exec
        if ($this->ping()) {
            $cmd = "$this->_phpBin $this->_shellScript --control --method $method --class ".get_class($this);
            $cmd .= ' --args '.implode(',', $args);
            if ($async == true) {
                $cmd .= ' --async 1 > /dev/null 2>&1 &';
            } else {
                $cmd .= ' --async 0';
            }

            Mage::log($cmd, null, 'exec.log');
            exec($cmd, $result);
            $result = implode(PHP_EOL, $result);
        } else {
            $result = call_user_func_array(array($this, $method), $args);
        }

        Mage::log($result, null, 'exec.log');
        return $result;
    }

    /**
     * ÐÑÐ¸ÑÐ°ÐµÐ¼ ÐºÐµÑ Ð¸ÑÑÐ¾Ð´Ñ Ð¸Ð· ÑÐ¾Ð±ÑÑÐ¸Ñ (ÑÐ»ÐµÐ¼ÐµÐ½ÑÐ° Ð² Ð¾ÑÐµÑÐµÐ´Ð¸)
     * @todo ÑÐµÑÐ°ÐºÑÐ¾ÑÐ¸Ð½Ð³
     *
     * @param  object  $event
     * @param  boolean $clean
     *
     * @return object
     */
    protected function _clearCache($event, $clean = false)
    {
        if ($event != null) {
            $cacheTag = $event->getData('entity').'_'.$event->getData('entity_pk');
            $this->_cleanTags[] = $cacheTag;
        }

        if ($clean && count($this->_cleanTags)) {
            foreach ($this->_cleanTags as $idx => $tag) {
                $this->_cleanTags[$idx] = strtoupper($tag);
            }

            Mage::app()->getCache()->clean('matchingAnyTag', $this->_cleanTags);

            if ($this->_eeCache) {
                $cacheInstance = Enterprise_PageCache_Model_Cache::getCacheInstance();
                $cacheInstance->clean($this->_cleanTags);
            }
        }

        return $this;
    }

    /**
     * ÐÐ° Ð¾ÑÐ½Ð¾Ð²Ð°Ð½Ð¸Ð¸ Ð¾Ð±ÑÐ°Ð±Ð¾ÑÐ°Ð½Ð¾Ð³Ð¾ ÑÐ¾Ð±ÑÑÐ¸Ñ (ÐµÐ»ÐµÐ¼ÐµÐ½ÑÐ° Ð¾ÑÐµÑÐµÐ´Ð¸)
     * Ð¿ÑÐ¸Ð¼ÐµÐ½ÑÐ¼ Catalog Price Rules Ð´Ð»Ñ ÑÐ¾Ð²Ð°ÑÐ°
     * *ÐµÑÐ»Ð¸ ÑÐ¾Ð±ÑÑÐ¸Ðµ Ð±ÑÐ»Ð¾ ÑÐ¾ÑÑÐ°Ð½ÐµÐ½Ð¸Ðµ ÑÐ¾Ð²Ð°ÑÐ°
     *
     * @param  object $event
     *
     * @return object
     */
    protected function _applyPriceRule($event)
    {
        $version = Mage::getVersionInfo();
        if ($event->getEntity() == 'catalog_product'
            && $event->getType() == 'save'
            && $event->getEntityPk()) {
            $productId = $event->getEntityPk();
            Mage::getSingleton('catalogrule/rule')->applyAllRulesToProduct($productId, true);
        }

        return $this;
    }

    public function ping()
    {
        return false;
        $output = array();
        exec("$this->_phpBin $this->_shellScript --ping", $output);
        $output = implode(PHP_EOL, $output);

        if ($output === Mirasvit_AsyncIndex_Model_Config::STATUS_OK) {
            return true;
        }

        return false;
    }
}