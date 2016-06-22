<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */
class Belitsoft_Survey_Block_Adminhtml_Report_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	/**
	 * Constructor of Grid
	 *
	 */
	public function __construct()
	{
		parent::__construct();

		//DON'T CHANGE GRID ID
		$this->setId('survey_report_grid');
		$this->setUseAjax(false);
		$this->setDefaultSort('creation_date');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
	}

	/**
	 * Preparation of the data that is displayed by the grid.
	 *
	 * @return Belitsoft_Survey_Block_Adminhtml_Survey_Grid
	 */
	protected function _prepareCollection()
	{
		$collection = Mage::getResourceModel('belitsoft_survey/answer_collection')->setGridView(true);
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
		$this->addColumn('start_id',
			array(
				'header'	=> $this->__('ID'), 
				'width'		=> '80px', 
				'type'		=> 'number', 
				'index'		=> 'start_id'
			)
		);
		
		$this->addColumn('creation_date',
			array(
				'header'	=> $this->__('Date'), 
				'width'		=> '160px', 
				'index'		=> 'creation_date',
				'type'		=> 'datetime',
				'gmtoffset'	=> true,
			)
		);
						
		$this->addColumn('survey_id', 
			array(
				'header'	=> $this->__('Survey Name'), 
				'index'		=> 'survey_id', 
				'type'		=> 'options',
				'options'	=> $this->_getSurveys(),
				'sortable'	=> false, 
				'filter_condition_callback'	=> array(
					$this, 
					'_filterSurveyCondition'
				)
			)
		);
						
		$this->addColumn('customer_id', 
			array(
				'header'	=> $this->__('Customer ID'), 
				'width'		=> '20px', 
				'index'		=> 'customer_id', 
				'type'		=> 'number',
				'default'	=> '----',
			)
		);
		
		$this->addColumn('firstname',
			array(
				'header'	=> $this->__('Customer First Name'),
				'width'		=> '10%', 
				'index'		=> 'customer_firstname',
				'default'	=> '----',
			)
		);

		$this->addColumn('lastname',
			array(
				'header'	=> $this->__('Customer Last Name'),
				'width'		=> '10%', 
				'index'		=> 'customer_lastname',
				'default'	=> '----',
			)
		);
				
		$this->addColumn('action', 
			array(
				'header'	=> $this->__('Action'), 
				'width'		=> '100px', 
				'type'		=> 'action', 
				'getter'	=> 'getStartId', 
				'actions'	=> array(
					array(
						'caption'	=> $this->__('Report'), 
						'url'		=> array(
							'base' => '*/*/report'
						), 
						'field'		=> 'start_id'
					),
					array(
						'caption'	=> $this->__('PDF'), 
						'url'		=> array(
							'base' => '*/*/pdf'
						), 
						'field'		=> 'start_id'
					)
				), 
				'filter'	=> false, 
				'sortable'	=> false, 
				'is_system'	=> true,
				'index'		=> 'action' 
			)
		);
		
		return parent::_prepareColumns();
	}
	
	/**
	 * Helper function to load surveys collection
	 */
	protected function _getSurveys()
	{
		return Mage::getResourceModel('belitsoft_survey/survey_collection')->toOptionHash();
	}

	/**
	 * Helper function to add survey filter condition
	 *
	 * @param Mage_Core_Model_Mysql4_Collection_Abstract $collection Data collection
	 * @param Mage_Adminhtml_Block_Widget_Grid_Column $column Column information to be filtered
	 */
	protected function _filterSurveyCondition($collection, $column)
	{
		if(!$value = $column->getFilter()->getValue()) {
			return;
		}
		
		$this->getCollection()->addSurveyFilter($value);
	}
	
	/**
	 * Helper function to reveive on row click url
	 *
	 * @param Belitsoft_Survey_Model_Question $row Current rows dataset
	 * @return string URL for current row's onclick event
	 */
	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/report', array('start_id' => $row->getStartId()));
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
