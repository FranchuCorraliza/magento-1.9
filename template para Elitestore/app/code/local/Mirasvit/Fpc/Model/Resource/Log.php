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



class Mirasvit_Fpc_Model_Resource_Log extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('fpc/log', 'log_id');
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if ($object->isObjectNew() && !$object->hasCreatedAt()) {
            $object->setCreatedAt(Mage::getSingleton('core/date')->gmtDate());
        }

        return parent::_beforeSave($object);
    }

    public function aggregate()
    {
        $adapter = $this->_getWriteAdapter();
        $readAdapter = $this->_getReadAdapter();

        try {
            $adapter->delete($this->getTable('fpc/log_aggregated_daily'));

            $periodExpr = new Zend_Db_Expr(sprintf('DATE(%s)', 'created_at'));

            $select = $adapter->select();

            $select->group(array(
                $periodExpr,
                'from_cache',
            ));

            $columns = array(
                'period' => $periodExpr,
                'response_time' => new Zend_Db_Expr('AVG(response_time)'),
                'hits' => new Zend_Db_Expr('COUNT(log_id)'),
                'from_cache' => new Zend_Db_Expr('SUM(from_cache)'),
            );

            $select->from(array('source_table' => $this->getMainTable()), $columns);

            $select->useStraightJoin();

            $insertQuery = $select->insertFromSelect($this->getTable('fpc/log_aggregated_daily'),
                array_keys($columns));
            $adapter->query($insertQuery);

            if (($tableName = Mage::getSingleton('core/resource')->getTableName('fpc/log_aggregated')) //check if table m_fpc_log_aggregated exist
                && $adapter->showTableStatus($tableName) !== false) {
                    $insertQuery = 'INSERT INTO ' . $this->getTable('fpc/log_aggregated')
                                    . ' (period,from_cache,response_time,hits) SELECT period,from_cache,response_time,hits FROM '
                                    . $this->getTable('fpc/log_aggregated_daily')
                                    . ' WHERE from_cache > 0 AND period NOT IN (SELECT period FROM (SELECT period,from_cache FROM '
                                    . $this->getTable('fpc/log_aggregated') . ' WHERE from_cache > 0) AS aggreg); '
                                    . 'INSERT INTO ' . $this->getTable('fpc/log_aggregated')
                                    . ' (period,from_cache,response_time,hits) SELECT period,from_cache,response_time,hits FROM '
                                    . $this->getTable('fpc/log_aggregated_daily')
                                    . ' WHERE from_cache = 0 AND period NOT IN (SELECT period FROM (SELECT period,from_cache FROM '
                                    . $this->getTable('fpc/log_aggregated') . ' WHERE from_cache = 0) AS aggreg);';

                    $adapter->query($insertQuery);

                    $query = 'SELECT * FROM ' . $this->getTable('fpc/log_aggregated_daily') . ' WHERE period IN (SELECT period FROM ' . $this->getTable('fpc/log_aggregated') . ') ORDER BY period,from_cache ASC';
                    $resultsAggregatedDaily = $readAdapter->fetchAll($query);

                    $query = 'SELECT * FROM ' . $this->getTable('fpc/log_aggregated') . ' WHERE period IN (SELECT period FROM ' . $this->getTable('fpc/log_aggregated_daily') . ') ORDER BY period,from_cache ASC';
                    $resultsAggregated = $readAdapter->fetchAll($query);

                    $updateAggregated = array();
                    if ($resultsAggregated && $resultsAggregatedDaily
                        && count($resultsAggregated) == count($resultsAggregatedDaily)) {
                        foreach ($resultsAggregated as $key => $value) {
                            $updateAggregated[$value['id']] = array(
                                'id' => $value['id'],
                                'period' => $value['period'],
                                'from_cache' => $value['from_cache'],
                                'response_time' => ($value['response_time'] + $resultsAggregatedDaily[$key]['response_time']) / 2,
                                'hits' => $value['hits'] + $resultsAggregatedDaily[$key]['hits'],
                            );
                        }
                    }

                    if ($updateAggregated) {
                        foreach ($updateAggregated as $id => $value) {
                            $where = $adapter->quoteInto('id = ?', $id);
                            $adapter->update($this->getTable('fpc/log_aggregated'),
                                $updateAggregated[$id],
                                $where
                            );
                        }
                    }

                    $adapter->delete($this->getTable('fpc/log_aggregated_daily'));
                    $adapter->delete($this->getTable('fpc/log'));
            }
        } catch (Exception $e) {
            throw $e;
        }

        return $this;
    }
}
