<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */

class Belitsoft_Survey_Block_Frontend_Report extends Mage_Core_Block_Template
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('survey/report.phtml');
		$this->setArea('frontend');
	}

	protected function _prepareLayout()
	{
		$question_types = Mage::getModel('belitsoft_survey/question')->getQuestionTypes();

		foreach($question_types as $question_type=>$question_type_name) {
			if(!$question_type || ($question_type == 'shortanswer')) {
				$question_type = 'default';
			}

			$this->setChild('survey_report_'.$question_type,
				$this->getLayout()->createBlock('belitsoft_survey/frontend_report_'.$question_type)
			);
		}

		return parent::_prepareLayout();
	}

	/**
	 * Function to get the survey
	 *
	 * @return Belitsoft_Survey_Model_Survey The survey
	 */
	public function getSurvey()
	{
		$survey = $this->getData('survey');
		if (is_null($survey)) {
			$survey = Mage::registry('survey_survey');
			$this->setData('survey', $survey);
		}

		return $survey;
	}

	/**
	 * Function to get the answer collection
	 *
	 * @return Belitsoft_Survey_Model_Mysql4_Answer_Collection The answer collection
	 */
	public function getAnswers()
	{
		$answers = $this->getData('answers');
		if (is_null($answers)) {
			$answers = Mage::registry('survey_answers');
			$this->setData('answers', $answers);
		}

		return $answers;
	}

	/**
	 * Function to get the first answer
	 *
	 * @return Belitsoft_Survey_Model_Answer The answer object
	 */
	public function getFirstAnswer()
	{
		$first_answer = $this->getData('first_answers');
		if (is_null($first_answer)) {
			$first_answer = Mage::registry('survey_first_answer');
			$this->setData('first_answers', $first_answer);
		}

		return $first_answer;
	}

	/**
	 * Function to get the question collection
	 *
	 * @return Belitsoft_Survey_Model_Mysql4_Question_Collection The question collection
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

	public function getPerformedDate()
	{
		return $this->getFirstAnswer()->getCreationDate();
	}

	public function getQuestionAnswersHTML($question)
	{
		Mage::unregister('survey_report_current_question');
		Mage::register('survey_report_current_question', $question);

		$type = $question->getQuestionType();
		if(!$type || ($type == 'shortanswer')) {
			$type = 'default';
		}

		$block = $this->getLayout()->createBlock('belitsoft_survey/frontend_report_'.$type);
		if($block) {
			return $block->toHtml();
		} else {
			return $this->getLayout()->createBlock('belitsoft_survey/frontend_report_default')->toHtml();
		}
	}
}