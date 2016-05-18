<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */
class Belitsoft_Survey_Model_Answer_Ranking extends Belitsoft_Survey_Model_Answer
{
	const QUESTION_TYPE = 'ranking';
		
	public function getAnswerType()
	{
		return self::QUESTION_TYPE;
	}
	
	public function getAswerWithKey($question, $answer) 
	{
		$return = array();
		
		if(!is_array($answer)) {
			return $return;
		}
		
		$answer_keys = $this->getUserDataKey($question);
		if(!is_array($answer_keys)) {
			return $return;
		}
					
		foreach($answer_keys as $answer_key) {
			foreach($answer as $alp) {
				if($answer_key == $this->getAnswerKey($alp->getAnswer())) {
					$return[$answer_key] = $alp->getAnswerField();
				}
			}
		}

						
		return $return;
	}
	
	public function getUserDataKey($question)
	{
		$fields = $question->getFields();
		if(empty($fields) || !is_array($fields)) {
			return array();
		}

		$keys = array();
		foreach($fields as $field) {
			if($field instanceof Belitsoft_Survey_Model_Field) {
				$keys[] = $this->getAnswerKey($field->getId());
			} elseif(is_array($field)) {
				$keys[] = $this->getAnswerKey($field['field_id']);
			}
		}

		return $keys;
	}
	
	public function getAnswerString()
	{
		$field_text = '';
		if($field_id = (int)$this->getAnswer()) {
			$field = Mage::getModel('belitsoft_survey/field')->load($field_id);
			$field_text .= $field->getFieldText();
		}
		if($field_id = (int)$this->getAnswerField()) {
			$field = Mage::getModel('belitsoft_survey/field')->load($field_id);
			$field_text .= ': ' . $field->getFieldText();
		}
		
		return $field_text;
	}
	
	/**
	 * Set answer data
	 * 
	 * @param array $user_data
	 *
	 * @return array Ex.: array(field_id_1 => array(rank_id), field_id_2 => array(rank_id))
	 */
	protected function _getAnswer($user_data=array(), $fields=array())
	{
		$return = array();
		
		foreach($fields as $field) {
			/* @var $field Varien_Object */
			$user_data_key = $this->getAnswerKey($field->getId());
			$temp_arr = array();
			$temp_arr[] = $user_data[$user_data_key];
			$return[$field->getId()] = $temp_arr;
		}
		
		return $return;
	}
}
