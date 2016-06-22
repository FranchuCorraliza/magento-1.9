<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */

class Belitsoft_Survey_Block_Frontend_Report_Pickmany extends Belitsoft_Survey_Block_Frontend_Report_Default
{
	const QUESTION_TYPE = 'pickmany';

	protected $_selected;
	
	protected function _getQuestionType()
	{
		return self::QUESTION_TYPE;
	}
	
	public function checkSelected($field)
	{
		if(is_null($this->_selected))	{
			$answers = $this->getCurrentQuestion()->getAnswers();
			if(is_array($answers)) {
				foreach($answers as $answer) {
					$this->_selected[] = (int) $answer->getAnswer();
				}
			} else {
				$this->_selected = array();
			}
		}
		
		return in_array($field->getId(), $this->_selected);
	}
}