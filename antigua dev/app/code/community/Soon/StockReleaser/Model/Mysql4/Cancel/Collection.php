<?php

/**
 * Agence Soon
 *
 * @category    Soon
 * @package     Soon_StockReleaser
 * @copyright   Copyright (c) 2011 Agence Soon. (http://www.agence-soon.fr)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author      Hervé Guétin
 */
class Soon_StockReleaser_Model_Mysql4_Cancel_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    public function _construct() {
        $this->_init('stockreleaser/cancel');
    }

    /**
     * Filter collection with orders that have been automatically canceled
     * 
     * @return Soon_StockReleaser_Model_Mysql4_Cancel_Collection 
     */
    public function addIsCanceledFilter() {
        $this->addFieldToFilter('autocancel_status', 1);
        return $this;
    }

    /**
     * Filter collection with orders that have not been canceled yet
     * 
     * @return Soon_StockReleaser_Model_Mysql4_Cancel_Collection 
     */
    public function addIsNotCanceledFilter() {
        $this->addFieldToFilter('autocancel_status', 0);
        return $this;
    }

    /**
     * Retrieve collection of orders that have not been automatically canceled
     * but that are completed
     * 
     * @return Soon_StockReleaser_Model_Mysql4_Cancel_Collection 
     */
    public function getCompletedOrders() {
        $this->addIsNotCanceledFilter()
                ->getSelect()
                ->join(array('order_table' => $this->getTable('sales/order')), 'main_table.order_id = order_table.entity_id');
        $this->addFieldToFilter('state', Mage_Sales_Model_Order::STATE_COMPLETE);
        return $this;
    }

}
