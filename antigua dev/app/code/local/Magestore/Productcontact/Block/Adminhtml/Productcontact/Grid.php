<?php

class Magestore_Productcontact_Block_Adminhtml_Productcontact_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('productcontactGrid');
      $this->setDefaultSort('productcontact_id');
      $this->setDefaultDir('DESC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
	
	$store_id = $this->getRequest()->getParam('store', 0);
      $collection = Mage::getModel('productcontact/productcontact')->getCollection()
		->addFieldToFilter('status', '1')
		->addFieldToFilter('store_id', array('neq'=>0))
		;
	
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('productcontact_id', array(
          'header'    => Mage::helper('productcontact')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'productcontact_id',
      ));

      $this->addColumn('product_name', array(
          'header'    => Mage::helper('productcontact')->__('Product name'),
          'align'     =>'left',
          'index'     => 'product_name',
		  'renderer'  => 'productcontact/adminhtml_productcontact_renderer_productname',
      ));
	  
	  $this->addColumn('customer_email', array(
          'header'    => Mage::helper('productcontact')->__('Customer email'),
          'align'     =>'left',
          'index'     => 'customer_email',
		  'renderer'  => 'productcontact/adminhtml_productcontact_renderer_customeremail',
      ));
	  
	  $this->addColumn('country_id', array(
          'header'    => Mage::helper('productcontact')->__('Country'),
          'align'     =>'left',
          'index'     => 'country_id',
		  'type'      => 'options',
		  'options'   => Mage::helper('productcontact')->getCountryOption(),
      ));
	  
	  $this->addColumn('store_id', array(
          'header'    => Mage::helper('productcontact')->__('Store'),
          'align'     =>'left',
          'index'     => 'store_id',
		  // 'type' 	  => 'options',
		  // 'options'   => Mage::getModel('core/store')->getCollection()->toOptionHash(),
		  'type'      => 'store',
		  'store_view'=> true,
		  
      ));
	  

	  
	  $this->addColumn('created_time', array(
          'header'    => Mage::helper('productcontact')->__('Contact Times'),
          'align'     =>'left',
          'index'     => 'created_time',
		  'type'      => 'date',
      ));

	  /*
      $this->addColumn('content', array(
			'header'    => Mage::helper('productcontact')->__('Item Content'),
			'width'     => '150px',
			'index'     => 'content',
      ));
	  */

      $this->addColumn('status', array(
          'header'    => Mage::helper('productcontact')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('productcontact')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('productcontact')->__('View'),
                        'url'       => array('base'=> '*/*/view'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('productcontact')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('productcontact')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('productcontact_id');
        $this->getMassactionBlock()->setFormFieldName('productcontact');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('productcontact')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('productcontact')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('productcontact/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('productcontact')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('productcontact')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/view', array('id' => $row->getId()));
  }

}