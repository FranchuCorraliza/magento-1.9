<?php

class Magestore_Auction_Block_Adminhtml_Transaction_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('transactionGrid');
        $this->setDefaultSort('transaction_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        // $this->setUseAjax(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('auction/transaction')->getCollection();


        $orderTable = Mage::getSingleton('core/resource')->getTableName('sales/order');

        $productAuctionTable = Mage::getModel('core/resource')->getTableName('auction_product');

        $collection->getSelect()
                ->join($productAuctionTable, "$productAuctionTable.productauction_id = main_table.productauction_id", array("product_name" => "product_name",))
                ->join($orderTable, "$orderTable.entity_id = main_table.order_id", array("order_number" => "increment_id", "created_at", "total_amount" => "grand_total"))
        ;

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $orderTable = Mage::getSingleton('core/resource')->getTableName('sales/order');

        $productAuctionTable = Mage::getModel('core/resource')->getTableName('auction_product');

        $this->addColumn('transaction_id', array(
            'header' => Mage::helper('auction')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'transaction_id',
            'type' => 'number',
        ));

        $this->addColumn('product_name', array(
            'header' => Mage::helper('auction')->__('Auction'),
            'align' => 'left',
            'index' => $productAuctionTable . '.product_name',
            'renderer' => 'auction/adminhtml_transaction_renderer_productname',
            'sortable' => true,
        ));

        $this->addColumn('increment_id', array(
            'header' => Mage::helper('auction')->__('Order Number'),
            'align' => 'right',
            'sortable' => true,
            'renderer' => 'auction/adminhtml_transaction_renderer_order',
            'index' => $orderTable . '.increment_id',
            'type' => 'number',
        ));

        $this->addColumn('transaction_price', array(
            'header' => Mage::helper('auction')->__('Transaction Price'),
            'align' => 'right',
            'sortable' => true,
            'index' => 'transaction_price',
            'type' => 'price',
            'currency_code' => Mage::app()->getStore()->getBaseCurrency()->getCode(),
        ));

        $this->addColumn('created_at', array(
            'header' => Mage::helper('auction')->__('Created Date'),
            'align' => 'left',
            'width' => '200px',
            'index' => 'created_at',
            'type' => 'datetime',
            'sortable' => true,
        ));



        // $this->addColumn('status', array(
        // 'header'    => Mage::helper('auction')->__('Status'),
        // 'align'     => 'left',
        // 'width'     => '80px',
        // 'index'     => 'status',
        // 'type'      => 'options',
        // 'options'   => Mage::helper('auction')->getListBidStatus(),
        // ));

        $this->addColumn('action', array(
            'header' => Mage::helper('auction')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('auction')->__('View'),
                    'url' => array('base' => '*/*/viewtransaction'),
                    'field' => 'id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('auction')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('auction')->__('XML'));

        return parent::_prepareColumns();
    }

    // protected function _prepareMassaction()
    // {
    // $this->setMassactionIdField('transaction_id');
    // $this->getMassactionBlock()->setFormFieldName('auction');
    // $this->getMassactionBlock()->addItem('delete', array(
    // 'label'    => Mage::helper('auction')->__('Delete'),
    // 'url'      => $this->getUrl('*/*/massDelete'),
    // 'confirm'  => Mage::helper('auction')->__('Are you sure?')
    // ));
    // $statuses = Mage::getSingleton('auction/status')->getOptionArray();
    // array_unshift($statuses, array('label'=>'', 'value'=>''));
    // $this->getMassactionBlock()->addItem('status', array(
    // 'label'=> Mage::helper('auction')->__('Change status'),
    // 'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
    // 'additional' => array(
    // 'visibility' => array(
    // 'name' => 'status',
    // 'type' => 'select',
    // 'class' => 'required-entry',
    // 'label' => Mage::helper('auction')->__('Status'),
    // 'values' => $statuses
    // )
    // )
    // ));
    // return $this;
    // }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/viewtransaction', array('id' => $row->getId()));
    }

    // public function getGridUrl()
    // {
    // return $this->getUrl('*/*/listbid', array('id'=>$this->getRequest()->getParam('id')));
    // }

    protected function _afterLoadCollection() {
        if ($this->_isExport) {
            $this->removeColumn('created_at');
            $this->removeColumn('transaction_price');
            $this->removeColumn('increment_id');
            $this->removeColumn('product_name');
            $this->addColumn('product_name', array(
                'header' => Mage::helper('auction')->__('Auction'),
                'index' => 'product_name',
            ));
            $this->addColumn('increment_id', array(
                'header' => Mage::helper('auction')->__('Order Number'),
                'index' => 'order_number',
            ));
            $this->addColumn('transaction_price', array(
                'header' => Mage::helper('auction')->__('Transaction Price'),
                'index' => 'transaction_price',
                'type' => 'price',
                'currency_code' => Mage::app()->getStore()->getBaseCurrency()->getCode(),
            ));
            $this->addColumn('created_at', array(
                'header' => Mage::helper('auction')->__('Created Date'),
                'index' => 'created_at',
                'type' => 'datetime',
            ));
        }
    }

}
