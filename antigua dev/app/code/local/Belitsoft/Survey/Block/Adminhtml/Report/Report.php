<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */
class Belitsoft_Survey_Block_Adminhtml_Report_Report extends Mage_Adminhtml_Block_Widget_Form_Container
{
	/**
	 * Constructor for the Survey edit form
	 */
	public function __construct()
	{
		$this->_objectId = 'start_id';
		$this->_blockGroup = 'belitsoft_survey';
		$this->_controller = 'adminhtml_report';
		$this->_mode = 'report';
		
		parent::__construct();
		
		$this->_removeButton('reset');
		$this->_removeButton('save');
		
		$this->_addButton('report_pdf',
			array(
				'label'		=> $this->__('Export to PDF'),
				'onclick'	=> 'setLocation(\'' . $this->getUrl('*/*/pdf/', array('start_id'=>intval($this->getRequest()->getParam('start_id')))).'\')',
			)
		);
	}
	
	/**
	 * Helper function to edit the header of the current form
	 *
	 * @return string
	 */
	public function getHeaderText()
	{
		return $this->__('Report');
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
