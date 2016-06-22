<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package	Belitsoft_Survey
 * @author	 Belitsoft <bits@belitsoft.com>
 */
class Belitsoft_Survey_Model_Mysql4_Survey extends Mage_Core_Model_Mysql4_Abstract
{
	/**
	 * Constructor
	 */
	protected function _construct()
	{
		$this->_init('belitsoft_survey/survey', 'survey_id');
	}
	
	/**
	 * Some processing prior to saving to database - processes the given images
	 * and the store configuration
	 *
	 * @param Mage_Core_Model_Abstract $object Current survey
	 */
	protected function _beforeSave(Mage_Core_Model_Abstract $object)
	{
		$id = $object->getSurveyId();
		
		if(!$id) {
			$object->setCreationDate(Mage::getSingleton('core/date')->gmtDate());
		}
		$object->setUpdateDate(Mage::getSingleton('core/date')->gmtDate());
		
		$startDate = trim($object->getStartDate());
		$object->setStartDate($startDate ? $startDate : '0000-00-00 00:00:00');
		
		$expiredDate = trim($object->getExpiredDate());
		$object->setExpiredDate($expiredDate ? $expiredDate : '0000-00-00 00:00:00');

		$object->setSurveyName(Mage::helper('belitsoft_survey')->cleanText($object->getSurveyName()));

		$urlKey = $object->getSurveyUrlKey();
		if($urlKey && ($newUrlKey = preg_replace('/[^a-z0-9\-]/i', '-', $urlKey))) {
			if($newUrlKey != $urlKey) {
				$object->setSurveyUrlKey($newUrlKey);
				Mage::getSingleton('adminhtml/session')->addWarning(
					Mage::helper('belitsoft_survey')->__('The URL key was changed')
				);
			}

			$select = $this->_getReadAdapter()
				->select()
				->from(
					$this->getMainTable(),
					array('survey_url_key')
				)->where(
					'survey_url_key = ?', $newUrlKey
				);

			if($id) {
				$select->where(
					'survey_id != ?', $id
				);
	   		}

	   		if($this->_getReadAdapter()->fetchOne($select)) {
				$select = $this->_getReadAdapter()
					->select()
					->from(
						$this->getMainTable(),
						array('survey_url_key')
					)->where(
						'survey_url_key LIKE ?', $newUrlKey.'%'
					);

				if($id) {
					$select->where(
						'survey_id != ?', $id
					);
		   		}

				if($data = $this->_getReadAdapter()->fetchCol($select)) {
					$countArray = array();
					foreach($data as $row) {
						$countArray[] = (int) preg_replace('/^\-/', '', substr($row, strlen($newUrlKey)));
					}

					$newUrlKey .= '-'.(max($countArray)+1);
					$object->setSurveyUrlKey($newUrlKey);
					Mage::getSingleton('adminhtml/session')->addWarning(
						Mage::helper('belitsoft_survey')->__('This URL key already exists, so it was stored in the form like: %s', $newUrlKey)
					);

				}
	   	   }

		} else {
			$object->setSurveyUrlKey('');
		}


		return $this;
	}
	
