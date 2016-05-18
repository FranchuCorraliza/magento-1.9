<?php
/**
 * MageWorx
 * Admin Order Grid  extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersGrid
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersGrid_Model_Order_Group extends Mage_Core_Model_Abstract {

    protected function _construct() {
        parent::_construct();
        $this->_init('mageworx_ordersgrid/order_group');

        if (!$this->getId()) {
            $this->setId(0);
        }
    }

    public function setGroupToOrders($orderIds)
    {
        Varien_Profiler::start('mw_setGroupToOrders');
        /** @var Varien_Db_Adapter_Interface $connection */
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $tablePrefix = (string)Mage::getConfig()->getTablePrefix();

        foreach ($orderIds as $orderId) {
            $connection->update($tablePrefix . 'sales_flat_order_grid', array('order_group_id' => intval($this->getId())), 'entity_id = ' . intval($orderId));
            $connection->update($tablePrefix . 'sales_flat_order', array('order_group_id' => intval($this->getId())), 'entity_id = ' . intval($orderId));
        }
        Varien_Profiler::stop('mw_setGroupToOrders');
    }
}