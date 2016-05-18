<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */
class Belitsoft_Survey_Block_Adminhtml_Question_Edit_Fields extends Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset_Element
{
	public function __construct()
	{
		$this->setTemplate('survey/question/edit/fields.phtml');
	}

	protected function _prepareLayout()
	{
		$this->setChild('add_button',
			$this->getLayout()
				->createBlock('adminhtml/widget_button')
				->setData(
					array(
						'label'		=> $this->__('Add New Option'),
						'class'		=> 'add',
						'id'		=> 'add_new_field',
						'on_click'	=> 'bitsSurveyFields.add()'
					)
				)
		);

		$this->setChild('delete_button',
			$this->getLayout()
				->createBlock('adminhtml/widget_button')
				->setData(
					array(
						'label'		=> $this->__('Remove'),
						'class'		=> 'delete delete-product-option',
						'on_click'	=> 'bitsSurveyFields.remove(event)'
					)
				)
		);
		
		$fields_layout = $this->getQuestionType();
		if(!$fields_layout || ($fields_layout == 'shortanswer')) {
			$fields_layout = 'empty';
		}
		
		$field_block = $this->getLayout()->createBlock('belitsoft_survey/adminhtml_question_edit_fields_'.strtolower($fields_layout));
		if(!$field_block) {
			$this->getLayout()->createBlock('belitsoft_survey/adminhtml_question_edit_fields_empty');
		}

		$fields = array();
		if(Mage::registry('survey_question') && Mage::registry('survey_question')->getId()) {
			$fields = Mage::registry('survey_question')->getFields();
		}
		$field_block->setFields($fields);
		
		$this->setChild('fields', $field_block);
		
		parent::_prepareLayout();
	}

	public function getFieldId()
	{
		return 'survey_field';
	}
	
	public function getFieldTopId()
	{
		return 'survey_fields_top';
	}
	
	public function getFieldName()
	{
		return 'fields';
	}

	public function getAddButtonHtml()
	{
		return $this->getChildHtml('add_button');
	}

	public function getDeleteButtonHtml()
	{
		return $this->getChildHtml('delete_button');
	}
	
	public function getFields()
	{
		return $this->getChildHtml('fields');
	}

	protected function _toJson($data)
	{
		return Mage::helper('core')->jsonEncode($data);
	}
}
