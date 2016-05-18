<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */
class Belitsoft_Survey_Model_Answer extends Mage_Core_Model_Abstract
{
	const QUESTION_TYPE = 'default';
	
	protected $_survey_id;
	protected $_question_id;

	/**
	 * Constructor
	 */
	protected function _construct()
	{
		$this->_init('belitsoft_survey/answer');
	}

	public function getAnswerType()
	{
		return self::QUESTION_TYPE;
	}	
		
	public function getAnswerKey($id)
	{
		return $this->getAnswerType().'_'.$id;
	}
	
	/**
	 * Save user answers
	 * 
	 * @param Belitsoft_Survey_Model_Question $question
	 */
	public function saveAnswer(Belitsoft_Survey_Model_Question $question)
	{
		$this->setCustomerId($question->getCustomerId());
		$this->setStartId($question->getStartId());
		$this->setSurveyId($question->getSurveyId());
		$this->setQuestionId($question->getQuestionId());

		//Don't change rows direction!!!
		$answers = $this->_getAnswer($question->getUserData(), $question->getFieldsObjects());
#		Mage::log('ANSWERS');
#		Mage::log($answers);
		if(is_array($answers)) {
			foreach($answers as $field_id=>$ranking_array) {
				$this->setAnswer($field_id);
				if(is_array($ranking_array) && count($ranking_array)) {
					foreach($ranking_array as $rank) {
						$this->setAnswerField($rank);
#						Mage::log('SAVE ANSWER - RANKING');
#						Mage::log($this);
						$this->setAnswerId(null);
						$this->save();
					}
				} else {
#					Mage::log('SAVE ANSWER - PICKONE/PICKMANY');
#					Mage::log($this);
					$this->setAnswerId(null);
					$this->save();
				}
			}
		} else if(is_string($answers)) {
			$this->setAnswerText($answers);
#			Mage::log('SAVE ANSWER - DEFAULT');
#			Mage::log($this);
			$this->setAnswerId(null);
			$this->save();			
		}
		
		return $this;
	}
	
	/**
	 * Delete all entries from DB with passed start_id

	 * @param int $start_id
	 */
	public function deleteAnswers($start_id)
	{
		$this->_getResource()->deleteAnswers($start_id);
	}
	
	/**
	 * Get max start id
	 * 
	 * @param int
	 */
	public function getMaxStartId()
	{
		return $this->getResource()->getMaxStartId();
	}
	
	/**
	 * Get count start id
	 * 
	 * @param int
	 */
	public function getCountStartId($survey_id=0, $question_id=0)
	{
		return $this->getResource()->getCountStartId($survey_id, $question_id);
	}
	
	/**
	 * Get count start id
	 * 
	 * @param int
	 */
	public function getAllAnswers($survey_id=0, $question_id=0, $field_id=0)
	{
		return $this->getResource()->getAllAnswers($survey_id, $question_id, $field_id);
	}
	
	public function getUserDataKey($question)
	{
		return $this->getAnswerKey($question->getId());
	}
	
	public function getAswerWithKey($question, $answer) 
	{
		$qid = $question->getId();
		$answer_key = $this->getAnswerKey($qid);
		if(is_array($answer)) {
			$answer = array_pop($answer);
		}
		
		$return = array();
		if($answer instanceof Belitsoft_Survey_Model_Answer) {
			if($a = $answer->getAnswer()) {
				$return[$answer_key] = $a;
			} else if($at = $answer->getAnswerText()) {
				$return[$answer_key] = $at;
			}
		}
		
		return $return;
	}
	
	public function getAnswerString()
	{
		$field_text = '';
		if($field_id = (int)$this->getAnswer()) {
			$field = Mage::getModel('belitsoft_survey/field')->load($field_id);
			$field_text = $field->getFieldText();
		} else if($field_text = $this->getAnswerText()) {
		}
		
		return $field_text;
	}
	
	public function isOwner($aid, $customerId=null)
	{
		$aid = intval($aid);
		if(!$aid) {
			return false;
		}
		
		if(!$customerId) {
			$customerId = Mage::getSingleton('customer/session')->getCustomerId();
		} 
		
		if(!$customerId) {
			return false;
		}
		
		$read = $this->_getResource()->getReadConnection();
		$select = $read->select();
		$select->from($this->_getResource()->getMainTable())
			->where('`start_id` = ?', $aid)
			->where("`customer_id` = ?", $customerId);

		$data = $read->fetchOne($select); 
		
		return (empty($data) ? false : true);
	}
	
	public function hasCustomerSurveyAswers($surveyId, $customerId=null)
	{
		$surveyId = intval($surveyId);
		if(!$surveyId) {
			return false;
		}
		
		if(!$customerId) {
			$customerId = Mage::getSingleton('customer/session')->getCustomerId();
		} 
		
		if(!$customerId) {
			return false;
		}
		
		$read = $this->_getResource()->getReadConnection();
		$select = $read->select();
		$select->from($this->_getResource()->getMainTable())
			->where('`survey_id` = ?', $surveyId)
			->where("`customer_id` = ?", $customerId);

		$data = $read->fetchOne($select); 
		
		return (empty($data) ? false : true);
	}
	
	

	/**
	 * Set answer data
	 * 
	 * @param array $user_data
	 * @param array $fields
	 * 
	 * @return array|string
	 * Ex. array for pickone: array(field_id => array())
	 * Ex. array for pickmany: array(field_id_1 => array(), field_id_2 => array())
	 * Ex. array for ranking: array(field_id_1 => array(rank_id), field_id_2 => array(rank_id))
	 * Ex. array for multiple ranking: array(field_id_1 => array(rank_id_1, rank_id_2), field_id_2 => array(rank_id_1, rank_id_2))
	 * 
	 */
	protected function _getAnswer($user_data=array(), $fields=array())
	{
		$answer = '';
		$user_data_key = $this->getAnswerKey($this->getQuestionId());
		if(is_array($user_data) && array_key_exists($user_data_key, $user_data)) {
			$answer = $user_data[$user_data_key];
		}
		
		return $answer;
	}
}
