<?php
/**
 * SugarCRM Connection Model
 *
 * @category   Belitsoft
 * @package    Belitsoft_Sugarcrm
 * @author     Belitsoft <bits@belitsoft.com>
 */
class Belitsoft_Survey_Model_Graphic_Ranking extends Belitsoft_Survey_Model_Graphic
{
	function getImage($survey, $question, $start_id = 0)
	{
		$answer_model = Mage::getModel('belitsoft_survey/answer');
		$maxval = $answer_model->getCountStartId(0, $question->getQuestionId());
		
		$questions = Mage::getResourceModel('belitsoft_survey/field_collection')
			->addQuestionFilter($question)
			->addFieldRankFilter(1)
			->setOrder('sort_order', 'ASC')
			->getItems();
		
		$fields = Mage::getResourceModel('belitsoft_survey/field_collection')
			->addQuestionFilter($question)
			->addFieldRankFilter(0)
			->setOrder('sort_order', 'ASC')
			->getItems();
/*		$tmp = new stdClass();
		$tmp->id = 0;
		$tmp->stext = ($qtype !=9?'No answer':'Not ranked');
		$fields[] = $tmp;
*/			
		$tmp = null;
		$sections = array();
		$titles = array();
		$usr_answers = array();
		$i = 0;
		
		foreach($questions as $main_field) {
			$tmp = null;
			$rows = array();
			$i++;		
			
			$answer_field = $answer_model->getAllAnswers($question->getSurveyId(), $question->getQuestionId(), $main_field->getFieldId());
			$j = count($answer_field);
			for($ii = $j; $ii < $maxval; $ii++) {
				$answer_field[] = 0;
			}
			
			$results = array_count_values($answer_field);				
			foreach($fields as $field) {
				$tmp = new stdClass();
				$tmp->label = trim(strip_tags($field->getFieldText()));
				$tmp->percent = (isset($results[$field->getId()])? intval((100*$results[$field->getId()])/$maxval): 0);
				$tmp->number = (isset($results[$field->getId()])? intval($results[$field->getId()]): 0);
				$rows[] = $tmp;
			}		
			
			$sections[$i] = $rows;
			$titles[$i] = trim(strip_tags($main_field->getFieldText()));
			
			$usr_answers[$i][] = trim(strip_tags('RANKING'));	
		}

		$maintitle = trim(strip_tags($question->getQuestionText()));

		return $this->createImage($sections, $titles, $usr_answers, $maintitle, $maxval, $i);
	}
}	
