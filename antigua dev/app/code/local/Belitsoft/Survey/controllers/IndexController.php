<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */
class Belitsoft_Survey_IndexController extends Mage_Core_Controller_Front_Action
{
	/**
	 * Initialize survey categories collection
	 *
	 * @return Belitsoft_Survey_Model_Mysql4_Category_Collection
	 */
	protected function _initCategoryCollection()
	{
		$categories =  Mage::getResourceSingleton('belitsoft_survey/category_collection')
			->addStoreFilter(Mage::app()->getStore())
			->addIsActiveFilter()
			->addCustomerGroupFilter();

		Mage::register('survey_category_collection', $categories);

		return $categories;
	}

	/**
	 * Displays the Survey categories list.
	 */
	public function indexAction()
	{
		$this->_initCategoryCollection();
		
		$this->loadLayout()->renderLayout();
	}
}
