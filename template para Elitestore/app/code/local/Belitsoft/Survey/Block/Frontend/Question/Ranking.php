<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */

class Belitsoft_Survey_Block_Frontend_Question_Ranking extends Belitsoft_Survey_Block_Frontend_Question_Abstract
{
	/**
	 * Function to gather the field's ranks of question
	 *
	 * @return array
	 */
	public function getRanks()
	{
		return $this->getCurrentQuestion()->getRanks();
	}
		
	/**
	 * Function to gather the field's ranks objects
	 *
	 * @return array
	 */
	public function getRanksObjects()
	{
		return $this->getCurrentQuestion()->getRanksObjects();
	}

	/**
	 * Function to get if exists data that the user entered previously
	 *
	 * @param string $el_name Element name
	 * @param string $value Element value
	 * @return string
	 */
	public function getRankingSelected($el_name, $value)
	{
		$data = $this->getUserData();
		if(array_key_exists($el_name, $data) && ($data[$el_name] == $value)) {
			return ' selected="true"';
		}
		
		return '';
	}
	
	/**
	 * Function to get html select field options of the rank's objects
	 *
	 * @param string $id Select element name
	 * @return string
	 */
	public function getRanksOptions($name)
	{
		$options = array();
		$options[] = '<option value="">'.$this->__('- Select rank -').'</option>';
		foreach($this->getRanksObjects() as $rank) {
			$value = $rank->getId();
			$options[] = '<option value="'.$value.'"'.$this->getRankingSelected($name, $value).'>'.$rank->getFieldText().'</option>';
		}
		
		return implode("\n", $options);
	}
}