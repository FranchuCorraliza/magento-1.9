<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */
class Belitsoft_Survey_Block_Adminhtml_Question_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	/**
	 * Constructor for the Survey edit form
	 *
	 */
	public function __construct()
	{
		$this->_objectId = 'question_id';
		$this->_blockGroup = 'belitsoft_survey';
		$this->_controller = 'adminhtml_question';
		
		parent::__construct();
		
		$this->_removeButton('reset');
		$this->_updateButton('save', 'label', $this->__('Save question'));
		$this->_updateButton('save', 'id', $this->getSaveButtonId());
		$this->_updateButton('save', 'onclick', 'saveQuestionEdit()');
		$this->_updateButton('delete', 'label', $this->__('Delete question'));
		
		$this->_addButton('saveandcontinue',
			array (
				'label'		=> $this->__('Save and continue edit'),
				'id'		=> $this->getSaveContinueButtonId(), 
				'onclick'	=> 'saveAndContinueQuestionEdit()', 
				'class'		=> 'save'
			),
			-100
		);
	}
	
	/**
	 * Helper function to get save button id
	 *
	 * @return string Returns id.
	 */
	public function getSaveButtonId()
	{
		return 'save_button';
	}
	
	/**
	 * Helper function to get save and continue button id
	 *
	 * @return string Returns id.
	 */
	public function getSaveContinueButtonId()
	{
		return 'save_continue_button';
	}
	
	/**
	 * Helper function to edit the header of the current form
	 *
	 * @return string Returns an "edit" or "new" text depending on the type of modifications.
	 */
	public function getHeaderText()
	{
		if ($question_id = Mage::registry('survey_question')->getQuestionId()) {
			return $this->__("Edit Question #%s", $question_id);
		} else {
			return $this->__('New Question');
		}
	}


	public function addFormScripts($js)
	{
		$this->_formScripts[] = $js;
	}
	
	
	/**
	 * Returns the CSS class for the header
	 * 
	 * Usually 'icon-head' and a more precise class is returned. We return
	 * only an empty string to avoid spacing on the left of the header as we
	 * don't have an icon.
	 * 
	 * @return string
	 */
	public function getHeaderCssClass()
	{
		return '';
	}
}
