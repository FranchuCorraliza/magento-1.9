<?php

class Magestore_Auction_Block_Adminhtml_Productauction_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('productauctionGrid');
        $this->setDefaultSort('productauction_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $timezone = ((Mage::app()->getLocale()->date()->get(Zend_Date::TIMEZONE_SECS)) / 3600);
        $collection = Mage::getModel('auction/productauction')->getCollection();
        $collection->getSelect()
                ->columns(array(
                    'start_date_time' => new Zend_Db_Expr("SUBDATE(CONCAT(start_date, ' ' ,start_time),INTERVAL " . $timezone . " HOUR)"),
                    'end_date_time' => new Zend_Db_Expr("SUBDATE(CONCAT(end_date, ' ' ,end_time),INTERVAL " . $timezone . " HOUR)"),
        ));
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('productauction_id', array(
            'header' => Mage::helper('auction')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'productauction_id',
        ));

        $this->addColumn('product_name', array(
            'header' => Mage::helper('auction')->__('Product'),
            'align' => 'left',
            'index' => 'product_name',
        ));

        $this->addColumn('start_date_time', array(
            'header' => Mage::helper('auction')->__('Start Time'),
            'align' => 'left',
            'index' => 'start_date_time',
            'type' => 'datetime',
            'filter_index' => "CONCAT(start_date, ' ' ,start_time)",
        ));

        $this->addColumn('end_date_time', array(
            'header' => Mage::helper('auction')->__('End Time'),
            'align' => 'left',
            'index' => 'end_date_time',
            'type' => 'datetime',
            'filter_index' => "CONCAT(end_date, ' ' ,end_time)",
        ));

        $this->addColumn('allow_buyout', array(
            'header' => Mage::helper('auction')->__('Sell auctioned product normally'),
            'align' => 'left',
            'width' => '50px',
            'index' => 'allow_buyout',
            'type' => 'options',
            'options' => Mage::helper('auction')->getListBuyoutStatus(),
        ));

        $this->addColumn('day_to_buy', array(
            'header' => Mage::helper('auction')->__('Sell normally after'),
            'align' => 'left',
            'width' => '50px',
            'index' => 'day_to_buy',
        ));
        
        $this->addColumn('featured', array(
            'header' => Mage::helper('auction')->__('Featured'),
            'align' => 'left',
            'width' => '40px',
            'index' => 'featured',
            'type' => 'options',
            'options' => Mage::helper('auction')->getListFeaturedStatus(),
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('auction')->__('Status'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'status',
            'type' => 'options',
            'options' => Mage::helper('auction')->getListAuctionStatus(),
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('auction')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('auction')->__('View Bids'),
                    'url' => array('base' => '*/*/detail'),
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
        $this->setMassactionIdField('productauction_id');
        $this->getMassactionBlock()->setFormFieldName('productauction');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('auction')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('auction')->__('Are you sure?')
        ));

        //$statuses = Mage::getSingleton('auction/status')->getOptionArray();
        $statuses = Mage::helper('auction')->getListAuctionStatus();

        array_unshift($statuses, array('label' => '', 'value' => ''));
        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('auction')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('auction')->__('Status'),
                    'values' => $statuses
                )
            )
        ));
        $this->getMassactionBlock()->addItem('featured', array(
            'label' => Mage::helper('auction')->__('Change featured'),
            'url' => $this->getUrl('*/*/massFeatured', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'featured',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('auction')->__('Featured'),
                    'values' => Mage::helper('auction')->getListFeaturedStatus()
                )
            )
        ));
        return $this;
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}
