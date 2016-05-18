<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */
class Belitsoft_Survey_Model_Mysql4_Survey_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	protected $_isPreview;
	protected $_totalSurveyRecords;
	protected $_addCategories = false;

	/**
	 * Constructor
	 *
	 */
	protected function _construct()
	{
		$this->_init('belitsoft_survey/survey');
	}

	public function addCategoriesToGridCollection()
	{
		$this->_addCategories = true;
		
		return $this;
	}
	
	/**
	 * Creates an options array for grid filter functionality
	 *
	 * @return array Options array
	 */
	public function toOptionHash()
	{
		return $this->_toOptionHash('survey_id', 'survey_name');
	}
	
	/**
	 * Creates an options array for edit functionality
	 *
	 * @return array Options array
	 */
	public function toOptionArray()
	{
		return $this->_toOptionArray('survey_id', 'survey_name');
	}
	
	public function getSize()
	{
		if (is_null($this->_totalSurveyRecords)) {
			$sql = $this->getSelectCountSql();
			$group_part = $sql->getPart('group');
			if(empty($group_part)) {
				$this->_totalSurveyRecords = $this->getConnection()->fetchOne($sql, $this->_bindParams);
			} else {
				$this->_totalSurveyRecords = count($this->getConnection()->fetchAll($sql, $this->_bindParams));
			}
		}

		return intval($this->_totalSurveyRecords);
	}
	
	public function addIsActiveFilter()
	{
		$this->addFilter('is_active', 1);
		return $this;
	}
	
	public function addSurveyIdFilter($survey_id)
	{
		$this->addFilter('main_table.survey_id', $survey_id);
		return $this;
	}
	
	public function addStartDateFilter()
	{
		$this->getSelect()->where('(main_table.start_date IS NULL) OR (main_table.start_date = "") OR (main_table.start_date = "0000-00-00 00:00:00") OR (main_table.start_date <= "'.Mage::app()->getLocale()->date()->toString('y-MM-dd HH:mm:ss').'")');
		
		return $this;
	}
	
	public function addExpiredDateFilter()
	{
		$this->getSelect()->where('(main_table.expired_date IS NULL) OR (main_table.expired_date = "") OR (main_table.expired_date = "0000-00-00 00:00:00") OR (main_table.expired_date >= "'.Mage::app()->getLocale()->date()->toString('y-MM-dd HH:mm:ss').'")');
		
		return $this;
	}
	
	/**
	 * Add Filter by category
	 * 
	 * @param int|Belitsoft_Survey_Model_Category|Belitsoft_Survey_Model_Survey $category Category to be filtered
	 * @return Belitsoft_Survey_Model_Mysql4_Category_Collection
	 */
	public function addCategoryFilter($category)
	{
		if ($category instanceof Belitsoft_Survey_Model_Category) {
			$category = $category->getId();
		} else if ($category instanceof Belitsoft_Survey_Model_Survey) {
			$category = $category->getCategoryId();
		}
		
		$category = (int)$category;
		
		$select = $this->getSelect();

		if(!$this->_addCategories) {
			$select->join(
					array(
						'category_table' => $this->getTable('belitsoft_survey/category_survey')
					),
					'main_table.survey_id = category_table.survey_id',
					array()
				);
		}

		$select->where(
				'category_table.category_id IN (?)',
				array (
					0, 
					$category
				)
			)->group(
				'main_table.survey_id'
			);

		return $this;
	}
	
	/**
	 * Add Filter by store
	 *
	 * @param int|Mage_Core_Model_Store $store Store to be filtered
	 * @return Belitsoft_Survey_Model_Mysql4_Survey_Collection
	 */
	public function addStoreFilter($store)
	{
		if ($store instanceof Mage_Core_Model_Store) {
			$store = $store->getId();
		}
		
		$store = (int)$store;
		
		$this->getSelect()
			->join(
				array(
					'store_table' => $this->getTable('belitsoft_survey/survey_store')
				),
				'main_table.survey_id = store_table.survey_id',
				array()
			)->where(
				'store_table.store_id IN (?)',
				array (
					0, 
					$store
				)
			)->group(
				'main_table.survey_id'
			);
		
		return $this;
	}
	
	/**
	 * Add customer group's filter
	 *
	 * @param array $customerGroupId Customer group ids to be filtered
	 * @return Belitsoft_Survey_Model_Mysql4_Survey_Collection
	 */
	public function addCustomerGroupIdsFilter($customerGroupId)
	{
		$customerGroupId = (array)$customerGroupId;
		
		$this->getSelect()
			->joinLeft(
				array(
					'customer_group_table' => $this->getTable('belitsoft_survey/survey_customer_group')
				),
				'main_table.survey_id = customer_group_table.survey_id',
				array()
			)->where(
				'((customer_group_table.customer_group_id IN (?) AND main_table.only_for_registered = 1) OR main_table.only_for_registered = 0)',
				$customerGroupId
			)->group(
				'main_table.survey_id'
			);
		
		
		return $this;
	}

	public function addCustomerGroupFilter()
	{
		if(!Mage::helper('customer')->isLoggedIn()) {
			$this->addFilter('main_table.only_for_registered', 0);
		} else {
			$group_ids = array();
			$group_ids[] = Mage::helper('customer')->getCustomer()->getGroupId();
			$this->addCustomerGroupIdsFilter($group_ids);
		}

		return $this;
	}
	
	protected function _beforeLoad()
	{
		if($this->_addCategories) {
			$this->getSelect()
				->join(
					array(
						'category_table' => $this->getTable('belitsoft_survey/category_survey')
					),
					'main_table.survey_id = category_table.survey_id',
					array('category_id')
				);
		}
		
		return parent::_beforeLoad();
	}
	
	/**
	 * After load processing - adds store information to the datasets
	 *
	 */
	protected function _afterLoad()
	{
		if($this->_isPreview) {
			$items = $this->getColumnValues('survey_id');
			if(count($items)) {
				$select = $this->getConnection()
					->select()
					->from(
						$this->getTable('belitsoft_survey/survey_store')
					)->where(
						$this->getTable('belitsoft_survey/survey_store') . '.survey_id IN (?)',
						$items
					);

				if($result = $this->getConnection()->fetchPairs($select)) {
					foreach ($this as $item) {
						$survey_id = $item->getData('survey_id');
						if(!isset($result[$survey_id])) {
							continue;
						}
						
						if($result[$survey_id] == 0) {
							$stores = Mage::app()->getStores(false, true);
							$storeId = current($stores)->getId();
							$storeCode = key($stores);
						} else {
							$storeId = $result[$survey_id];
							$storeCode = Mage::app()->getStore($storeId)->getCode();
						}

						$item->setData('_first_store_id', $storeId);
						$item->setData('store_code', $storeCode);
					}
				}
			}
		}
		
		parent::_afterLoad();
	}
	
	public function reset()
	{
		$this->_reset();
		$this->_filters = array();
		$this->_isFiltersRendered = false;
		$this->_totalSurveyRecords = null;
	}
}
