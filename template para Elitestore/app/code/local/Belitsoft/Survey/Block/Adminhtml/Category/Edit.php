<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */
class Belitsoft_Survey_Block_Adminhtml_Category_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	/**
	 * Constructor for the category edit form
	 */
	public function __construct()
	{
		$this->_objectId = 'category_id';
		$this->_blockGroup = 'belitsoft_survey';
		$this->_controller = 'adminhtml_category';
		
		parent::__construct();
		
		$this->_removeButton('reset');
		$this->_updateButton('save', 'label', $this->__('Save Category'));
		$this->_updateButton('delete', 'label', $this->__('Delete Category'));
		
		$this->_addButton('saveandcontinue',
			array(
				'label'		=> $this->__('Save and continue edit'), 
				'onclick'	=> 'saveAndContinueEdit()', 
				'class'		=> 'save'
			),
			-100
		);
		
		$this->_formScripts[] = "
			function saveAndContinueEdit(){
				editForm.submit($('edit_form').action+'back/edit/');
			}
		";
	}
	
	public function getHeaderText()
	{
		if (Mage::registry('survey_category')->getCategoryId()) {
			return $this->__("Edit Category '%s'", $this->htmlEscape(Mage::registry('survey_category')->getName()));
		} else {
			return $this->__('New Category');
		}
	}

	public function getHeaderCssClass()
	{
		return '';
	}
}
