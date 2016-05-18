<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package	Belitsoft_Survey
 * @author	 Belitsoft <bits@belitsoft.com>
 */
class Belitsoft_Survey_Model_Mysql4_Category extends Mage_Core_Model_Mysql4_Abstract
{
	/**
	 * Constructor
	 */
	protected function _construct()
	{
		$this->_init('belitsoft_survey/category', 'category_id');
	}

	/**
	 * Sets the creation and update timestamps
	 *
	 * @param Mage_Core_Model_Abstract $object Current category
	 * @return Belitsoft_Survey_Model_Mysql4_Category
	 */
	protected function _beforeSave(Mage_Core_Model_Abstract $object)
	{
		$id = $object->getCategoryId();

		if(!$id) {
			$object->setCreationDate(Mage::getSingleton('core/date')->gmtDate());
		}
		$object->setUpdateDate(Mage::getSingleton('core/date')->gmtDate());

		$object->setCategoryName(Mage::helper('belitsoft_survey')->cleanText($object->getCategoryName()));

		$urlKey = $object->getCategoryUrlKey();
		if($urlKey && ($newUrlKey = preg_replace('/[^a-z0-9\-]/i', '-', $urlKey))) {
			if($newUrlKey != $urlKey) {
				$object->setCategoryUrlKey($newUrlKey);
				Mage::getSingleton('adminhtml/session')->addWarning(
					Mage::helper('belitsoft_survey')->__('The URL key was changed')
				);
			}

			$select = $this->_getReadAdapter()
				->select()
				->from(
					$this->getMainTable(),
					array('category_url_key')
				)->where(
					'category_url_key = ?', $newUrlKey
				);

			if($id) {
				$select->where(
					'category_id != ?', $id
				);
	   		}

	   		if($this->_getReadAdapter()->fetchOne($select)) {
				$select = $this->_getReadAdapter()
					->select()
					->from(
						$this->getMainTable(),
						array('category_url_key')
					)->where(
						'category_url_key LIKE ?', $newUrlKey.'%'
					);

				if($id) {
					$select->where(
						'category_id != ?', $id
					);
		   		}

				if($data = $this->_getReadAdapter()->fetchCol($select)) {
					$countArray = array();
					foreach($data as $row) {
						$countArray[] = (int) preg_replace('/^\-/', '', substr($row, strlen($newUrlKey)));
					}

					$newUrlKey .= '-'.(max($countArray)+1);
					$object->setCategoryUrlKey($newUrlKey);
					Mage::getSingleton('adminhtml/session')->addWarning(
						Mage::helper('belitsoft_survey')->__('This URL key already exists, so it was stored in the form like: %s', $newUrlKey)
					);
				}
	   	   }

		} else {
			$object->setCategoryUrlKey('');
		}
		
		return $this;
	}

	/**
	 * Do store processing after category save
	 *
	 * @param Mage_Core_Model_Abstract $object Current category
	 * @return Belitsoft_Survey_Model_Mysql4_Category
	 */
	protected function _afterSave(Mage_Core_Model_Abstract $object)
	{
		$id = $object->getCategoryId();
		
		$condition = $this->_getWriteAdapter()->quoteInto('category_id = ?', $id);
		
		// process category to stores relation
		$this->_getWriteAdapter()->delete($this->getTable('belitsoft_survey/category_store'), $condition);
		
		foreach((array) $object->getData('stores') as $store) {
			$this->_getWriteAdapter()
				->insert(
					$this->getTable('belitsoft_survey/category_store'),
					array(
						'category_id'	=> $id,
						'store_id'		=> $store
					)
				);
		}

		// process category to customer groups relation
		$this->_getWriteAdapter()->delete($this->getTable('belitsoft_survey/category_customer_group'), $condition);

		if($object->getOnlyForRegistered()) {
			$customer_group_ids = (array) $object->getData('customer_group_ids');
			if(!count($customer_group_ids)) {
				$customer_group_ids = array_keys(Mage::helper('customer')->getGroups()->toOptionHash());
			}
			foreach($customer_group_ids as $customer_group_id) {
				$this->_getWriteAdapter()
					->insert(
						$this->getTable('belitsoft_survey/category_customer_group'),
						array(
							'category_id'		=> $id,
							'customer_group_id'	=> $customer_group_id
						)
					);
			}
		}

		
		return $this;
	}

	/**
	 * Do store processing after loading
	 * 
	 * @param Mage_Core_Model_Abstract $object Current category
	 * @return Belitsoft_Survey_Model_Mysql4_Category
	 */
	protected function _afterLoad(Mage_Core_Model_Abstract $object)
	{
		// process category to stores relation
		$select = $this->_getReadAdapter()
			->select()
			->from(
				$this->getTable('belitsoft_survey/category_store')
			)->where(
				'category_id = ?',
				$object->getCategoryId()
			);
		
		if ($data = $this->_getReadAdapter()->fetchAll($select)) {
			$stores = array();
			foreach ($data as $row) {
				$stores[] = $row['store_id'];
			}

			$object->setData('store_id', $stores);
		}
		
		// process category to customer groups relation
		$select = $this->_getReadAdapter()
			->select()
			->from(
				$this->getTable('belitsoft_survey/category_customer_group')
			)->where(
				'category_id = ?',
				$object->getCategoryId()
			);
		
		if ($data = $this->_getReadAdapter()->fetchAll($select)) {
			$customer_group_ids = array();
			foreach ($data as $row) {
				$customer_group_ids[] = $row['customer_group_id'];
			}

			$object->setData('customer_group_ids', $customer_group_ids);
		}
		
		return $this;
	}

	/**
	 * Retrieves category title from DB by passed id.
	 *
	 * @param string $id
	 * @return string|false
	 */
	public function getSurveyCategoryTitleById($id)
	{
		$select = $this->_getReadAdapter()->select();
		/* @var $select Zend_Db_Select */
		$select->from(array('main_table' => $this->getMainTable()), 'category_name')
			->where('main_table.category_id = ?', $id);

		return $this->_getReadAdapter()->fetchOne($select);
	}

	public function getSurveyCategoryByUrlKey($urlKey)
	{
		$select = $this->_getReadAdapter()
		    ->select()
		    ->from(
                $this->getMainTable(),
  		        array('category_id')
			)->where(
				'category_url_key = ?', $urlKey
			);

		return $this->_getReadAdapter()->fetchOne($select);
	}
}
