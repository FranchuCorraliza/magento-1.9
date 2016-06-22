<?php
/**
 * Mageplace Survey
 *
 * @category	Belitsoft
 * @package		Belitsoft_Survey
 * @copyright	Copyright (c) 2012 Mageplace. (http://www.mageplace.com)
 * @license		http://www.mageplace.com/disclaimer.html
 */

class Belitsoft_Survey_Block_Frontend_Catlist extends Belitsoft_Survey_Block_Frontend_Abstract
{
	/**
	 * Returns all active categories
	 *
	 * @return Belitsoft_Survey_Model_Mysql4_Category_Collection
	 */
	public function getCategoryCollection()
	{
		$categories = $this->getData('category_collection');
		if (is_null($categories)) {
			$categories = Mage::registry('survey_category_collection');
			$this->setData('category_collection', $categories);
		}

		return $categories;
	}


	public function hasCategories()
	{
		return ($this->countCategories() > 0);
	}

	public function countCategories()
	{
		return $this->getCategoryCollection()->getSize();
	}

	public function getIntro($category)
	{
		return Mage::helper('belitsoft_survey')->getIntroText($category->getCategoryDescription());
	}

	public function getCategoryLink($category)
	{
		return Mage::helper('belitsoft_survey')->getCategoryUrl($category);
	}

	public function encodeQuestionForUrl($question)
	{
		return Mage::helper('belitsoft_survey')->encodeForUrl($question);
	}
}
