<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */
class Belitsoft_Survey_Block_Adminhtml_Question_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Prepares the page layout
	 * Loads the WYSIWYG editor on demand if enabled.
	 * 
	 * @return Belitsoft_Survey_Block_Admin_Edit
	 */
	protected function _prepareLayout()
	{
		$return = parent::_prepareLayout();
		if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
			$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
		}
		return $return;
	}
	
	/**
	 * Preperation of current form
	 *
	 * @return Belitsoft_Survey_Block_Adminhtml_Question_Edit_Form
	 */
	protected function _prepareForm()
	{
		$question = Mage::registry('survey_question');
		$id = (int)$question->getQuestionId();
		
		$form = new Varien_Data_Form();
		
		$fieldset = $form->addFieldset('base_fieldset',
			array(
				'legend'	=> $this->__('Question details'), 
				'class'		=> 'fieldset-wide'
			)
		);
		
		$question_type = $fieldset->addField('question_type',
			'select',
			array(
				'name'		=> 'question_type', 
				'label'		=> $this->__('Question type'), 
				'title'		=> $this->__('Question type'), 
				'required'	=> true, 
				'values'	=> $this->_getQuestionTypesValuesForForm()
			)
		);
		if($id) {
			$question_type->setDisabled(true);
		}
		
		$fieldset->addField('survey_id',
			'select',
			array(
				'name'		=> 'survey_id', 
				'label'		=> $this->__('Survey'), 
				'title'		=> $this->__('Survey'), 
				'required'	=> true, 
				'values'	=> $this->_getSurveysValuesForForm()
			)
		);
		
		$fieldset->addField('is_active',
			'select', 
			array(
				'name'		=> 'is_active', 
				'label'		=> Mage::helper('cms')->__('Status'), 
				'title'		=> $this->__('Question Status'), 
				'required'	=> true, 
				'options'	=> array (
					'1' => Mage::helper('cms')->__('Enabled'), 
					'0' => Mage::helper('cms')->__('Disabled')
				)
			)
		);
		
		$fieldset->addField('sort_order',
			'text',
			array(
				'name'		=> 'sort_order',
				'label'		=> $this->__('Position'),
				'title'		=> $this->__('Position'),
				'class'		=> 'validate-digits',
				'style'		=> 'width:30px!important;',
			)
		);
		
		$fieldset->addField('question_text',
			'editor', 
			array(
				'name'		=> 'question_text', 
				'label'		=> $this->__('Question Text'), 
				'title'		=> $this->__('Question Text'), 
				'required'	=> true, 
				'config'	=> Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
			)
		);
		
		$fieldsBlock = $this->getLayout()
			->createBlock(
				'belitsoft_survey/adminhtml_question_edit_fields',
				'',
				array('question_type' => $question->getQuestionType())
			);
			
		$fieldset->addField('fields_box',
			'note',
			array(
				'label'		=> $this->__('Question options'),
				'text'		=> '<div id="'.$this->getFieldsDetailsAreaId().'" style="width:600px;">' . $fieldsBlock->toHtml() . '</div>',
			)
		);
		
        if($id) {
			$fieldset->addField('question_id',
				'hidden',
				array(
					'name' => 'question_id'
				)
			);
		}
		
		$question->setSortOrder((intval($question->getSortOrder())));
		
		$form->setValues($question->getData());
		$form->setUseContainer(true);
		$form->setAction($this->getSaveUrl());
		$form->setId($this->getEditFormId());
		$form->setMethod('post');

		$this->setForm($form);
		
		$question_types = Mage::getModel('belitsoft_survey/question')->getQuestionTypes();
		$js = "\n			var questionTypes = {};";
		foreach($question_types as $question_type_name=>$question_type_label) {
			$js .= "\n			questionTypes['$question_type_name'] = \"$question_type_label\";";
		}
		
		$this->getParentBlock()->addFormScripts($js."
			var fieldType = function() {
				return {
					updateFields: function() {
//						if ($('{$this->getQuestionTypeFieldName()}').value != '') {
							var elements = [$('{$this->getQuestionTypeFieldName()}')].flatten();
							$('{$this->getParentBlock()->getSaveButtonId()}').disabled = true;
							$('{$this->getParentBlock()->getSaveContinueButtonId()}').disabled = true;
							new Ajax.Updater('{$this->getFieldsDetailsAreaId()}', '{$this->getUrl('*/*/loadField')}',
								{
									parameters: Form.serializeElements(elements),
									evalScripts: true,
									onComplete: function(){
										$('{$this->getParentBlock()->getSaveButtonId()}').disabled = false;
										$('{$this->getParentBlock()->getSaveContinueButtonId()}').disabled = false;
									}
								}
							);
//						}
					}
				}
			}();
			
			Event.observe(window, 'load', function() {
				if ($('{$this->getQuestionTypeFieldName()}')) {
					Event.observe($('{$this->getQuestionTypeFieldName()}'), 'change', fieldType.updateFields);
				}
			});
			
			function saveQuestionEdit(action_str) {
				if(!action_str) {
					action_str = '';
				}
				try {
					if(!surveyOperations.save()) {
						return false;
					}
				} catch(e) {}
				
				editForm.submit(action_str);
			}
			
			function saveAndContinueQuestionEdit() {
				saveQuestionEdit($('{$this->getEditFormId()}').action+'back/edit/');
			}
		");
		
		return parent::_prepareForm();
	}
	
	/**
	 * Helper function to load question types array
	 */
	protected function _getQuestionTypesValuesForForm()
	{
		$return = Mage::registry('survey_question')->getQuestionTypesForForm();
		
		$first_value = array(
			'value'	=> '',
			'label'	=> Mage::helper('adminhtml')->__('-- Please Select --')
		);
		
		array_unshift($return, $first_value);
		
		return $return;
	}

	/**
	 * Helper function to load categories collection
	 *
	 */
	protected function _getSurveysValuesForForm()
	{
		return Mage::getResourceModel('belitsoft_survey/survey_collection')->toOptionArray();
	}

	/**
	 * Helper function to get question type field id
	 *
	 * @return string Returns id.
	 */
	public function getQuestionTypeFieldName() 
	{
		return 'question_type';
	}
	
	/**
	 * Helper function to get edit form id
	 *
	 * @return string Returns id.
	 */
	public function getEditFormId() 
	{
		return 'edit_form';
	}
	
	/**
	 * Helper function to get fields details area id
	 *
	 * @return string Returns id.
	 */
	public function getFieldsDetailsAreaId() 
	{
		return 'fields_details';
	}
	
	public function getSaveUrl()
	{
		return $this->getUrl('*/*/save');
	}
}
