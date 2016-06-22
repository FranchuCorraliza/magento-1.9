<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */
class Belitsoft_Survey_CategoryController extends Mage_Core_Controller_Front_Action
{
	protected $_category;

	/**
	 * Initialize requested category object
	 *
	 * @return Belitsoft_Survey_Model_Category
	 */
	protected function _initCategory()
	{
		$category_id = (int) $this->getRequest()->getParam('cid', false);
		if (!$category_id) {
			return false;
		}

		$this->_category = Mage::getModel('belitsoft_survey/category')->load($category_id);

		if (!Mage::helper('belitsoft_survey')->canShowCategory($this->_category)) {
			return false;
		}

		Mage::register('survey_current_category', $this->_category);

		return $this->_category;
	}

	/**
	 * Initialize requested category's surveys object
	 *
	 * @return Belitsoft_Survey_Model_Survey
	 */
	protected function _initSurveys()
	{
		$surveyCollection = Mage::getResourceSingleton('belitsoft_survey/survey_collection')
			->addCategoryFilter($this->_category->getCategoryId())
			->addIsActiveFilter()
			->addStartDateFilter()
			->addExpiredDateFilter()
			->addStoreFilter(Mage::app()->getStore())
			->addCustomerGroupFilter();

		Mage::register('survey_collection', $surveyCollection);

		return $surveyCollection;
	}

	/**
	 * Displays the current Survey's category detail view
	 */
	public function viewAction()
	{
		if ($this->_initCategory()) {
			$this->_initSurveys();
			
			$this->loadLayout()->renderLayout();
		} else {
            $this->_forward('noRoute');
		}
	}
}
