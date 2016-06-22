<?php
/**
 * Survey widgets controller for CMS WYSIWYG
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Mageplace
 */
class Belitsoft_Survey_Adminhtml_Survey_WidgetController extends Mage_Adminhtml_Controller_Action
{
	/**
	 * Chooser Source action
	 */
	public function chooserAction()
	{
		$this->getResponse()->setBody(
			$this->_getSurveyBlock()->toHtml()
		);
	}

	protected function _getSurveyBlock()
	{
		return $this->getLayout()
			->createBlock('belitsoft_survey/adminhtml_survey_widget_chooser',
				'',
				array(
					'id' => $this->getRequest()->getParam('uniq_id')
				)
			);
	}
}