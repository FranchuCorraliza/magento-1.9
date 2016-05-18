<?php
/**
 * Mageplace Survey
 *
 * @category	Belitsoft
 * @package		Belitsoft_Survey
 * @copyright	Copyright (c) 2012 Mageplace. (http://www.mageplace.com)
 * @license		http://www.mageplace.com/disclaimer.html
 */

class Belitsoft_Survey_Block_Frontend_Detail extends Belitsoft_Survey_Block_Frontend_Abstract
{
	public function getQuestionsHtml()
	{
		Mage::register('survey_questions_list', true);
		return $this->getChildHtml('survey_questions');
	}

	public function getSurveyStartFormActionUrl()
	{
		return Mage::helper('belitsoft_survey')->getSurveyUrl($this->getSurvey(), 'question');
	}

	public function getSurveyRestrictedPageText()
	{
		return Mage::helper('belitsoft_survey')->__('Survey already taken');
	}

	public function isRestrictedPage()
	{
		return Mage::registry('survey_restricted');
	}

	public function isFinishPage()
	{
		return Mage::registry('survey_finished');
	}
}