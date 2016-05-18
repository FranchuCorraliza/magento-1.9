<?php
/**
 * Mageplace Survey
 *
 * @category	Belitsoft
 * @package		Belitsoft_Survey
 * @copyright	Copyright (c) 2012 Mageplace. (http://www.mageplace.com)
 * @license		http://www.mageplace.com/disclaimer.html
 */

class Belitsoft_Survey_Block_Frontend_Catdetail extends Belitsoft_Survey_Block_Frontend_Abstract
{
	public function getSurveyListHtml()
	{
		return $this->getChildHtml('survey_list');
	}
}