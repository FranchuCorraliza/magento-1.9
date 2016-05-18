<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */
class Belitsoft_Survey_Block_Adminhtml_Question_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	/**
	 * Constructor of Grid
	 *
	 */
	public function __construct()
	{
		parent::__construct();
		$this->setId('survey_question_grid');
		$this->setUseAjax(true);
		$this->setDefaultSort('question_id');
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
		$collection = Mage::getResourceModel('belitsoft_survey/question_collection');
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
		$this->addColumn('question_id',
			array(
				'header'	=> $this->__('Question ID'), 
				'width'		=> '80px', 
				'type'		=> 'text', 
				'index'		=> 'question_id'
			)
		);
		
		$this->addColumn('question_text',
			array(
				'header'	=> $this->__('Question Text'), 
				'index'		=> 'question_text',
				'renderer'  => 'belitsoft_survey/adminhtml_question_grid_column_renderer_html'
			)
		);
		
		$this->addColumn('question_type', 
			array(
				'header'	=> $this->__('Question type'), 
				'index'		=> 'question_type',
				'type'		=> 'options',
				'options'	=> $this->_getQuestionTypes(),
				'sortable'	=> false, 
				'filter_condition_callback'	=> array(
					$this, 
					'_filterQuestionTypeCondition'
				)
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
		
		$this->addColumn('sort_order',
			array(
				'header'	=> $this->__('Position'),
				'width'		=> '1',
				'type'		=> 'number',
				'index'		=> 'sort_order',
//				'editable'	=> true
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
						'caption'	=> $this->__('Edit'), 
						'url'		=> array(
							'base' => '*/*/edit'
						), 
						'field'		=> 'question_id'
					),
					array(
						'caption'	=> Mage::helper('adminhtml')->__('Delete'),
						'url'		=> array(
							'base' => '*/*/delete'
						),
						'field'		=> 'question_id'
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
		$this->setMassactionIdField('question_id');
		$this->getMassactionBlock()->setFormFieldName('questiontable');

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
	 * Helper function to load surveys collection
	 *
	 */
	protected function _getSurveys()
	{
		return Mage::getResourceModel('belitsoft_survey/survey_collection')->toOptionHash();
	}
	
	/**
	 * Helper function to load question types
	 *
	 */
	protected function _getQuestionTypes()
	{
		return Mage::getModel('belitsoft_survey/question')->getQuestionTypes();
	}
	
	/**
	 * Helper function to do after load modifications
	 *
	 */
	protected function _afterLoadCollection()
	{
//		$this->getCollection()->walk('afterLoad', $this->getCollection()->getItems());

		parent::_afterLoadCollection();
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
	 * Helper function to add question type filter condition
	 *
	 * @param Mage_Core_Model_Mysql4_Collection_Abstract $collection Data collection
	 * @param Mage_Adminhtml_Block_Widget_Grid_Column $column Column information to be filtered
	 */
	protected function _filterQuestionTypeCondition($collection, $column)
	{
		if(!$value = $column->getFilter()->getValue()) {
			return;
		}
		
		$this->getCollection()->addQuestionTypeFilter($value);
	}
	
	/**
	 * Helper function to reveive on row click url
	 *
	 * @param Belitsoft_Survey_Model_Question $row Current rows dataset
	 * @return string URL for current row's onclick event
	 */
	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/edit', array('question_id' => $row->getQuestionId()));
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
