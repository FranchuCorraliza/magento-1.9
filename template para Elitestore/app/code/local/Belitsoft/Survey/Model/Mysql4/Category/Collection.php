<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */
class Belitsoft_Survey_Model_Mysql4_Category_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	protected $_isPreview;
	
	/**
	 * Constructor
	 */
	protected function _construct()
	{
		$this->_init('belitsoft_survey/category');
	}
	
	/**
	 * After load processing - adds store information to the datasets
	 */
	protected function _afterLoad()
	{
		if ($this->_isPreview) {
			$categories = $this->getColumnValues('category_id');
			if (count($categories)) {
				$select = $this->getConnection()
					->select()
					->from(
						$this->getTable('belitsoft_survey/category_store')
					)->where(
						$this->getTable('belitsoft_survey/category_store') . '.category_id IN (?)',
						$categories
					);

				if($result = $this->getConnection()->fetchPairs($select)) {
					foreach($this as $item) {
						$category_id = $item->getData('category_id');
						if(!isset($result[$category_id])) {
							continue;
						}
						if($result[$category_id] == 0) {
							$stores = Mage::app()->getStores(false, true);
							$storeId = current($stores)->getId();
							$storeCode = key($stores);
						} else {
							$storeId = $result[$category_id];
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
		
	/**
	 * Creates an options array for grid filter functionality
	 *
	 * @return array Options array
	 */
	public function toOptionHash()
	{
		return $this->_toOptionHash('category_id', 'category_name');
	}
	
	/**
	 * Creates an options array for edit functionality
	 *
	 * @return array Options array
	 */
	public function toOptionArray()
	{
		return $this->_toOptionArray('category_id', 'category_name');
	}
	
	/**
	 * Add store's Filter
	 *
	 * @param int|Mage_Core_Model_Store $store Store to be filtered
	 * @return Belitsoft_Survey_Model_Mysql4_Category_Collection
	 */
	public function addStoreFilter($store)
	{
		if($store instanceof Mage_Core_Model_Store) {
			$store = $store->getId();
		}
		
		$store = (int)$store;
		
		$this->getSelect()
			->join(
				array(
					'store_table' => $this->getTable('belitsoft_survey/category_store')
				),
				'main_table.category_id = store_table.category_id',
				array()
			)->where(
				'store_table.store_id IN (?)',
				array (
					0,
					$store
				)
			)->group(
				'main_table.category_id'
			);
		
		return $this;
	}

	public function addIsActiveFilter()
	{
		$this->addFilter('is_active', 1);
		return $this;
	}
	
	/**
	 * Add customer group's filter
	 *
	 * @param array $customerGroupId Customer group ids to be filtered
	 * @return Belitsoft_Survey_Model_Mysql4_Category_Collection
	 */
	public function addCustomerGroupIdsFilter($customerGroupId)
	{
		$customerGroupId = (array)$customerGroupId;
		
		$this->getSelect()
			->joinLeft(
				array(
					'customer_group_table' => $this->getTable('belitsoft_survey/category_customer_group')
				),
				'main_table.category_id = customer_group_table.category_id',
				array()
			)->where(
				'((customer_group_table.customer_group_id IN (?) AND main_table.only_for_registered = 1) OR main_table.only_for_registered = 0)',
				$customerGroupId
			)->group(
				'main_table.category_id'
			);
		
		
		return $this;
	}

	public function addCustomerGroupFilter()
	{
		if(!Mage::helper('customer')->isLoggedIn()) {
			$this->addFilter('only_for_registered', 0);
		} else {
			$group_ids = array();
			$group_ids[] = Mage::helper('customer')->getCustomer()->getGroupId();
			$this->addCustomerGroupIdsFilter($group_ids);
		}

		return $this;
	}
}
