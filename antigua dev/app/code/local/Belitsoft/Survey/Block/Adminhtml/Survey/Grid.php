<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */
class Belitsoft_Survey_Block_Adminhtml_Survey_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	/**
	 * Constructor of Grid
	 *
	 */
	public function __construct()
	{
		parent::__construct();
		$this->setId('survey_grid');
		$this->setUseAjax(true);
		$this->setDefaultSort('survey_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}

	/**
	 * Preparation of the data that is displayed by the grid.
	 *
	 * @return Belitsoft_Survey_Block_Adminhtml_Survey_Grid
	 */
	protected function _prepareCollection()
	{
		$collection = Mage::getResourceModel('belitsoft_survey/survey_collection');
		$this->setCollection($collection);

		parent::_prepareCollection();
		
		return $this;
	 }

	/**
	 * Preparation of the requested columns of the grid
	 *
	 * @return Belitsoft_Survey_Block_Adminhtml_Survey_Grid
	 */
	protected function _prepareColumns()
	{
		$this->addColumn('survey_id',
			array(
				'header'					=> $this->__('Survey ID'), 
				'width'						=> '80px', 
				'type'						=> 'text', 
				'index'						=> 'survey_id',
				'filter_condition_callback'	=> array(
					$this, 
					'_filterSurveyIdCondition'
				)
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
		
		$this->addColumn('survey_name',
			array(
				'header'	=> $this->__('Survey Name'), 
				'index'		=> 'survey_name'
			)
		);
		
		$this->addColumn('category_id', 
			array(
				'header'					=> $this->__('Survey category'), 
				'index'						=> 'category_id', 
				'type'						=> 'options',
				'options'					=> $this->_getCategories(),
				'sortable'					=> false, 
				'filter_condition_callback'	=> array(
					$this, 
					'_filterCategoryCondition'
				)
			)
		);
		
		$this->addColumn('start_date',
			array(
				'header'	=> $this->__('Start on'),
				'type'		=> 'datetime',
				'index'		=> 'start_date',
				'renderer'	=> 'belitsoft_survey/adminhtml_survey_grid_column_renderer_date', 
				'default'	=> ' ---- ',
			)
		);
		
		$this->addColumn('expired_date',
			array(
				'header'	=> $this->__('Expired on'),
				'type'		=> 'datetime',
				'index'		=> 'expired_date',
				'renderer'	=> 'belitsoft_survey/adminhtml_survey_grid_column_renderer_date',
				'default'	=> ' ---- ',
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
				'header'	=> $this->__('Active'),
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
						'caption' => $this->__('Edit'), 
						'url' => array(
							'base'	=> '*/*/edit'
						), 
						'field'	=> 'survey_id'
					),
					array(
						'caption'	=> Mage::helper('adminhtml')->__('Delete'),
						'url'		=> array(
							'base' => '*/*/delete'
						),
						'field'		=> 'survey_id'
					),
				),
				'filter'	=> false, 
				'sortable'	=> false, 
				'is_system'	=> true,
				'index'		=> 'action' 
			)
		);
		
		return parent::_prepareColumns();
	}

	protected function _prepareMassaction()
	{
		$this->setMassactionIdField('survey_id');
		$this->getMassactionBlock()->setFormFieldName('surveytable');

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
	 * Helper function to load categories collection
	 */
	protected function _getCategories()
	{
		return Mage::getResourceModel('belitsoft_survey/category_collection')->toOptionHash();
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
		if(!$value = $column->getFilter()->getValue()) {
			return;
		}
		
		$this->getCollection()->addStoreFilter($value);
	}
	
	/**
	 * Helper function to add survey id filter condition
	 *
	 * @param Mage_Core_Model_Mysql4_Collection_Abstract $collection Data collection
	 * @param Mage_Adminhtml_Block_Widget_Grid_Column $column Column information to be filtered
	 */
	protected function _filterSurveyIdCondition($collection, $column)
	{
		if(!$value = $column->getFilter()->getValue()) {
			return;
		}
		
		$this->getCollection()->addSurveyIdFilter($value);
	}
	
	/**
	 * Helper function to add category filter condition
	 *
	 * @param Mage_Core_Model_Mysql4_Collection_Abstract $collection Data collection
	 * @param Mage_Adminhtml_Block_Widget_Grid_Column $column Column information to be filtered
	 */
	protected function _filterCategoryCondition($collection, $column)
	{
		if(!$value = $column->getFilter()->getValue()) {
			return;
		}
		
		$this->getCollection()->addCategoryFilter($value);
	}
	
	/**
	 * Helper function to reveive on row click url
	 *
	 * @param Belitsoft_Survey_Model_Survey $row Current rows dataset
	 * @return string URL for current row's onclick event
	 */
	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/edit', array('survey_id' => $row->getSurveyId()));
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
