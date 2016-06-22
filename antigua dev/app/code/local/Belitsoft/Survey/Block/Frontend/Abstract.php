<?php

/**
 * Mageplace Survey
 *
 * @category    Belitsoft
 * @package        Belitsoft_Survey
 * @copyright    Copyright (c) 2012 Mageplace. (http://www.mageplace.com)
 * @license        http://www.mageplace.com/disclaimer.html
 */
class Belitsoft_Survey_Block_Frontend_Abstract extends Mage_Core_Block_Template
{
	protected function _prepareLayout()
	{
		parent::_prepareLayout();

		if (!Mage::registry('belitsoft_survey_meta_set') && ($head = $this->getLayout()->getBlock('head'))) {
			$category = $this->getCategory();
			$survey   = $this->getSurvey();

			$title = $description = $keywords = null;
			if ($survey) {
				$title       = $this->htmlEscape($survey->getSurveyName()) . ($category ? ' - ' . $this->htmlEscape($category->getCategoryName()) : '') . ' - ' . $head->getTitle();
				$description = $this->htmlEscape($survey->getSurveyMetaDescription());
				$keywords    = $this->htmlEscape($survey->getSurveyMetaKeywords());
			} else if ($category) {
				$title       = $this->htmlEscape($category->getCategoryName()) . ' - ' . $head->getTitle();
				$description = $this->htmlEscape($category->getCategoryMetaDescription());
				$keywords    = $this->htmlEscape($category->getCategoryMetaKeywords());
			}

			if (!$title) {
				$title = $this->htmlEscape($this->__('Survey')) . ' - ' . $head->getTitle();
			}

			if (!$description) {
				$description = $this->htmlEscape(Mage::getModel('belitsoft_survey/config')->getConfigData('meta_description'));
			}

			if (!$keywords) {
				$keywords = $this->htmlEscape(Mage::getModel('belitsoft_survey/config')->getConfigData('meta_keywords'));
			}

			if ($title) {
				$head->setTitle($title);
			}

			if ($description) {
				$head->setDescription($description);
			}

			if ($keywords) {
				$head->setKeywords($keywords);
			}

			Mage::unregister('belitsoft_survey_meta_set');
			Mage::register('belitsoft_survey_meta_set', true);
		}

		return $this;
	}

	/**
	 * Function to gather the current survey category
	 *
	 * @return Belitsoft_Survey_Model_Category The current category
	 */
	public function getCategory()
	{
		$category = $this->getData('category');
		if (is_null($category)) {
			$category = Mage::registry('survey_current_category');
			$this->setData('category', $category);
		}

		return $category;
	}

	/**
	 * Function to gather the current survey
	 *
	 * @return Belitsoft_Survey_Model_Survey The current survey
	 */
	public function getSurvey()
	{
		$survey = $this->getData('survey');
		if (is_null($survey)) {
			$survey = Mage::registry('survey_current_survey');
			$this->setData('survey', $survey);
		}

		return $survey;
	}

	public function isEditMode()
	{
		return Mage::helper('belitsoft_survey')->isEditMode();
	}

	public function isViewMode()
	{
		return Mage::helper('belitsoft_survey')->isViewMode();
	}

	public function getBackUrl()
	{
		return Mage::helper('belitsoft_survey')->getSurveyManagePageUrl();
	}

	public function filter($text)
	{
		return Mage::helper('cms')->getBlockTemplateProcessor()->filter($text);
	}
}