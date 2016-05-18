<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */
class Belitsoft_Survey_Model_Mysql4_Answer extends Mage_Core_Model_Mysql4_Abstract
{
	/**
	 * Constructor
	 */
	protected function _construct() {

		$this->_init('belitsoft_survey/answer', 'answer_id');
	}
	
	/**
	 * Some processing prior to saving to database - processes the given images
	 * and the store configuration
	 *
	 * @param Mage_Core_Model_Abstract $object Current answer
	 */
	protected function _beforeSave(Mage_Core_Model_Abstract $object) {

		if(!$object->getAnswerId()) {
			$object->setCreationDate(Mage::getSingleton('core/date')->gmtDate());
		}
        
		return $this;
	}
	
	/**
	 * Get max start_id in table
	 * 
	 * @return int
	 */
	public function getMaxStartId($survey_id=0, $question_id=0)
	{
		$select = $this->_getReadAdapter()
			->select()
			->from(
				$this->getMainTable(),
				'MAX(start_id) as max_start_id'
			);
		
		if($survey_id) {
			$select->where('survey_id = ?', $survey_id);
		}
					
		if($question_id) {
			$select->where('question_id = ?', $question_id);
		}
		
		$max = (int) $this->_getReadAdapter()->fetchOne($select);
		
		return $max;
	}
	
	
	/**
	 * Get count start_id in table
	 * 
	 * @return int
	 */
	public function getCountStartId($survey_id=0, $question_id=0)
	{
		$select = $this->_getReadAdapter()
			->select()
			->from(
				$this->getMainTable(),
				'start_id'
			)->group(
				'start_id'
			);
		
		if($survey_id) {
			$select->where('survey_id = ?', $survey_id);
		}
					
		if($question_id) {
			$select->where('question_id = ?', $question_id);
		}
		
		$start_id_arr = $this->_getReadAdapter()->fetchCol($select);
		
		if(is_array($start_id_arr)) {
			$count = count($start_id_arr);
		} else {
			$count = 0;
		}
		
		return $count;
	}
	
	public function getAllAnswers($survey_id=0, $question_id=0, $field_id=0)
	{
		$select = $this->_getReadAdapter()
			->select()
			->from(
				$this->getMainTable(),
				($field_id ? 'answer_field' : 'answer')
			);
		
		if($survey_id) {
			$select->where('survey_id = ?', $survey_id);
		}
					
		if($question_id) {
			$select->where('question_id = ?', $question_id);
		}
					
		if($field_id) {
			$select->where('answer = ?', $field_id);
		}
		
		$answers = $this->_getReadAdapter()->fetchCol($select);
		
		return $answers;
	}
	
	public function getAllAnswersText($survey_id=0, $question_id=0)
	{
		$select = $this->_getReadAdapter()
			->select()
			->from(
				$this->getMainTable(),
				'answer_text'
			);
		
		if($survey_id) {
			$select->where('survey_id = ?', $survey_id);
		}
					
		if($question_id) {
			$select->where('question_id = ?', $question_id);
		}
		
		$answers = $this->_getReadAdapter()->fetchCol($select);
		
		return $answers;
	}
	
	public function deleteAnswers($start_id)
	{
        $this->_getWriteAdapter()->delete(
            $this->getMainTable(),
            $this->_getWriteAdapter()->quoteInto('start_id = ?', $start_id)
        );
	}
	
}