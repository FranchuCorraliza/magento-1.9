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
require_once 'Mage/Adminhtml/controllers/Sales/OrderController.php';

class Soon_StockReleaser_Adminhtml_Sales_Order_CanceledController extends Mage_Adminhtml_Sales_OrderController {

    /**
     * Orders grid
     * 
     * @return void
     */
    public function indexAction() {
        $this->_title($this->__('Sales'))->_title($this->__('Automatically Canceled Orders'));

        $this->_initAction()
                ->renderLayout();
    }

    /**
     * Export order grid to CSV format
     * 
     * @return void
     */
    public function exportCsvAction() {
        $fileName = 'canceled_orders.csv';
        $grid = $this->getLayout()->createBlock('stockreleaser/adminhtml_sales_order_canceled_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     *  Export order grid to Excel XML format
     * 
     * @return void
     */
    public function exportExcelAction() {
        $fileName = 'canceled_orders.xml';
        $grid = $this->getLayout()->createBlock('stockreleaser/adminhtml_sales_order_canceled_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }

}
