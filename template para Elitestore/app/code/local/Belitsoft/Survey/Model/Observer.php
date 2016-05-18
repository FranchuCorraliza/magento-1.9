<?php
/**
 * Mageplace Survey
 *
 * @category	Belitsoft
 * @package		Belitsoft_Survey
 * @copyright	Copyright (c) 2012 Mageplace. (http://www.mageplace.com)
 * @license		http://www.mageplace.com/disclaimer.html
 */

class Belitsoft_Survey_Model_Observer
{
	public function processCoreBlockAbstractToHtmlBefore($observer)
	{
		$block = $observer->getBlock();
		
		/*if($block instanceof Mage_Page_Block_Html_Head) {
			$request = Mage::app()->getFrontController()->getRequest();
			$module_name = $request->getModuleName();
			$controller_name = $request->getControllerName();

			if($module_name == Belitsoft_Survey_Helper_Data::URL_PREFIX) {
				$description = $keywords = null;
				if($sid = $request->get('sid')) {
					$survey         = Mage::getModel('belitsoft_survey/survey')->load($sid);
					$description    = $survey->getSurveyMetaDescription();
					$keywords       = $survey->getSurveyMetaKeywords();
				} else if($cid = $request->get('cid')) {
					$category       = Mage::getModel('belitsoft_survey/category')->load($cid);
					$description    = $category->getCategoryMetaDescription();
					$keywords       = $category->getCategoryMetaKeywords();
				}

				if(!is_null($description)) {
					$observer->getBlock()->setDescription($description);
				}

				if(!is_null($keywords)) {
					$observer->getBlock()->setKeywords($keywords);
				}
			}
		}
		*/
		
		if(($block instanceof Mage_Customer_Block_Account_Navigation) && Mage::helper('belitsoft_survey')->isCustomerCanView())	{
			$block->addLink(
				'belitsoft_survey',
				Mage::helper('belitsoft_survey')->getSurveyManagePage(),
				Mage::helper('belitsoft_survey')->__('My Surveys')
			);
		}
	}
}