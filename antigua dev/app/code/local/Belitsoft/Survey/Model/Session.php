<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */
class Belitsoft_Survey_Model_Session extends Mage_Core_Model_Session_Abstract
{
	const MODE_EDIT	= 'edit';
	const MODE_VIEW	= 'view';
	const MODE_NEW	= 'new';
	
	public function __construct()
	{
		$namespace = 'belitsoft_survey' . '_' . (Mage::app()->getStore()->getWebsite()->getCode());
		$this->init($namespace);
	}
	
	public function startSurvey(Belitsoft_Survey_Model_Survey $survey = null, array $questions = null)
	{
		$this->clear();
		
		if(!is_null($survey)) {
			$this->setSurvey($survey);
		}
		
		if(!is_null($questions)) {
			$this->setNextQuestionsArray($questions);
		}
		
		$this->setPrevQuestionsArray(array());
		
		$this->setSurveyStage(1);
		$this->setSurveyStarted(true);
		
		return $this;
	}
	
	public function finishSurvey($post = array())
	{
		$questions = $this->getQuestions();
		$last_object = array_pop($questions);
		if(!$this->isViewMode()) {
			$last_object->setData('user_data', $post);
		}
		array_push($questions, $last_object);
		
		$mode = $this->getMode();
		$this->clear();
		$this->setSurveyFinished(true);
		$this->setMode($mode);
		
		return $questions;
	}
	
	public function setEditMode($start_id)
	{
		$start_id = (int)$start_id;
		if($start_id > 0) {
			$this->setMode(self::MODE_EDIT);
			$this->setStartId($start_id);
		}
		
		return $this;
	}
	
	public function setViewMode($start_id)
	{
		$start_id = (int)$start_id;
		if($start_id > 0) {
			$this->setMode(self::MODE_VIEW);
			$this->setStartId($start_id);
		}
		
		return $this;
	}
	
	public function getMode()
	{
		$mode = $this->_getData('mode');
		if(!$mode) {
			$mode = self::MODE_NEW;
		}
		return $mode;
	}

	public function isEditMode()
	{
		return $this->getMode() == self::MODE_EDIT;
	}
	
	public function isViewMode()
	{
		return $this->getMode() == self::MODE_VIEW;
	}
	
	public function nextSurveyQuestion($post = array())
	{
		//Set survey stage to next level
		$stage = (int) $this->getSurveyStage();
		$this->setSurveyStage(++$stage);
		
		$prev = $this->getPrevQuestionsArray();
		$next = $this->getNextQuestionsArray();
		
		$prev_object = array_shift($next);
		if(!$this->isViewMode()) {
			$prev_object->user_data = $post;
		}
		array_push($prev, $prev_object);
		$this->setPrevQuestionsArray($prev);
		
		reset($next);
		$this->setNextQuestionsArray($next);
	}
	
	public function prevSurveyQuestion()
	{
		//Set survey stage to previos level
		$stage = (int) $this->getSurveyStage();
		$this->setSurveyStage(--$stage);

		$prev = $this->getPrevQuestionsArray();
		$next = $this->getNextQuestionsArray();
		
		$next_object = array_pop($prev);
		array_unshift($next, $next_object);
		reset($next);
		$this->setNextQuestionsArray($next);
		
		$this->setPrevQuestionsArray($prev);
	}
	
	public function getPrevButtonShow()
	{
		return count((array)$this->getPrevQuestionsArray()) > 0;
	}
	
	public function getNextButtonShow()
	{
		return count((array)$this->getNextQuestionsArray()) > 0;
	}
	
	public function initFromArray($array)
	{
		if(empty($array) || !is_array($array)) {
			return $this;
		}
		
		$prev = $this->getPrevQuestionsArray();
		foreach($prev as $key=>$question) {
			$qid = $question->getQuestionId();
			if(array_key_exists($qid, $array)) {
				$prev[$key]->user_data = $array[$qid];
			}
		}
		$this->setPrevQuestionsArray($prev);
		
		$next = $this->getNextQuestionsArray();
		foreach($next as $key=>$question) {
			$qid = $question->getQuestionId();
			if(array_key_exists($qid, $array)) {
				$next[$key]->user_data = $array[$qid];
			}
		}
		$this->setNextQuestionsArray($next);
				
		return $this;
	}
	
	/**
	 * Get count questions array 
	 * 
	 * @return array
	 */
	public function countQuestions()
	{
		return count($this->getQuestions());
	}
		
	/**
	 * Get next survey's questions array
	 * 
	 * @return array
	 */
	public function getQuestions()
	{
		return array_merge((array)$this->getPrevQuestionsArray(), (array)$this->getNextQuestionsArray());
	}

	/**
	 * Get current survey's question object
	 * 
	 * @return Belitsoft_Survey_Model_Question
	 */
	public function getCurrentQuestion()
	{
		return current((array)$this->getNextQuestionsArray());	
	}
	
	/**
	 * Check is survey was started
	 * 
	 * @return boolean
	 */
	public function isStarted()
	{
		return $this->getSurveyStarted();
	}
		
	/**
	 * Check is survey was finished
	 * 
	 * @return boolean
	 */
	public function isFinished()
	{
		return $this->getSurveyFinished();
	}
}