	/**
	 * Assign page to store views
	 *
	 * @param Mage_Core_Model_Abstract $object Current survey
	 */
	protected function _afterSave(Mage_Core_Model_Abstract $object)
	{
		$condition = $this->_getWriteAdapter()->quoteInto('survey_id = ?', $object->getSurveyId());
		
		// process survey to store relation
		$this->_getWriteAdapter()->delete($this->getTable('belitsoft_survey/survey_store'), $condition);
		
		foreach((array) $object->getData('stores') as $store) {
			$this->_getWriteAdapter()->insert(
				$this->getTable('belitsoft_survey/survey_store'),
				array(
					'survey_id'	=> $object->getSurveyId(),
					'store_id'	=> $store
				)
			);
		}
		
		// process survey to categories relation
		$this->_getWriteAdapter()->delete($this->getTable('belitsoft_survey/category_survey'), $condition);
		
		foreach((array) $object->getData('categories') as $categoryId) {
			$this->_getWriteAdapter()->insert(
				$this->getTable('belitsoft_survey/category_survey'),
				array(
					'survey_id'		=> $object->getSurveyId(),
					'category_id'	=> $categoryId
				)
			);
		}
		
		// process category to customer group relation
		$this->_getWriteAdapter()->delete($this->getTable('belitsoft_survey/survey_customer_group'), $condition);
		
		if($object->getOnlyForRegistered()) {
			$customer_group_ids = (array) $object->getData('customer_group_ids');
			if(!count($customer_group_ids)) {
				$customer_group_ids = array_keys(Mage::helper('customer')->getGroups()->toOptionHash());
			}		
			foreach($customer_group_ids as $customer_group_ids) {
				$this->_getWriteAdapter()->insert(
					$this->getTable('belitsoft_survey/survey_customer_group'),
					array(
						'survey_id'			=> $object->getSurveyId(),
						'customer_group_id'	=> $customer_group_ids
					)
				);
			}
		}

		return $this;
	}

	/**
	 * Do store and category processing after loading
	 * 
	 * @param Mage_Core_Model_Abstract $object Current survey
	 */
	protected function _afterLoad(Mage_Core_Model_Abstract $object)
	{
		// process survey to stores relation
		$select = $this->_getReadAdapter()
			->select()
			->from(
				$this->getTable('belitsoft_survey/survey_store')
			)->where(
				'survey_id = ?', $object->getId()
			);
		
		if ($data = $this->_getReadAdapter()->fetchAll($select)) {
			$storesArray = array();
			foreach($data as $row) {
				$storesArray[] = $row['store_id'];
			}
			$object->setData('store_id', $storesArray);
		}
		
		// process survey to categories relation
		$select = $this->_getReadAdapter()
			->select()
			->from(
				$this->getTable('belitsoft_survey/category_survey')
			)->where(
				'survey_id = ?',
				$object->getId()
			);
		
		if($data = $this->_getReadAdapter()->fetchAll($select)) {
			$categoryArray = array();
			foreach($data as $row) {
				$categoryArray[] = $row['category_id'];
			}
			$object->setData('category_id', $categoryArray);
		}

		// process survey to customer groups relation
		$select = $this->_getReadAdapter()
			->select()
			->from(
				$this->getTable('belitsoft_survey/survey_customer_group')
			)->where(
				'survey_id = ?',
				$object->getId()
			);
		
		if($data = $this->_getReadAdapter()->fetchAll($select)) {
			$customer_group_ids = array();
			foreach ($data as $row) {
				$customer_group_ids[] = $row['customer_group_id'];
			}

			$object->setData('customer_group_ids', $customer_group_ids);
		}
		
		$startDate = $object->getStartDate();
		$object->setStartDate($startDate == '0000-00-00 00:00:00' ? '' : $startDate);
		
		$expiredDate = $object->getExpiredDate();
		$object->setExpiredDate($expiredDate == '0000-00-00 00:00:00' ? '' : $expiredDate);
		
		return $this;
	}

	/**
	 * Retrieves survey title from DB by passed id.
	 *
	 * @param string $id
	 * @return string|false
	 */
	public function getSurveyTitleById($id)
	{
		$select = $this->_getReadAdapter()->select();
		/* @var $select Zend_Db_Select */
		$select->from(array('main_table' => $this->getMainTable()), 'survey_name')
			->where('main_table.survey_id = ?', $id);

		return $this->_getReadAdapter()->fetchOne($select);
	}

	public function getSurveyByUrlKey($urlKey)
	{
		$select = $this->_getReadAdapter()
		    ->select()
		    ->from(
		        $this->getMainTable(),
		        array('survey_id')
			)->where(
				'survey_url_key = ?', $urlKey
			);

		return $this->_getReadAdapter()->fetchOne($select);
	}
}