<?php
/**
 * Mageplace Survey
 *
 * @category	Belitsoft
 * @package		Belitsoft_Survey
 * @copyright	Copyright (c) 2012 Mageplace. (http://www.mageplace.com)
 * @license		http://www.mageplace.com/disclaimer.html
 */

class Belitsoft_Survey_Block_Customer_Form_Surveys extends Mage_Customer_Block_Account_Dashboard
{
	protected $_collection;

	protected function _construct()
	{
		$this->_collection = Mage::getResourceModel('belitsoft_survey/answer_collection')->setCustomerGridView(true);
		$this->_collection
			->addStoreFilter(Mage::app()->getStore()->getId())
			->addCustomerFilter(Mage::getSingleton('customer/session')->getCustomerId())
			->setDateOrder();
	}

	protected function _prepareLayout()
	{
		$toolbar = $this->getLayout()
			->createBlock('belitsoft_survey/page_html_pager', 'customer_surveys_list.toolbar')
			->setCollection($this->_getCollection());
		
		$this->setChild('toolbar', $toolbar);
		
		parent::_prepareLayout();
		
		if ($head = $this->getLayout()->getBlock('head')) {
			$head->setTitle($this->htmlEscape($this->__('My Surveys')));
			$head->setDescription($this->htmlEscape(Mage::getModel('belitsoft_survey/config')->getConfigData('meta_description')));
			$head->setKeywords($this->htmlEscape(Mage::getModel('belitsoft_survey/config')->getConfigData('meta_keywords')));
		}

		return $this;
	}

	public function getToolbarHtml()
	{
		return $this->getChildHtml('toolbar');
	}

	public function count()
	{
		return $this->_collection->getSize();
	}

	protected function _getCollection()
	{
		return $this->_collection;
	}

	public function getCollection()
	{
		return $this->_getCollection();
	}

	public function getSurveyLink($answer)
	{
		return Mage::helper('belitsoft_survey')->getSurveyUrl($answer->getSurveyId(), 'view', $answer->getStartId());
	}

	public function getSurveyEditLink($answer)
	{
		return Mage::helper('belitsoft_survey')->getSurveyUrl($answer->getSurveyId(), 'edit', $answer->getStartId());
	}

	public function dateFormat($date)
	{
		return $this->formatDate($date, Mage_Core_Model_Locale::FORMAT_TYPE_SHORT, true);
	}

	protected function _beforeToHtml()
	{
		$this->_getCollection()->load();
		
		return parent::_beforeToHtml();
	}
}
