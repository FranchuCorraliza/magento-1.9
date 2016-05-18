<?php
/**
 * Mageplace Survey
 *
 * @category	Belitsoft
 * @package		Belitsoft_Survey
 * @copyright	Copyright (c) 2012 Mageplace. (http://www.mageplace.com)
 * @license		http://www.mageplace.com/disclaimer.html
 */

if(Mage::helper('belitsoft_survey/version')->isEE()) {
	class Belitsoft_Survey_Helper_Data extends Belitsoft_Survey_Helper_Enterprise
	{
	}
} else {
	class Belitsoft_Survey_Helper_Data extends Belitsoft_Survey_Helper_Community
	{
	}
}