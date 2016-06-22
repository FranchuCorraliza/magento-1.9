<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */

class Belitsoft_Survey_Block_Frontend_Question_Pickmany extends Belitsoft_Survey_Block_Frontend_Question_Abstract
{
	/**
	 * Function to get if exists data that the user entered previously
	 *
	 * @param string $el_name Element name
	 * @param string $value Element value
	 * @return string
	 */
	public function getPickmanySelected($el_name, $value)
	{
		$data = $this->getUserData();

		if(array_key_exists($el_name, $data)) {
			if(is_array($data[$el_name]) && (in_array($value, $data[$el_name]) !== false) ) {
				return ' checked="true"';
			}
		}

		return '';
	}
}