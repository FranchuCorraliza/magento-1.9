<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */
class Belitsoft_Survey_Block_Adminhtml_Category_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	/**
	 * Constructor of Grid
	 *
	 */
	public function __construct()
	{
		parent::__construct();
		$this->setId('survey_category_grid');
		$this->setUseAjax(true);
		$this->setDefaultSort('category_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}

	/**
	 * Preparation of the data that is displayed by the grid.
	 *
	 * @return Belitsoft_Survey_Block_Adminhtml_Category_Grid
	 */
	protected function _prepareCollection()
	{
		/* @var $collection Belitsoft_Survey_Model_Mysql4_Category_Collection */
		$collection = Mage::getResourceModel('belitsoft_survey/category_collection');
		$this->setCollection($collection);

		parent::_prepareCollection();
		
		return $this;
	}

	/**
	 * Preparation of the requested columns of the grid
	 *
	 * @return Belitsoft_Survey_Block_Adminhtml_Category_Grid
	 */
	protected function _prepareColumns()
	{
		$this->addColumn('category_id',
			array(
				'header'	=> $this->__('Category ID'), 
				'width'		=> '80px', 
				'type'		=> 'text', 
				'index'		=> 'category_id'
			)
		);
		
		if (!Mage::app()->isSingleStoreMode()) {
			$this->addColumn('store_id',
				array(
					'header'					=> Mage::helper('cms')->__('Store view'), 
					'index'						=> 'store_id', 
					'type'						=> 'store', 
					'store_all'					=> true, 
					'store_view'				=> true, 
					'sortable'					=> false, 
					'filter_condition_callback'	=> array(
						$this, 
						'_filterStoreCondition'
					)
				)
			);
		}
		
		$this->addColumn(
			'category_name',
			array(
				'header'	=> $this->__('Category Name'), 
				'index'		=> 'category_name',
			)
		);
		
		$this->addColumn('only_for_registered', 
			array(
				'header'	=> $this->__('Only for registered'),
				'index'		=> 'only_for_registered', 
				'type'		=> 'options', 
				'width'		=> '70px', 
				'options'	=> array(
					0 => Mage::helper('cms')->__('No'), 
					1 => Mage::helper('cms')->__('Yes')
				)
			)
		);
		
		$this->addColumn('is_active', 
			array(
				'header'	=> Mage::helper('cms')->__('Active'), 
				'index'		=> 'is_active', 
				'type'		=> 'options', 
				'width'		=> '70px', 
				'options'	=> array(
					0 => Mage::helper('cms')->__('No'), 
					1 => Mage::helper('cms')->__('Yes')
				)
			)
		);
		
		$this->addColumn('action', 
			array(
				'header'	=> $this->__('Action'), 
				'width'		=> '50px',
				'type'		=> 'action',
				'getter'	=> 'getId',
				'actions'	=> array(
					array(
						'caption'	=> $this->__('Edit'), 
						'url'		=> array(
							'base' => '*/*/edit'
						), 
						'field'		=> 'category_id'
					),
					array(
						'caption'	=> Mage::helper('adminhtml')->__('Delete'),
						'url'		=> array(
							'base' => '*/*/delete'
						),
						'field'		=> 'category_id'
					),
				),
				'filter'	=> false, 
				'sortable'	=> false, 
				'index'		=> 'stores', 
				'is_system'	=> true,
			)
		);
		
		return parent::_prepareColumns();
	}

	protected function _prepareMassaction()
	{
		$this->setMassactionIdField('category_id');
		$this->getMassactionBlock()->setFormFieldName('categorytable');

		$this->getMassactionBlock()
			->addItem('delete',
				array(
					'label'	=> Mage::helper('adminhtml')->__('Delete'),
					'url'	=> $this->getUrl('*/*/massDelete')
				)
			);


		return $this;
	}

	/**
	 * Helper function to do after load modifications
	 *
	 */
	protected function _afterLoadCollection()
	{
		$this->getCollection()->walk('afterLoad');

		parent::_afterLoadCollection();
	}

	/**
	 * Helper function to add store filter condition
	 *
	 * @param Mage_Core_Model_Mysql4_Collection_Abstract $collection Data collection
	 * @param Mage_Adminhtml_Block_Widget_Grid_Column $column Column information to be filtered
	 */
	protected function _filterStoreCondition($collection, $column)
	{
		if (!$value = $column->getFilter()->getValue()) {
			return;
		}
		
		$this->getCollection()->addStoreFilter($value);
	}
	
	/**
	 * Helper function to reveive on row click url
	 *
	 * @param Belitsoft_Survey_Model_Category $row Current rows dataset
	 * @return string URL for current row's onclick event
	 */
	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/edit', array('category_id' => $row->getCategoryId()));
	}

	/**
	 * Helper function to receive grid functionality urls for current grid
	 *
	 * @return string Requested URL
	 */
	public function getGridUrl()
	{
		return $this->getUrl('*/*/index', array('_current' => true));
	}
}
