<?php

class Magestore_Auction_Block_Adminhtml_Productauction_Edit_Tab_Bidwinner extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('winnerbidGrid');
        $this->setDefaultSort('auctionbid_id');
        $this->setUseAjax(true);
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $timezone = ((Mage::app()->getLocale()->date()->get(Zend_Date::TIMEZONE_SECS)) / 3600);
        $collection = Mage::getModel('auction/auction')->getCollection()
                ->addFieldToFilter('productauction_id', $this->getRequest()->getParam('id'))
                ->addFieldToFilter('status', array('in' => array(5, 6)));
        $collection->getSelect()
                ->columns(array(
                    'created_date_time' => new Zend_Db_Expr("SUBDATE(CONCAT(created_date, ' ' ,created_time),INTERVAL " . $timezone . " HOUR)"),
        ));

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
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
            'currency_code' => Mage::app()->getStore()->getBaseCurrency()->getCode(),
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

        $this->addExportType('*/*/exportWinnerCsv', Mage::helper('auction')->__('CSV'));
        $this->addExportType('*/*/exportWinnerXml', Mage::helper('auction')->__('XML'));

        return parent::_prepareColumns();
    }

    protected function _afterLoadCollection() {
        if ($this->_isExport) {
            $this->removeColumn('status');
            $this->removeColumn('created_date_time');
            $this->removeColumn('price');
            $this->removeColumn('customer_name');
            $this->addColumn('customer_name', array(
                'header' => Mage::helper('auction')->__('Customer'),
                'index' => 'customer_name',
            ));
            $this->addColumn('price', array(
                'header' => Mage::helper('auction')->__('Price'),
                'index' => 'price',
                'type'  => 'price',
                'currency_code' => Mage::app()->getStore()->getBaseCurrency()->getCode(),
            ));
            $this->addColumn('created_date_time', array(
                'header' => Mage::helper('auction')->__('Bid Time'),
                'index' => 'created_date_time',
                'type' => 'datetime',
            ));
            $this->addColumn('status', array(
                'header' => Mage::helper('auction')->__('Status'),
                'index' => 'status',
            ));
            
        }
    }

}
