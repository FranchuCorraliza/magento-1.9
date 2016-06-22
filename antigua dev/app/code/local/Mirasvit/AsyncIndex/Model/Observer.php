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
 * Extension Observer
 *
 * @category Mirasvit
 * @package  Mirasvit_AsyncIndex
 */
class Mirasvit_AsyncIndex_Model_Observer
{
    /**
     * Run of execution:
     * full reindex
     * queue reindex
     * valation
     *
     * @return object
     */
    public function process()
    {
        if (!Mage::getSingleton('asyncindex/config')->isCronjobAllowed()) {
            return $this;
        }

        error_reporting(E_ALL);
        ini_set('error_reporting', E_ALL);
        ini_set('max_execution_time', 360000);
        set_time_limit(360000);

        $control = Mage::getModel('asyncindex/control');
        $control->run();

        return $this;
    }

    /**
     * Store changed, clear queue - need run full reindex
     *
     * @return object
     */
    public function onStoreSaveAfter()
    {
        $this->_clearQueue();

        return $this;
    }

    protected function _clearQueue()
    {
        $resource   = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('core_write');

        $connection->query('DELETE FROM '.$resource->getTableName('index/event'));

        return true;
    }
}