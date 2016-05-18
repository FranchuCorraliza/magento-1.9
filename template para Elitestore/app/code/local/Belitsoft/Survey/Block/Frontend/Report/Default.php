<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */

class Belitsoft_Survey_Block_Frontend_Report_Default extends Mage_Core_Block_Template
{
	const QUESTION_TYPE = 'default';
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		$this->setArea('frontend');
		$this->setTemplate('survey/report/'.$this->_getQuestionType().'.phtml');
	}
	
	protected function _getQuestionType()
	{
		return self::QUESTION_TYPE;
	}
	
	/**
	 * Function to get the current question
	 *
	 * @return Belitsoft_Survey_Model_Question The question collection
	 */
	public function getCurrentQuestion()
	{
		$question = $this->getData('current_question');
		if (is_null($question)) {
			$question = Mage::registry('survey_report_current_question');
			$this->setData('current_question', $question);
		}
		
		return $question;
	}
	
	public function getDefaultAnswer()
	{
		$answer = '';
		$answers = $this->getCurrentQuestion()->getAnswers();
		if(!empty($answers[0]) && ($answers[0] instanceof Belitsoft_Survey_Model_Answer)) {
			$answer = trim(strip_tags(nl2br($answers[0]->getAnswerText())));
		}
				
		return $answer;
	}
	
	public function checkSelected($field)
	{
		return false;
	}
	
	public function setPdf()
	{
		$this->setTemplate('survey/report/pdf/'.$this->_getQuestionType().'.phtml');
		$this->setData('is_pdf', 1);
		
		return $this;
	}
}