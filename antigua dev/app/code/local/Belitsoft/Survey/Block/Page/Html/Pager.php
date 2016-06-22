<?php
/**
 * Mageplace Survey
 *
 * @category	Belitsoft
 * @package		Belitsoft_Survey
 * @copyright	Copyright (c) 2012 Mageplace. (http://www.mageplace.com)
 * @license		http://www.mageplace.com/disclaimer.html
 */

class Belitsoft_Survey_Block_Page_Html_Pager extends Mage_Page_Block_Html_Pager
{
	public function getCurrentPage()
	{
		if ($page = (int) $this->getRequest()->getParam($this->getPageVarName())) {
			return $page;
		}
		return 1;
	}

	public function getPagerUrl($params=array())
	{
		$urlParams = array();
		$urlParams['_current']		= true;
		$urlParams['_escape']		= true;
		$urlParams['_use_rewrite']	= true;
		$urlParams['_query']		= $params;
		
//		var_dump(Mage::helper('belitsoft_survey')->getSurveyManagePageUrl($urlParams)); die;
		
		return Mage::helper('belitsoft_survey')->getSurveyManagePageUrl($urlParams);
	}
}
