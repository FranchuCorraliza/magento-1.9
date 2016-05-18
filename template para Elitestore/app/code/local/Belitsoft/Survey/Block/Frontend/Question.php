<?php
/**
 * Mageplace Survey
 *
 * @category	Belitsoft
 * @package		Belitsoft_Survey
 * @copyright	Copyright (c) 2012 Mageplace. (http://www.mageplace.com)
 * @license		http://www.mageplace.com/disclaimer.html
 */

class Belitsoft_Survey_Block_Frontend_Question extends Belitsoft_Survey_Block_Frontend_Abstract
{
	/**
	 * Function to gather the current question
	 *
	 * @return Belitsoft_Survey_Model_Question The current question
	 */
	public function getCurrentQuestion()
	{
		return Mage::registry('survey_current_question');
	}

	/**
	 * Function to gather the current stage
	 *
	 * @return int The current stage
	 */
	public function getCurrentStage()
	{
		$stage = $this->getData('stage');
		if (is_null($stage)) {
			$stage = Mage::registry('survey_current_stage');
			$this->setData('stage', $stage);
		}

		return $stage;
	}

	/**
	 * Function to gather the current question collection
	 *
	 * @return array The current question array
	 */
	public function getQuestions()
	{
		$questions = $this->getData('questions');
		if (is_null($questions)) {
			$questions = Mage::registry('survey_questions');
			$this->setData('questions', $questions);
		}

		return $questions;
	}

	public function getQuestionOptions()
	{
		$current_question = $this->getCurrentQuestion();
		$type = $current_question->getQuestionType();

		if(!$child = $this->getChild('survey_question_'.$type)) {
			$child = $this->getChild('survey_question_default');
		}
		$child->setData('current_question', $this->getCurrentQuestion());

		return $child->toHtml();
	}

	public function isQuestionsList()
	{
		return Mage::registry('survey_questions_list');
	}

	public function isPrevButtonShow()
	{
		return Mage::registry('survey_prev_button_show');
	}

	public function isNextButtonShow()
	{
		return Mage::registry('survey_next_button_show');
	}

	public function isFinishPage()
	{
		return Mage::registry('survey_finished');
	}

	public function getSurveyContinueFormActionUrl()
	{
		return Mage::helper('belitsoft_survey')->getSurveyUrl($this->getSurvey(), 'question');
	}
}