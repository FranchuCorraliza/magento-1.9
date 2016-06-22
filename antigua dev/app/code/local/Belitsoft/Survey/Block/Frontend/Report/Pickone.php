<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */

class Belitsoft_Survey_Block_Frontend_Report_Pickone extends Belitsoft_Survey_Block_Frontend_Report_Default
{
	const QUESTION_TYPE = 'pickone';

	protected $_selected;
	
	protected function _getQuestionType()
	{
		return self::QUESTION_TYPE;
	}
		
	public function checkSelected($field)
	{
		if(is_null($this->_selected))	{
			$answers = $this->getCurrentQuestion()->getAnswers();
			if(!empty($answers[0]) && ($answers[0] instanceof Belitsoft_Survey_Model_Answer)) {
				$this->_selected = (int) $answers[0]->getAnswer();
			} else {
				$this->_selected = 0;
			}
		}
		
		return ($field->getId() == $this->_selected);
	}
}