<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */
class Belitsoft_Survey_Block_Adminhtml_Report_Report_Form extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Preperation of current form
	 *
	 * @return Belitsoft_Survey_Block_Adminhtml_Report_Report_Form
	 */
	protected function _prepareForm()
	{
		$survey = Mage::registry('survey_survey');
		$questions = Mage::registry('survey_questions');
		$answers = Mage::registry('survey_answers');
		
		$form = new Varien_Data_Form();

		$reportBlock = $this->getLayout()->createBlock('belitsoft_survey/frontend_report');
		$form->addField('graphics_box',
			'note',
			array(
				'text'	=> '<div id="survey_report_area">' . $reportBlock->toHtml() . '</div>',
			)
		);
		
/*
		$form->setUseContainer(true);
		$form->setAction($this->getActionUrl());
		$form->setId('edit_form');
		$form->setMethod('post');
*/

		$this->setForm($form);
		
		return parent::_prepareForm();
	}
}
