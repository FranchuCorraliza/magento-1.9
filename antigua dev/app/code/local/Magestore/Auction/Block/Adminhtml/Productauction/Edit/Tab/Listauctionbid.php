<?php

class Magestore_Auction_Block_Adminhtml_Productauction_Edit_Tab_Listauctionbid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('auctionbidGrid');
        $this->setDefaultSort('auctionbid_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $timezone = ((Mage::app()->getLocale()->date()->get(Zend_Date::TIMEZONE_SECS)) / 3600);
        $collection = Mage::getModel('auction/auction')->getCollection()
                ->addFieldToFilter('productauction_id', $this->getRequest()->getParam('id'));
        $collection->getSelect()
                ->columns(array(
                    'created_date_time' => new Zend_Db_Expr("SUBDATE(CONCAT(created_date, ' ' ,created_time),INTERVAL " . $timezone . " HOUR)"),
        ));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $store = $this->_getStore();

        $this->addColumn('auctionbid_id', array(
            'header' => Mage::helper('auction')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'auctionbid_id',
        ));

        $this->addColumn('customer_name', array(
            'header' => Mage::helper('auction')->__('Customer'),
            'align' => 'left',
            'index' => 'customer_name',
            'renderer' => 'auction/adminhtml_productauction_renderer_customer',
        ));

        $this->addColumn('price', array(
            'header' => Mage::helper('auction')->__('Price'),
            'align' => 'left',
            'index' => 'price',
            'type' => 'price',
            'currency_code' => $store->getBaseCurrency()->getCode(),
        ));

        $this->addColumn('created_date_time', array(
            'header' => Mage::helper('auction')->__('Bid Time'),
            'align' => 'left',
            'index' => 'created_date_time',
            'type' => 'datetime',
            'width' => '180px',
            'filter_index' => "CONCAT(created_date, ' ' ,created_time)"
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('auction')->__('Status'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'status',
            'type' => 'options',
            'options' => Mage::helper('auction')->getListBidStatus(),
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('auction')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('auction')->__('View'),
                    'url' => array('base' => '*/adminhtml_auction/edit'),
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

    protected function _prepareMassaction() {
        $this->setMassactionIdField('auction_id');
        $this->getMassactionBlock()->setFormFieldName('auction');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('auction')->__('Delete'),
            'url' => $this->getUrl('*/adminhtml_auction/massDelete'),
            'confirm' => Mage::helper('auction')->__('Are you sure?')
        ));

        return $this;
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/adminhtml_auction/edit', array('id' => $row->getId()));
    }

    protected function _getStore() {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _afterLoadCollection() {
        if ($this->_isExport) {
        }
    }

}
