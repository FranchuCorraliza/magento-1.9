<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */

class Belitsoft_Survey_Block_Frontend_Report_Ranking extends Belitsoft_Survey_Block_Frontend_Report_Default
{
	const QUESTION_TYPE = 'ranking';

	protected $_selected;
	
	protected function _getQuestionType()
	{
		return self::QUESTION_TYPE;
	}
	
	public function getRankingAnswer($field)
	{
		if(is_null($this->_selected))	{
			$answers = $this->getCurrentQuestion()->getAnswers();
			if(is_array($answers)) {
				foreach($answers as $answer) {
					$this->_selected[$answer->getAnswer()] = (int) $answer->getAnswerField();
				}
			} else {
				$this->_selected = array();
			}
		}

		$answer = '';
		$id = (int) $field->getId();
		if(array_key_exists($id, $this->_selected)) {
			$rank = Mage::getModel('belitsoft_survey/field')->load($this->_selected[$id]);
			if(($rank instanceof Belitsoft_Survey_Model_Field) && ($rank->getIsMain == 0)) {
				$answer = $rank->getFieldText();
			}
		}

		return $answer;
	}
}