<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */
class Belitsoft_Survey_Block_Adminhtml_Report_Results extends Mage_Adminhtml_Block_Widget_Form_Container
{
	/**
	 * Constructor for the Result form
	 */
	public function __construct()
	{
		$this->_objectId = 'survey_id';
		$this->_blockGroup = 'belitsoft_survey';
		$this->_controller = 'adminhtml_report';
		$this->_mode = 'results';
		
		parent::__construct();
		
		$this->_addButton('results_pdf',
			array(
				'label'		=> $this->__('Export to PDF'),
				'onclick'	=> 'setLocation(\'' . $this->getUrl('*/*/pdf/', array('survey_id'=>intval($this->getRequest()->getParam('survey_id')))).'\')',
			)
		);
		
		$this->_removeButton('reset');
		$this->_removeButton('save');
		$this->_removeButton('delete');
	}
	
	/**
	 * Helper function to edit the header of the current form
	 *
	 * @return string
	 */
	public function getHeaderText()
	{
		return $this->__('Survey Results - %s', Mage::registry('survey_survey')->getSurveyName());
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
