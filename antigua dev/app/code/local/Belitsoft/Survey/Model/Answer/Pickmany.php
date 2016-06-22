<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */
class Belitsoft_Survey_Model_Answer_Pickmany extends Belitsoft_Survey_Model_Answer
{
	const QUESTION_TYPE = 'pickmany';
		
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
		$qid = $question->getId();
		$answer_key = $this->getAnswerKey($qid);
		
		foreach($answer as $item) {
			if($item instanceof Belitsoft_Survey_Model_Answer) {
				if($a = $item->getAnswer()) {
					$return[$answer_key][] = $a;
				}							
			}
		}
		
		return $return;
	}
	
	/**
	 * Set answer data
	 * 
	 * @param array $user_data
	 *
	 * @return array Ex.: array(field_id_1 => array(), field_id_2 => array())
s	 */
	protected function _getAnswer($user_data=array(), $fields=array())
	{
		$return = array();
		$user_data_key = $this->getAnswerKey($this->getQuestionId());
		if(is_array($user_data) && array_key_exists($user_data_key, $user_data)) {
			foreach($user_data[$user_data_key] as $field_id) {
				$return[$field_id] = array();
			}
		}
		
		return $return;
	}
}
