<?php

/**
 * Agence Soon
 *
 * @category    Soon
 * @package     Soon_StockReleaser
 * @copyright   Copyright (c) 2011 Agence Soon. (http://www.agence-soon.fr)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author      Hervé Guétin
 *
 */
class Soon_StockReleaser_Block_Adminhtml_Sales_Order_Canceled extends Mage_Adminhtml_Block_Sales_Order {

    public function __construct() {
        parent::__construct();
        $this->_controller = 'adminhtml_sales_order_canceled';
        $this->_blockGroup = 'stockreleaser';
        $this->_headerText = Mage::helper('sales')->__('Automatically Canceled Orders');
    }

}
