<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */
class Belitsoft_Survey_Block_Adminhtml_Survey_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	/**
	 * Constructor for the Survey edit form
	 *
	 */
	public function __construct()
	{
		$this->_objectId = 'survey_id';
        $this->_blockGroup = 'belitsoft_survey';
		$this->_controller = 'adminhtml_survey';
		
		parent::__construct();
		
		$this->_removeButton('reset');
		$this->_updateButton('save', 'label', $this->__('Save survey'));
		$this->_updateButton('delete', 'label', $this->__('Delete survey'));
		
		$this->_addButton('saveandcontinue',
			array (
				'label'		=> $this->__('Save and continue edit'), 
				'onclick'	=> 'saveAndContinueEdit()', 
				'class'		=> 'save'
			),
			-100
		);
		
		$this->_addButton('duplicate',
			array (
				'label'		=> Mage::helper('catalog')->__('Duplicate'), 
				'onclick'	=> 'duplicateSurvey()', 
				'class'		=> 'add'
			),
			-100
		);
		
		$this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
            function duplicateSurvey(){
                editForm.submit('".$this->getDuplicateUrl()."');
            }
        ";
	}
	
	/**
	 * Helper function to edit the header of the current form
	 *
	 * @return string Returns an "edit" or "new" text depending on the type of modifications.
	 */
	public function getHeaderText()
	{
		if (Mage::registry('survey')->getSurveyId()) {
			return $this->__("Edit Survey '%s'", $this->htmlEscape(Mage::registry('survey')->getSurveyName()));
		} else {
			return $this->__('New Survey');
		}
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

    public function getDuplicateUrl()
    {
        return $this->getUrl('*/*/duplicate', array('_current'=>true));
    }
}
