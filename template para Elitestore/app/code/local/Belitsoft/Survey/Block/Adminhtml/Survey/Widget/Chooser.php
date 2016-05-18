<?php
/**
 * Category chooser for Wysiwyg CMS widget
 *
 * @category   Belitsoft
 * @package    Belitsoft_Adminhtml
 * @author     Mageplace
 */
class Belitsoft_Survey_Block_Adminhtml_Survey_Widget_Chooser extends Mage_Adminhtml_Block_Widget_Grid
{
	protected $_selectedSurveys = array();

	/**
	 * Block construction, prepare grid params
	 *
	 * @param array $arguments Object data
	 */
	public function __construct($arguments=array())
	{
		parent::__construct($arguments);
		$this->setDefaultSort('survey_name');
		$this->setDefaultDir('ASC');
		$this->setUseAjax(true);
	}

	/**
	 * Prepare chooser element HTML
	 *
	 * @param Varien_Data_Form_Element_Abstract $element Form Element
	 * @return Varien_Data_Form_Element_Abstract
	 */
	public function prepareElementHtml(Varien_Data_Form_Element_Abstract $element)
	{
		$uniqId = Mage::helper('core')->uniqHash($element->getId());
		$sourceUrl = $this->getUrl('*/survey_widget/chooser', array(
			'uniq_id' => $uniqId,
		));

		$chooser = $this->getLayout()->createBlock('widget/adminhtml_widget_chooser')
			->setElement($element)
			->setTranslationHelper($this->getTranslationHelper())
			->setConfig($this->getConfig())
			->setFieldsetId($this->getFieldsetId())
			->setSourceUrl($sourceUrl)
			->setUniqId($uniqId);

        if ($element->getValue()) {
            $survey = Mage::getModel('belitsoft_survey/survey')->load((int)$element->getValue());
            if ($survey->getId()) {
                $chooser->setLabel($survey->getSurveyName());
            }
        }
		
        $element->setData('after_element_html', $chooser->toHtml());
		return $element;
	}

	/**
	 * Grid Row JS Callback
	 *
	 * @return string
	 */
	public function getRowClickCallback()
	{
		$chooserJsObject = $this->getId();
		return '
				function (grid, event) {
					var trElement = Event.findElement(event, "tr");
					var surveyId = trElement.down("td").innerHTML;
					var surveyName = trElement.down("td").next().innerHTML;
					'.$chooserJsObject.'.setElementValue(surveyId.replace(/^\s+|\s+$/g,""));
					'.$chooserJsObject.'.setElementLabel(surveyName);
					'.$chooserJsObject.'.close();
				}
		';
	}

	/**
	 * Prepare collection, defined collection filter(category)
	 *
	 * @return Mage_Adminhtml_Block_Widget_Grid
	 */
	protected function _prepareCollection()
	{
		/* @var $collection Belitsoft_Survey_Model_Mysql4_Survey_Collection */
		$collection = Mage::getResourceModel('belitsoft_survey/survey_collection')->addCategoriesToGridCollection();
		$this->setCollection($collection);
		
		return parent::_prepareCollection();
	}

	/**
	 * Prepare columns for products grid
	 *
	 * @return Mage_Adminhtml_Block_Widget_Grid
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
		
		$this->addColumn('expired_date',
			array(
				'header'	=> $this->__('Expired on'),
				'index'		=> 'expired_date',
				'type'		=> 'datetime',
				'gmtoffset'	=> true,
				'default'	=> 	' ---- ',
				'filter'	=> false, 
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
		
		return parent::_prepareColumns();
	}
	
	/**
	 * Helper function to load categories collection
	 */
	protected function _getCategories()
	{
		return Mage::getResourceModel('belitsoft_survey/category_collection')->toOptionHash();
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
	 * Adds additional parameter to URL for loading only products grid
	 *
	 * @return string
	 */
	public function getGridUrl()
	{
		return $this->getUrl('*/survey_widget/chooser',
			array(
				'_current' => true,
				'uniq_id' => $this->getId(),
			)
		);
	}

	/**
	 * Setter
	 *
	 * @param array $selectedCategories
	 * @return Belitsoft_Survey_Block_Adminhtml_Category_Widget_Chooser
	 */
	public function setSelectedSurveys($selectedSurveys)
	{
		$this->_selectedSurveys = $selectedSurveys;

		return $this;
	}

	/**
	 * Getter
	 *
	 * @return array
	 */
	public function getSelectedSurveys()
	{
		if ($selectedSurveys = $this->getRequest()->getParam('selected_surveys', null)) {
			$this->setSelectedSurveys($selectedSurveys);
		}

		return $this->_selectedSurveys;
	}
}
