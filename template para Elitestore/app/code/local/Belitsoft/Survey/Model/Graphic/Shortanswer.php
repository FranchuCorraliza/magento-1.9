<?php
/**
 * SugarCRM Connection Model
 *
 * @category   Belitsoft
 * @package    Belitsoft_Sugarcrm
 * @author     Belitsoft <bits@belitsoft.com>
 */
class Belitsoft_Survey_Model_Graphic_Shortanswer extends Belitsoft_Survey_Model_Graphic
{
	function getText($survey, $question)
	{
		$answers = Mage::getResourceModel('belitsoft_survey/answer')
			->getAllAnswersText($question->getSurveyId(), $question->getQuestionId());
			
		return $answers;
	}
}	
