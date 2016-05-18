<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */
class Belitsoft_Survey_Block_Adminhtml_Report_Results_Form extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Preperation of current form
	 *
	 * @return Belitsoft_Survey_Block_Adminhtml_Report_Results_Form
	 */
	protected function _prepareForm()
	{
		$survey = Mage::registry('survey_survey');
		$graphics = Mage::registry('survey_graphics');
		$survey_results = Mage::registry('survey_results_graphics');
		
		$form = new Varien_Data_Form();
		
		$fieldset = $form->addFieldset('base_fieldset',
			array(
				'legend'	=> $this->__('Survey Results'), 
				'class'		=> 'fieldset-wide'
			)
		);
		
		$fieldset->addField('survey_id',
			'select',
			array(
				'name'		=> 'survey_id', 
				'label'		=> $this->__('Survey'), 
				'title'		=> $this->__('Survey'), 
				'required'	=> true, 
				'value'		=> $survey->getId(),
				'values'	=> $this->_getSurveysValuesForForm(),
				'onchange'	=> 'editForm.submit($(\'edit_form\').action)'
			)
		);
		
		$output = array();
		foreach($survey_results as $result) {
			if($result->is_image === true) {
				$output[] = '<div><img src="'.$graphics->getImageUrl().$result->data.'" /></div>';
			} else {
				$id = md5(uniqid(time()));
				
				$text = '<div style="margin-bottom:20px;">';
				$text .= '<strong>'.$result->title.'</strong>';
				$text .= '<button type="button" onclick="javascript: $(\''.$id.'\').toggle();">'.$this->__('Show/Hide answers').'</button>';
				
				$text .= '<div id="'.$id.'" style="display:none;">';
				foreach($result->data as $user_answer) {
					$text .= '<div style="border-bottom: 1px solid #ccc">'.nl2br(Mage::helper('belitsoft_survey')->cleanText($user_answer)).'</div>';
				}
				$text .= '</div>';
				$text .= '</div>';
				
				$output[] = $text;
			}
		}
		
		$fieldset->addField('graphics_box',
			'note',
			array(
				'text'		=> '<div>' . implode("\n", $output) . '</div>',
			)
		);
		
		$form->setUseContainer(true);
		$form->setAction($this->getActionUrl());
		$form->setId('edit_form');
		$form->setMethod('post');

		$this->setForm($form);
		
		return parent::_prepareForm();
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
	 * Helper function to get fields details area id
	 *
	 * @return string Returns id.
	 */
	public function getFieldsDetailsAreaId() 
	{
		return 'fields_details';
	}
	
	public function getActionUrl()
	{
		return $this->getUrl('*/*/*');
	}
}
