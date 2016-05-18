<?php
/**
 * SugarCRM Connection Model
 *
 * @category   Belitsoft
 * @package    Belitsoft_Sugarcrm
 * @author     Belitsoft <bits@belitsoft.com>
 */
class Belitsoft_Survey_Model_Graphic_Pickone extends Belitsoft_Survey_Model_Graphic
{
	function getImage($survey, $question, $start_id = 0)
	{
		$answer_model = Mage::getModel('belitsoft_survey/answer');
		$maxval = $answer_model->getCountStartId(0, $question->getQuestionId());
		$answers = $answer_model->getAllAnswers($question->getSurveyId(), $question->getQuestionId());

		$fields = Mage::getResourceModel('belitsoft_survey/field_collection')
			->addQuestionFilter($question)
			->setOrder('sort_order', 'ASC')
			->getItems();
		
		$sections = array();
		$titles = array();
		$usr_answers = array();				
		$rows = array();
		
		$results = array_count_values($answers);
		foreach($fields as $field) {
			$tmp = new stdClass();
			$tmp->label = trim(strip_tags($field->getFieldText()));
			$tmp->percent = (isset($results[$field->getId()])? intval((100*$results[$field->getId()])/$maxval): 0);
			$tmp->number = (isset($results[$field->getId()])? intval($results[$field->getId()]): 0);
			$rows[] = $tmp;
		}
/*		$tmp = new stdClass();
		$tmp->id = 0;
		$tmp->ftext = 'No answer';
		$rows[] = $tmp;
*/					
		
		$sections[1] = $rows;
		$titles[1] = '';
		
		$maintitle = trim(strip_tags($question->getQuestionText()));
		
		$usr_answers[1][] = trim(strip_tags('PICKONE'));		
		
		return $this->createImage($sections, $titles, $usr_answers, $maintitle, $maxval, 1);
	}
}	
