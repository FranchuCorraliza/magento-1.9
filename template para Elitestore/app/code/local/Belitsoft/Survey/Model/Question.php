<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */
class Belitsoft_Survey_Model_Question extends Mage_Core_Model_Abstract
{
	/**
	 * Constructor
	 */
	protected function _construct()
	{
		$this->_init('belitsoft_survey/question');
	}
	
	/**
	 * Get array of question's types
	 */
	public function getQuestionTypes()
	{
		return array(
			'pickone'		=> Mage::helper('belitsoft_survey')->__('PickOne'),
			'pickmany'		=> Mage::helper('belitsoft_survey')->__('PickMany'),
			'shortanswer'	=> Mage::helper('belitsoft_survey')->__('ShortAnswer'),
			'ranking'		=> Mage::helper('belitsoft_survey')->__('Ranking'),
		);
	}
	
	/**
	 * Get array of question's types
	 */
	public function getQuestionTypesForForm()
	{
		$question_types = $this->getQuestionTypes();
		$types = array();
		foreach($question_types as $type_value=>$type_name) {
			$types[] = array(
				'value'	=> $type_value,
				'label'	=> $type_name
			);
		}
		
		return $types;
	}
	
	public function afterLoad()
	{
		$this->setQuestionText(Mage::helper('belitsoft_survey')->getIntroText($this->getQuestionText()));
	}
	
	public function loadFirstQuestionInSurvey($survey)
	{
		$question_id = $this->getResource()->getFirstQuestionInSurvey($survey->getId());
		$question = $this->load($question_id);
		
		return $question;
	}
	
	public function duplicateSurveyQuestions($oldSurveyId, $newSurveyId)
	{
		if(!$oldSurveyId || !$newSurveyId) {
			return false;
		}
		
		$qids = $this->getCollection()
			->addFieldToFilter('survey_id', $oldSurveyId)
			->getAllIds();
		
		foreach($qids as $questionId) {
			if(!$questionId = intval($questionId)) {
				continue;
			}
			
			$newQuestion = $this->load($questionId);
			$newQuestion->setId(null);
			$newQuestion->setSurveyId($newSurveyId);
			$newQuestion->setFields(array());
			$fieldObjects = $newQuestion->getFieldsObjects();
			$newQuestion->setFieldsObjects(array());
			$newQuestion->setRanks(array());
			$rankObjects = $newQuestion->getRanksObjects();
			$newQuestion->setRanksObjects(array());
			$newQuestion->save();
			if(!$newQuestion->getId()) {
				continue;
			}
			
			foreach($fieldObjects as $fieldObject) {
				$newField = Mage::getModel('belitsoft_survey/field')->setData($fieldObject->getData());
				$newField->setId(null);
				$newField->setQuestionId($newQuestion->getId());
				$newField->save();
			}
			
			foreach($rankObjects as $rankObject) {
				$newRank = Mage::getModel('belitsoft_survey/field')->setData($rankObject->getData());
				$newRank->setId(null);
				$newRank->setQuestionId($newQuestion->getId());
				$newRank->save();
			}
		}
		
		return true;
	}
}