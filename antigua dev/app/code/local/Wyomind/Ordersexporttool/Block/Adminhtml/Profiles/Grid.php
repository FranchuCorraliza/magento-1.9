<?php

class Wyomind_Ordersexporttool_Block_Adminhtml_Profiles_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
      
        parent::__construct();
        $this->setId('ordersexporttoolGrid');
        $this->setDefaultSort('ordersexporttool_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
       
        $collection = Mage::getModel('ordersexporttool/profiles')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('file_id', array(
            'header' => Mage::helper('ordersexporttool')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'file_id',
            'filter' => false,
        ));

        $this->addColumn('file_name', array(
            'header' => Mage::helper('ordersexporttool')->__('Filename'),
            'align' => 'left',
            'index' => 'file_name',
        ));

        $this->addColumn('file_type', array(
            'header' => Mage::helper('ordersexporttool')->__('File format'),
            'align' => 'left',
            'index' => 'file_type',
            'type' => 'options',
            'options' => array(
                1 => 'xml',
                2 => 'txt',
                3 => 'csv',
            ),
            'renderer' => 'Wyomind_Ordersexporttool_Block_Adminhtml_Profiles_Renderer_Type',
        ));



        $this->addColumn('file_link', array(
            'header' => Mage::helper('ordersexporttool')->__('Last generated file'),
            'align' => 'left',
            'index' => 'file_link',
            'type' => 'options',
            'filter' => false,
            'sortable' => false,
            'renderer' => 'Wyomind_Ordersexporttool_Block_Adminhtml_Profiles_Renderer_Link',
        ));

        $this->addColumn('file_last_exported_id', array(
            'header' => Mage::helper('ordersexporttool')->__('Starting with order #'),
            'align' => 'left',
            'index' => 'file_last_exported_id',
             'renderer' => 'Wyomind_Ordersexporttool_Block_Adminhtml_Profiles_Renderer_Startingwith',
        ));
        $this->addColumn('file_updated_at', array(
            'header' => Mage::helper('ordersexporttool')->__('Last update'),
            'align' => 'left',
            'index' => 'file_updated_at',
            'type' => 'datetime',
        ));
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header' => Mage::helper('ordersexporttool')->__('Store View'),
                'index' => 'store_id',
                'type' => 'store',
            ));
        }

       

        $this->addColumn('action', array(
            'header' => Mage::helper('ordersexporttool')->__('Action'),
            'align' => 'left',
            'index' => 'action',
            'filter' => false,
            'sortable' => false,
            'renderer' => 'Wyomind_Ordersexporttool_Block_Adminhtml_Profiles_Renderer_Action',
        ));


        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}