<?php
/**
 * Category chooser for Wysiwyg CMS widget
 *
 * @category   Belitsoft
 * @package    Belitsoft_Adminhtml
 * @author     Mageplace
 */
class Belitsoft_Survey_Block_Adminhtml_Category_Widget_Chooser extends Mage_Adminhtml_Block_Widget_Grid
{
	protected $_selectedCategories = array();

	/**
	 * Block construction, prepare grid params
	 *
	 * @param array $arguments Object data
	 */
	public function __construct($arguments=array())
	{
		parent::__construct($arguments);
		$this->setDefaultSort('category_name');
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
		$sourceUrl = $this->getUrl('*/survey_category_widget/chooser', array(
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
            $category = Mage::getModel('belitsoft_survey/category')->load((int)$element->getValue());
            if ($category->getId()) {
                $chooser->setLabel($category->getCategoryName());
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
					var categoryId = trElement.down("td").innerHTML;
					var categoryName = trElement.down("td").next().innerHTML;
					var optionLabel = categoryName;
					var optionValue = categoryId.replace(/^\s+|\s+$/g,"");
					'.$chooserJsObject.'.setElementValue(optionValue);
					'.$chooserJsObject.'.setElementLabel(optionLabel);
					'.$chooserJsObject.'.close();
				}
		';
	}

	/**
	 * Prepare products collection, defined collection filters (category, product type)
	 *
	 * @return Mage_Adminhtml_Block_Widget_Grid
	 */
	protected function _prepareCollection()
	{
		/* @var $collection Belitsoft_Survey_Model_Mysql4_Category_Collection */
		$collection = Mage::getResourceModel('belitsoft_survey/category_collection');
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
		$this->addColumn('category_id',
			array(
				'header'	=> $this->__('Category ID'), 
				'width'		=> '80px', 
				'type'		=> 'text', 
				'index'		=> 'category_id'
			)
		);
		
		$this->addColumn(
			'category_name',
			array(
				'header'	=> $this->__('Category Name'), 
				'index'		=> 'category_name',
			)
		);

		return parent::_prepareColumns();
	}

	/**
	 * Adds additional parameter to URL for loading only products grid
	 *
	 * @return string
	 */
	public function getGridUrl()
	{
		return $this->getUrl('*/survey_category_widget/chooser',
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
	public function setSelectedCategories($selectedCategories)
	{
		$this->_selectedCategories = $selectedCategories;

		return $this;
	}

	/**
	 * Getter
	 *
	 * @return array
	 */
	public function getSelectedCaterories()
	{
		if ($selectedCategories = $this->getRequest()->getParam('selected_categories', null)) {
			$this->setSelectedCategories($selectedCategories);
		}

		return $this->_selectedCategories;
	}
}
