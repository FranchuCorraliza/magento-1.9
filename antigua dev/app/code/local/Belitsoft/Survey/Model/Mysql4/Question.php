<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */
class Belitsoft_Survey_Model_Mysql4_Question extends Mage_Core_Model_Mysql4_Abstract
{
	/**
	 * Constructor
	 */
	protected function _construct() {

		$this->_init('belitsoft_survey/question', 'question_id');
	}

	/**
	 * Retrieve select object for load object data
	 *
	 * @param string $field
	 * @param mixed $value
	 * @return Zend_Db_Select
	 */
	protected function _getLoadSelect($field, $value, $object) {

		$select = parent::_getLoadSelect($field, $value, $object);
		
		if($object->getStoreId()) {
			$select->where(
				'is_active = 1'
			)->order(
				'creation_date DESC'
			)->limit(1);
		}

		return $select;
	}
	
	/**
	 * Some processing prior to saving to database - processes the given images
	 * and the store configuration
	 *
	 * @param Mage_Core_Model_Abstract $object Current survey
	 */
	protected function _beforeSave(Mage_Core_Model_Abstract $object) {

		if(!$object->getQuestionId()) {
			$object->setCreationDate(Mage::getSingleton('core/date')->gmtDate());
		}
		
		$object->setUpdateDate(Mage :: getSingleton('core/date')->gmtDate());
        
		return $this;
	}

	/**
	 * Assign fields to question
	 *
	 * @param Mage_Core_Model_Abstract $object Current survey
	 */
	protected function _afterSave(Mage_Core_Model_Abstract $object)
	{
		// process save question fields
		$question_id = $object->getQuestionId();
		if($question_id) {
			$condition = $this->_getWriteAdapter()->quoteInto('question_id = ?', $question_id);
			$this->_getWriteAdapter()->delete($this->getTable('belitsoft_survey/field'), $condition);
		}

		$fields = (array)$object->getData('fields');
		foreach($fields as $field_data) {
			if(empty($field_data['delete']) && !empty($field_data['field_text'])) {
				unset($field_data['delete']);
				$field_data['question_id'] = $question_id;
				$field_data['is_main'] = 1;
				$field_data['field_text'] = Mage::helper('belitsoft_survey')->cleanText($field_data['field_text']);
				$this->_getWriteAdapter()->insert(
					$this->getTable('belitsoft_survey/field'),
					$field_data
				);
			}
		}
		
		$ranks = (array)$object->getData('ranks');
		foreach($ranks as $field_data) {
			if(empty($field_data['delete']) && !empty($field_data['field_text'])) {
				unset($field_data['delete']);
				$field_data['question_id'] = $question_id;
				$field_data['is_main'] = 0;
				$field_data['field_text'] = Mage::helper('belitsoft_survey')->cleanText($field_data['field_text']);
				$this->_getWriteAdapter()->insert(
					$this->getTable('belitsoft_survey/field'),
					$field_data
				);
			}
		}
		
		return $this;
	}

	/**
	 * Do fields processing after loading
	 * 
	 * @param Mage_Core_Model_Abstract $object Current question
	 */
	protected function _afterLoad(Mage_Core_Model_Abstract $object)
	{
		$fields = $this->_getFieldsOptions($object->getQuestionId(), 1);
		$object->setData('fields', $fields);
		$object->setData('fields_objects', $this->_getFieldsObjects($fields));
		
		$ranks = $this->_getFieldsOptions($object->getQuestionId(), 0);
		$object->setData('ranks', $ranks);
		$object->setData('ranks_objects', $this->_getFieldsObjects($ranks));
		
		return $this;
	}
	
	protected function _getFieldsOptions($question_id, $is_main = 1)
	{
		$select = $this->_getReadAdapter()
			->select()
			->from(
				$this->getTable('belitsoft_survey/field'),
				array(
					'id' => 'field_id',
					'*'
				)
			)->where(
				'question_id = ?', $question_id
			)->where(
				'is_main = ?', $is_main
			)->order(
				array('sort_order', 'field_id')
			);
		

		$data = $this->_getReadAdapter()->fetchAll($select);

		return $data;
	}
	
	protected function _getFieldsObjects($objects)
	{
		foreach($objects as $key=>$field) {
			$objects[$key] = new Varien_Object($field);
		}
		
		return $objects;
	}
	
	public function getFirstQuestionInSurvey($survey_id)
	{
		$select = $this->_getReadAdapter()
			->select()
			->from(
				$this->getMainTable(),
				'question_id'
			)->where(
				'survey_id = ?', $survey_id
			)->where(
				'is_active = ?', 1
			)->order(
				array('sort_order', 'creation_date')
			);
			
		$question_id = (int) $this->_getReadAdapter()->fetchOne($select);

		return $question_id;
	}
}