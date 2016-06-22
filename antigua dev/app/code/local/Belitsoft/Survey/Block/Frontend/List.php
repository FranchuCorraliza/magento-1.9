<?php
/**
 * Mageplace Survey
 *
 * @category	Belitsoft
 * @package		Belitsoft_Survey
 * @copyright	Copyright (c) 2012 Mageplace. (http://www.mageplace.com)
 * @license		http://www.mageplace.com/disclaimer.html
 */

class Belitsoft_Survey_Block_Frontend_List extends Belitsoft_Survey_Block_Frontend_Abstract
{
	/**
	 * Load survey collection
	 *
	 * @return Belitsoft_Survey_Model_Mysql4_Survey_Collection
	 */
	protected function getSurveyCollection()
	{
		$surveyCollection = $this->getData('survey_collection');
		if (is_null($surveyCollection)) {
			$surveyCollection = Mage::registry('survey_collection');
			$this->setData('survey_collection', $surveyCollection);
		}

		return $surveyCollection;
	}

	public function hasSurveys()
	{
		return ($this->countSurveys() > 0);
	}

	public function countSurveys()
	{
		return $this->getSurveyCollection()->getSize();
	}

	public function getIntro($survey)
	{
		return Mage::helper('belitsoft_survey')->getIntroText($survey->getSurveyDescription());
	}

	public function getSurveyLink($survey)
	{
		return Mage::helper('belitsoft_survey')->getSurveyUrl($survey);
	}
}
