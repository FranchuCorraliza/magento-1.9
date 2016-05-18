<?php

/**
 * MageWorx
 * Admin Order Grid  extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersGrid
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_OrdersGrid_Model_Grid extends Mage_Core_Model_Abstract
{

    const TABLE_TEMP_SALES_ORDER = 'temp_sales_order';
    const TABLE_TEMP_SALES_ORDER_ITEMS = 'temp_sales_order_items';
    const TABLE_TEMP_SALES_ORDER_COMMENTS = 'temp_sales_order_comments';
    const TABLE_TEMP_SHIPMENT_GRID = 'temp_shipment_grid';
    const TABLE_TEMP_SHIPMENT_TRACK = 'temp_shipment_track';
    const TABLE_SALES_ORDER_PAYMENT = 'order_payment_tbl';
    const TABLE_SALES_INVOICE = 'invoice_tbl';
    const TABLE_ORDER_ADDRESS_BILLING = 'order_address_billing_tbl';
    const TABLE_ORDER_ADDRESS_SHIPPING = 'order_address_shipping_tbl';

    protected $_listColumns = array();

    public function _construct()
    {
        parent::_construct();
    }

    /**
     * Set transaction isolation level for SESSION as READ COMMITTED
     * in order to avoid deadlocks
     *
     * @param Mage_Core_Model_Resource_Db_Collection_Abstract $collection
     */
    protected function setTransactionIsolationLevel(Mage_Core_Model_Resource_Db_Collection_Abstract $collection)
    {
        try {
            $connection = $collection->getConnection();
            $connection->query('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED;');
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Modify select of orders grid collection
     *
     * @param Mage_Sales_Model_Resource_Order_Grid_Collection $collection
     * @param array $listColumns
     * @return void
     */
    public function modifyOrdersGridCollection(
        Mage_Sales_Model_Resource_Order_Grid_Collection $collection,
        array $listColumns
    )
    {
        if (empty($listColumns)) {
            return;
        } else {
            $this->_listColumns = $listColumns;
        }

        $this->setTransactionIsolationLevel($collection);

        foreach ($listColumns as $column) {

            switch ($column) {
                case 'product_names':
                case 'product_skus':
                case 'product_options':
                case 'qnty':
                    $this->setOrderItemTbl($collection);
                    break;
                case 'payment_method':
                    $this->setFieldPaymentMethod($collection);
                    break;
                case 'shipped':
                    $this->setShipmentTbl($collection);
                    break;
                case 'tracking_number':
                    $this->setShipmentTbl($collection);
                    $this->setShipmentTrackTbl($collection);
                    break;
                case 'billing_company':
                case 'billing_street':
                case 'billing_city':
                case 'billing_region':
                case 'billing_country':
                case 'billing_postcode':
                case 'billing_telephone':
                    $this->setOrderAddressTbl('billing', $collection);
                    break;
                case 'shipping_company':
                case 'shipping_street':
                case 'shipping_city':
                case 'shipping_region':
                case 'shipping_country':
                case 'shipping_postcode':
                case 'shipping_telephone':
                    $this->setOrderAddressTbl('shipping', $collection);
                    break;
                case 'order_comment':
                    $this->setOrderCommentTbl($collection);
                    break;
                case 'shipping_amount':
                case 'base_shipping_amount':
                case 'subtotal':
                case 'base_subtotal':
                case 'status':
                    $this->setOrderTbl($collection);
                    break;
                case 'invoice_increment_id':
                    $this->setFieldInvoiceGrid($collection);
                    break;
            }
        }

        $this->hideArchivedOrders($collection);

        return;
    }

    /** Add default filter to collection
     * Do not show archived orders
     *
     * @param $collection
     */
    protected function hideArchivedOrders($collection)
    {
        $setDefaultFilter = true;
        $where = $collection->getSelect()->getPart('where');

        if (!empty($where)) {
            foreach ($where as $part) {
                if (stripos($part, 'order_group_id') !== false) {
                    $setDefaultFilter = false;
                    break;
                }
            }
        }

        if ($setDefaultFilter) {
            /** @var Varien_Db_Select $select */
            $select = $collection->getSelect();
            $where = $select->getPart('where');
            $and = '';
            if (!empty($where)) {
                $and = 'AND ';
            }
            $where[] = $and . "(main_table.order_group_id = '0')";
            $select->setPart('where', $where);
        }
    }

    /**
     * Modify select of customer orders grid collection
     *
     * @param Mage_Sales_Model_Resource_Order_Grid_Collection $collection
     * @param array $listColumns
     * @return void
     */
    public function modifyCustomerOrdersGridCollection(
        Mage_Sales_Model_Resource_Order_Grid_Collection $collection,
        array $listColumns
    )
    {
        if (empty($listColumns)) {
            return;
        } else {
            $this->_listColumns = $listColumns;
        }

        foreach ($listColumns as $column) {
            switch ($column) {
                case 'status':
                    $collection->addFieldToSelect('status');
                    break;
                case 'total_refunded':
                    $collection->addFieldToSelect('total_refunded');
                    break;
                case 'base_total_refunded':
                    $collection->addFieldToSelect('base_total_refunded');
                    break;
                case 'customer_email':
                    $collection->addFieldToSelect('customer_email');
                    break;
                case 'customer_group':
                    $collection->addFieldToSelect('customer_group_id');
                    break;
                case 'tax_amount':
                    $collection->addFieldToSelect('tax_amount');
                    break;
                case 'base_tax_amount':
                    $collection->addFieldToSelect('base_tax_amount');
                    break;
                case 'discount_amount':
                    $collection->addFieldToSelect('discount_amount');
                    break;
                case 'base_discount_amount':
                    $collection->addFieldToSelect('base_discount_amount');
                    break;
                case 'shipping_method':
                    $collection->addFieldToSelect('shipping_method');
                    $collection->addFieldToSelect('shipping_description');
                    break;
                case 'internal_credit':
                    if (Mage::getConfig()->getModuleConfig('MageWorx_CustomerCredit')->is('active', true)) {
                        $collection->addFieldToSelect('customer_credit_amount');
                    }
                    break;
                case 'base_internal_credit':
                    if (Mage::getConfig()->getModuleConfig('MageWorx_CustomerCredit')->is('active', true)) {
                        $collection->addFieldToSelect('base_customer_credit_amount');
                    }
                    break;
                case 'order_group':
                    break;
                case 'weight':
                    $collection->addFieldToSelect('weight');
                    break;
                case 'qnty':
                    break;
                case 'coupon_code':
                    $collection->addFieldToSelect('coupon_code');
                    break;
                case 'is_edited':
                    $collection->addFieldToSelect('is_edited');
                    break;
            }
        }
        $collection->addFieldToSelect('base_currency_code');
        $collection->addFieldToSelect('order_group_id');

        /** important: If you wish to remove next line, you need to call setTransactionIsolationLevel at this method */
        $this->modifyOrdersGridCollection($collection, $listColumns);

        return;
    }

    /**
     * Join sales order table
     *
     * @param Mage_Sales_Model_Resource_Order_Grid_Collection $collection
     * @return $this
     */
    public function setOrderTbl(Mage_Sales_Model_Resource_Order_Grid_Collection $collection)
    {
        if ($collection->getSelect() == null || isset($collection->_setFields['setOrderTbl'])) {
            return $collection;
        }

        Varien_Profiler::start('mgwrx_setOrderTbl');

        /** @var Varien_Db_Adapter_Interface $connection */
        $connection = $collection->getConnection();
        /** @var Varien_Db_Select $select */
        $select = $connection->select();

        /* Collect columns */
        $columns = array(
            'subtotal' => 'subtotal',
            'base_subtotal' => 'base_subtotal',
            'shipping_amount' => 'shipping_amount',
            'base_shipping_amount' => 'base_shipping_amount',
            'temp_entity_id' => 'entity_id'
        );

        $select->from($collection->getTable('sales/order'), $columns);
        $select->group('entity_id');

        /* Join temporary table to the collection */
        $collection->getSelect()->joinLeft(array(self::TABLE_TEMP_SALES_ORDER => $select),
            '`main_table`.`entity_id` = `' . self::TABLE_TEMP_SALES_ORDER . '`.`temp_entity_id`'
        );


        $collection->_setFields['setOrderTbl'] = true;

        Varien_Profiler::stop('mgwrx_setOrderTbl');

        return $collection;
    }

    /**
     * Join sales_flat_order_items table to the collection using temporary mysql table.
     * Adds columns like qty or product info.
     *
     * @param Mage_Sales_Model_Resource_Order_Grid_Collection $collection
     * @return Mage_Sales_Model_Resource_Order_Grid_Collection $collection
     */
    public function setOrderItemTbl(Mage_Sales_Model_Resource_Order_Grid_Collection $collection)
    {
        if ($collection->getSelect() == null || isset($collection->_setFields['setOrderItemTbl'])) {
            return $collection;
        }

        Varien_Profiler::start('mgwrx_setOrderItemTbl');

        /** @var Varien_Db_Adapter_Interface $connection */
        $connection = $collection->getConnection();
        /** @var Varien_Db_Select $select */
        $select = $connection->select();

        /* Collect columns */
        $columns = array(
            'order_id' => 'order_id',
            'parent_item_id' => 'parent_item_id',
            'product_names' => new Zend_Db_Expr('GROUP_CONCAT(`name` SEPARATOR \'\n\')'),
            'skus' => new Zend_Db_Expr('GROUP_CONCAT(`sku` SEPARATOR \'\n\')'),
            'product_ids' => new Zend_Db_Expr('GROUP_CONCAT(`product_id` SEPARATOR \'\n\')'),
            'product_options' => new Zend_Db_Expr('GROUP_CONCAT(`product_options` SEPARATOR \'^\')')
        );

        if (in_array('qnty', $this->_listColumns)) {
            $columns += array(
                'total_qty_refunded' => new Zend_Db_Expr('SUM(`qty_refunded`)'),
                'total_qty_ordered_aggregated' => new Zend_Db_Expr('SUM(`qty_ordered`)'),
                'total_qty_canceled' => new Zend_Db_Expr('SUM(`qty_canceled`)'),
                'total_qty_invoiced' => new Zend_Db_Expr('SUM(`qty_invoiced`)')
            );
        }

        $select->from($collection->getTable('sales/order_item'), $columns);
        $select->group('order_id');

        /* Join temporary table to the collection */
        $collection->getSelect()->joinLeft(array(self::TABLE_TEMP_SALES_ORDER_ITEMS => $select),
            '`main_table`.`entity_id` = `' . self::TABLE_TEMP_SALES_ORDER_ITEMS . '`.`order_id` AND `' . self::TABLE_TEMP_SALES_ORDER_ITEMS . '`.`parent_item_id` IS NULL'
        );

        $collection->_setFields['setOrderItemTbl'] = true;

        Varien_Profiler::stop('mgwrx_setOrderItemTbl');

        return $collection;
    }

    /**
     * Join comments table to the collection using temporary mysql table.
     * Adds comments column.
     *
     * @param Mage_Sales_Model_Resource_Order_Grid_Collection $collection
     * @return Mage_Sales_Model_Resource_Order_Grid_Collection $collection
     */
    public function setOrderCommentTbl(Mage_Sales_Model_Resource_Order_Grid_Collection $collection)
    {
        if ($collection->getSelect() == null || isset($collection->_setFields['setOrderCommentTbl'])) {
            return $collection;
        }

        Varien_Profiler::start('mgwrx_setOrderCommentTbl');

        /** @var Varien_Db_Adapter_Interface $connection */
        $connection = $collection->getConnection();
        /** @var Varien_Db_Select $select */
        $select = $connection->select();

        /* Collect columns */
        $columns = array(
            'order_comment' => new Zend_Db_Expr('GROUP_CONCAT(`comment` SEPARATOR \'\n\')'),
            'comment_parent_id' => 'parent_id'
        );

        $select->from($collection->getTable('sales/order_status_history'), $columns);
        $select->group('comment_parent_id');

        /* Join temporary table to the collection */
        $collection->getSelect()->joinLeft(array(self::TABLE_TEMP_SALES_ORDER_COMMENTS => $select),
            '`main_table`.`entity_id` = `' . self::TABLE_TEMP_SALES_ORDER_COMMENTS . '`.`comment_parent_id`'
        );

        $collection->_setFields['setOrderCommentTbl'] = true;

        Varien_Profiler::stop('mgwrx_setOrderCommentTbl');

        return $collection;
    }

    /**
     * Join address table
     *
     * @param string $addressType
     * @param Mage_Sales_Model_Resource_Order_Grid_Collection $collection
     * @return $this
     */
    public function setOrderAddressTbl($addressType = 'billing', Mage_Sales_Model_Resource_Order_Grid_Collection $collection)
    {
        if ($collection->getSelect() == null || isset($collection->_setFields['setOrderAddressTbl' . $addressType])) {
            return $collection;
        }

        $collection->getSelect()->joinLeft(array('order_address_' . $addressType . '_tbl' => $collection->getTable('sales/order_address')),
            'main_table.entity_id = order_address_' . $addressType . '_tbl.parent_id AND order_address_' . $addressType . '_tbl.`address_type` = "' . $addressType . '"',
            array(
                $addressType . '_company' => 'company',
                $addressType . '_street' => 'street',
                $addressType . '_city' => 'city',
                $addressType . '_region' => 'region',
                $addressType . '_country_id' => 'country_id',
                $addressType . '_postcode' => 'postcode',
                $addressType . '_telephone' => 'telephone'
            )
        );
        $collection->_setFields['setOrderAddressTbl' . $addressType] = true;

        return $collection;
    }

    /**
     * Join payment table
     *
     * @param Mage_Sales_Model_Resource_Order_Grid_Collection $collection
     * @return $this
     */
    public function setFieldPaymentMethod(Mage_Sales_Model_Resource_Order_Grid_Collection $collection)
    {
        if ($collection->getSelect() == null) {
            return $collection;
        }

        $collection->getSelect()->joinLeft(array(self::TABLE_SALES_ORDER_PAYMENT => $collection->getTable('sales/order_payment')),
            '`main_table`.`entity_id` = `' . self::TABLE_SALES_ORDER_PAYMENT . '`.`parent_id`', 'method'
        );

        return $collection;
    }

    /**
     * @param Mage_Sales_Model_Resource_Order_Grid_Collection $collection
     * @return $this
     */
    public function setFieldInvoiceGrid(Mage_Sales_Model_Resource_Order_Grid_Collection $collection)
    {
        if ($collection->getSelect() == null) {
            return $collection;
        }

        Varien_Profiler::start('mgwrx_setFieldInvoiceGrid');

        /** @var Varien_Db_Adapter_Interface $connection */
        $connection = $collection->getConnection();
        /** @var Varien_Db_Select $select */
        $select = $connection->select();

        /* Collect columns */
        $columns = array(
            'invoice_increment_id' => new Zend_Db_Expr('GROUP_CONCAT(`increment_id` SEPARATOR \'\n\')'),
            'invoice_order_id' => 'order_id'
        );

        $select->from($collection->getTable('sales/invoice'), $columns);
        $select->group('invoice_order_id');

        /* Join temporary table to the collection */
        $collection->getSelect()->joinLeft(array(self::TABLE_SALES_INVOICE => $select),
            '`main_table`.`entity_id` = `' . self::TABLE_SALES_INVOICE . '`.`invoice_order_id`'
        );

        Varien_Profiler::stop('mgwrx_setFieldInvoiceGrid');

        return $collection;
    }

    /**
     * @param Mage_Sales_Model_Resource_Order_Grid_Collection $collection
     * @return $this
     */
    public function setShipmentTbl(Mage_Sales_Model_Resource_Order_Grid_Collection $collection)
    {
        if ($collection->getSelect() == null || isset($collection->_setFields['setShipmentTbl'])) {
            return $collection;
        }

        /** @var Varien_Db_Adapter_Interface $connection */
        $connection = $collection->getConnection();
        /** @var Varien_Db_Select $select */
        $select = $connection->select();

        /* Collect columns */
        $columns = array(
            'shipped' => new Zend_Db_Expr('(IF(IFNULL(`entity_id`, 0)>0, 1, 0))'),
            'total_qty_shipped' => new Zend_Db_Expr('SUM(`total_qty`)'),
            'shipment_order_id' => 'order_id'
        );

        $select->from($collection->getTable('sales/shipment_grid'), $columns);
        $select->group('shipment_order_id');

        /* Join temporary table to the collection */
        $collection->getSelect()->joinLeft(array(self::TABLE_TEMP_SHIPMENT_GRID => $select),
            '`main_table`.`entity_id` = `' . self::TABLE_TEMP_SHIPMENT_GRID . '`.`shipment_order_id`'
        );

        $collection->_setFields['setShipmentTbl'] = true;

        return $collection;
    }

    /**
     * @param Mage_Sales_Model_Resource_Order_Grid_Collection $collection
     * @return $this
     */
    public function setShipmentTrackTbl(Mage_Sales_Model_Resource_Order_Grid_Collection $collection)
    {
        if ($collection->getSelect() == null || isset($collection->_setFields['setShipmentTrackTbl'])) {
            return $collection;
        }

        if (version_compare(Mage::helper('mageworx_ordersgrid')->getMagentoVersion(), '1.6.0', '>=')) {
            $columnName = 'track_number';
        } else {
            $columnName = 'number';
        }

        /** @var Varien_Db_Adapter_Interface $connection */
        $connection = $collection->getConnection();
        /** @var Varien_Db_Select $select */
        $select = $connection->select();

        /* Collect columns */
        $columns = array(
            'tracking_number' => new Zend_Db_Expr('GROUP_CONCAT(`' . $columnName . '` SEPARATOR \'\n\')'),
            'tracking_parent_id' => 'parent_id',
            'tracking_order_id' => 'order_id'
        );

        $select->from($collection->getTable('sales/shipment_track'), $columns);
        $select->group('tracking_order_id');

        /* Join temporary table to the collection */
        $collection->getSelect()->joinLeft(array(self::TABLE_TEMP_SHIPMENT_TRACK => $select),
            '`main_table`.`entity_id` = `' . self::TABLE_TEMP_SHIPMENT_TRACK . '`.`tracking_order_id`'
        );

        $collection->_setFields['setShipmentTrackTbl'] = true;

        return $collection;
    }

    /**
     * @param Mage_Sales_Model_Resource_Order_Grid_Collection $collection
     * @return $this
     */
    public function setShellRequest(Mage_Sales_Model_Resource_Order_Grid_Collection $collection)
    {
        if ($collection->getSelect() == null) {
            return $collection;
        }

        $sql = $collection->getSelect()->assemble();
        $collection->getSelect()->reset()->from(array('main_table' => new Zend_Db_Expr('(' . $sql . ')')), '*');

        return $collection;
    }

}