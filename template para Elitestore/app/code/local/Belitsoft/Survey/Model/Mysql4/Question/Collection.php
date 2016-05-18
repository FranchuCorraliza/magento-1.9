<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */
class Belitsoft_Survey_Model_Mysql4_Question_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	protected $_isPreview;
	protected $_totalQuestionsRecords;
	
	/**
	 * Constructor
	 *
	 */
	protected function _construct()
	{
		$this->_init('belitsoft_survey/question');
	}
	
	public function getSize()
	{
		if (is_null($this->_totalQuestionsRecords)) {
			$sql = $this->getSelectCountSql();
			$group_part = $sql->getPart('group');
			if(empty($group_part)) {
				$this->_totalQuestionsRecords = $this->getConnection()->fetchOne($sql, $this->_bindParams);
			} else {
				$this->_totalQuestionsRecords = count($this->getConnection()->fetchAll($sql, $this->_bindParams));
			}
		}
		
		return intval($this->_totalQuestionsRecords);
	}
	
	public function addIsActiveFilter()
	{
		$this->addFilter('is_active', 1);

		return $this;
	}

	public function addSurveyIdFilter($survey)
	{
		if ($survey instanceof Belitsoft_Survey_Model_Survey) {
			$survey = $survey->getSurveyId();
		}
		
		$this->addFilter('survey_id', intval($survey));

		return $this;
	}
	
	/**
	 * Add Filter by survey
	 * 
	 * @param int|Belitsoft_Survey_Model_Question|Belitsoft_Survey_Model_Survey $survey Survey to be filtered
	 * @return Belitsoft_Survey_Model_Mysql4_Category_Collection
	 */
	public function addSurveyFilter($survey)
	{
		if ($survey instanceof Belitsoft_Survey_Model_Question) {
			$survey = $survey->getSurveyId();
		} else if ($survey instanceof Belitsoft_Survey_Model_Survey) {
			$survey = $survey->getId();
		}
		
		$survey = (int)$survey;
		
		$this->getSelect()
			->join(
				array(
					'survey_table' => $this->getTable('belitsoft_survey/survey')
				),
				'main_table.survey_id = survey_table.survey_id',
				array()
			)->where(
				'survey_table.survey_id IN (?)',
				array (
					0, 
					$survey
				)
			)->group(
				'main_table.question_id'
			);
		
		return $this;
	}
		
	/**
	 * Add Filter by question type
	 * 
	 * @param int Belitsoft_Survey_Model_Category $category Category to be filtered
	 * @return Belitsoft_Survey_Model_Mysql4_Category_Collection
	 */
	public function addQuestionTypeFilter($question_type)
	{
		if ($question_type instanceof Belitsoft_Survey_Model_Question) {
			$question_type = $question_type->getQuestionType();
		}
		
		$this->getSelect()
			->where(
				'main_table.question_type LIKE (?)',
				$question_type
			)->group(
				'main_table.question_id'
			);
		
		return $this;
	}
	
	/**
	 * After load processing - adds store information to the datasets
	 *
	 */
	protected function _afterLoad()
	{
		foreach($this->_items as $key=>$item) {
			$question = Mage::getModel('belitsoft_survey/question')->load($key);
			$this->_items[$key] = $question;
		}
		
		parent::_afterLoad();
	}
	
	public function reset()
	{
		$this->_reset();
        $this->_filters = array();
        $this->_isFiltersRendered = false;
		$this->_totalQuestionsRecords = null;
	}
}