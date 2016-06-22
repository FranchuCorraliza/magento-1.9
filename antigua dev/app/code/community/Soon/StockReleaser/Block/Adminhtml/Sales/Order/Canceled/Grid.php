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
class Soon_StockReleaser_Block_Adminhtml_Sales_Order_Canceled_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('sales_order_canceled_grid');
        $this->setDefaultSort('autocancel_date');
    }

    /**
     * Prepare collection with automatically canceled orders
     * 
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection() {
        $collection = Mage::getResourceModel($this->_getCollectionClass());

        $collection->getSelect()->joinLeft(array('autocancel_order' => 'stockreleaser_cancel'), 'autocancel_order.order_id=main_table.entity_id');
        $collection->addFieldToFilter('autocancel_order.autocancel_status', 1);

        $this->setCollection($collection);

        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

    /**
     * Update existing colums from Mage_Adminhtml_Block_Sales_Order_Grid
     * 
     * @return Mage_Adminhtml_Block_Widget_Grid 
     */
    protected function _prepareColumns() {
        parent::_prepareColumns();

        $this->addColumnAfter('autocancel_date', array(
            'header' => Mage::helper('stockreleaser')->__('Canceled On'),
            'index' => 'autocancel_date',
            'type' => 'datetime',
            'width' => '100px',
                ), 'created_at');

        if (version_compare(Mage::getVersion(), '1.6.0.0', '>=')){
            $this->removeColumn('status');
        }
        else {
            $this->removeColumnAlt('status');
        }

        $this->_updateActions();

        return Mage_Adminhtml_Block_Widget_Grid::_prepareColumns();
    }
    
    /**
     * @deprecated since version 1.6.0.0 see Mage_Adminhtml_Block_Widget_Grid::removeColmun()
     * 
     * Remove existing column
     *
     * @param string $columnId
     * @return Soon_StockReleaser_Block_Adminhtml_Sales_Order_Canceled_Grid
     */
    public function removeColumnAlt($columnId)
    {        
        if (isset($this->_columns[$columnId])) {
            unset($this->_columns[$columnId]);
            if ($this->_lastColumnId == $columnId) {
                $this->_lastColumnId = key($this->_columns);
            }
        }
        return $this;
    }    

    /**
     * Change the link to open order view
     * 
     * @return Soon_StockReleaser_Block_Adminhtml_Sales_Order_Canceled_Grid
     */
    protected function _updateActions() {
        $actionColumn = $this->_columns['action'];
        $actions = $actionColumn->getActions();
        $newBaseUrl = '*/sales_order/view' . '/rel/' . true; // We add the /rel/true param to flag the referring
        
        $actions[0]['url']['base'] = $newBaseUrl;
        $actionColumn->setActions($actions);

        return $this;
    }

    /**
     * Empty this extended method in order to remove mass action block
     * which usually enables options that are of no use in the current grid
     * 
     * @return Soon_StockReleaser_Block_Adminhtml_Sales_Order_Canceled_Grid
     */
    protected function _prepareMassaction() {
        return $this;
    }
    
    
    /**
     * Retrieve row to view order.
     * 
     * @return mixed string|bool
     */
    public function getRowUrl($row)
    {        
        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            return $this->getUrl('*/sales_order/view', array('order_id' => $row->getId(), 'rel' => true));
        }
        return false;
    }    

}
