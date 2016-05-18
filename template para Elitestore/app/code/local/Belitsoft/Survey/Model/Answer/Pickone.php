<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */
class Belitsoft_Survey_Model_Answer_Pickone extends Belitsoft_Survey_Model_Answer
{
	const QUESTION_TYPE = 'pickone';
		
	public function getAnswerType()
	{
		return self::QUESTION_TYPE;
	}
	
	/**
	 * Get answer data
	 * 
	 * @param array $user_data
	 *
	 * @return array Ex.: array(field_id => array())
	 */
	protected function _getAnswer($user_data=array(), $fields=array())
	{
		$answer = '';
		$user_data_key = $this->getAnswerKey($this->getQuestionId());
		if(is_array($user_data) && array_key_exists($user_data_key, $user_data)) {
			$answer = $user_data[$user_data_key];
		}

		$return = array();
		if($answer) {
			$return[$answer] = array();
		}
		
		return $return;
	}
}