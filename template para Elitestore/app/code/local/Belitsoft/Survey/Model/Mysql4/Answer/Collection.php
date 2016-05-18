<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package	Belitsoft_Survey
 * @author	 Belitsoft <bits@belitsoft.com>
 */
class Belitsoft_Survey_Model_Mysql4_Answer_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	protected $_grid_view;
	protected $_customer_grid_view;
	protected $_totalAnswersRecords;

	/**
	 * Constructor
	 */
	protected function _construct()
	{
		$this->_init('belitsoft_survey/answer');
		$this->_grid_view = false;
	}
	
	/**
	 * 
	 * @param boolean $grid_view
	 * @return Belitsoft_Survey_Model_Mysql4_Answer_Collection
	 */
	public function setGridView($grid_view = false)
	{
		$this->_grid_view = $grid_view;
		
		return $this;
	}
	
	/**
	 * 
	 * @param boolean $customer_grid_view
	 * @return Belitsoft_Survey_Model_Mysql4_Answer_Collection
	 */
	public function setCustomerGridView($customer_grid_view = false)
	{
		$this->_customer_grid_view = $customer_grid_view;
		
		return $this;
	}
	
	protected function _beforeLoad()
	{
		if($this->_grid_view) { 		
			$this->showCustomerInfo();
			$this->addExpressionFieldToSelect('customer_name', 'CONCAT_WS(" ", {{firstname}}, {{lastname}})', array('firstname'=>'customer_firstname', 'lastname'=>'customer_lastname'));
			
			$this->addFieldToSelect('start_id'); 
			$this->addFieldToSelect('creation_date');
			$this->addFieldToSelect('survey_id');
			$this->addFieldToSelect('customer_id');
			
			$this->getSelect()->group('main_table.start_id');
//			print_r($this->getSelect()->assemble()); die;
			
		} else if($this->_customer_grid_view) {
			$this->addFieldToSelect('start_id'); 
			$this->addFieldToSelect('creation_date');
			$this->addFieldToSelect('survey_id');
			
			$this->getSelect()
				->join(
					array(
						'survey_table' => $this->getTable('belitsoft_survey/survey')
					),
					'main_table.survey_id = survey_table.survey_id',
					array('survey_name')
				)
				->group('main_table.start_id');
		}
		
		return parent::_beforeLoad();
	}
	
	public function getSize()
	{
		$sql = $this->getSelectCountSql();
		$this->_totalAnswersRecords = count($this->getConnection()->fetchAll($sql, $this->_bindParams));

		return intval($this->_totalAnswersRecords);
	}

	public function setDateOrder($dir='DESC')
	{
		$this->setOrder('main_table.creation_date', $dir);
		return $this;
	}
	
	public function joinQuestionTable($fields = array())
	{
		$this->getSelect()
			->join(
				array(
					'question_table' => $this->getTable('belitsoft_survey/question')
				),
				'main_table.question_id = question_table.question_id',
				$fields
			);
		return $this;
	}

	/**
	 * Adds customer info to select
	 *
	 * @return  Mage_Newsletter_Model_Mysql4_Subscriber_Collection
	 */
	public function showCustomerInfo()
	{
		$customer = Mage::getModel('customer/customer');
		/* @var $customer Mage_Customer_Model_Customer */
		$firstname  = $customer->getAttribute('firstname');
		$lastname   = $customer->getAttribute('lastname');

		$this->getSelect()
			->joinLeft(
				array('customer_lastname_table'=>$lastname->getBackend()->getTable()),
				'customer_lastname_table.entity_id = main_table.customer_id
				 AND customer_lastname_table.attribute_id = '.(int) $lastname->getAttributeId(),
				array('customer_lastname'=>'value')
			)->joinLeft(
				array('customer_firstname_table'=>$firstname->getBackend()->getTable()),
				'customer_firstname_table.entity_id = main_table.customer_id
				 AND customer_firstname_table.attribute_id = '.(int) $firstname->getAttributeId(),
				array('customer_firstname'=>'value')
			);
		
		return $this;
	}
	
	/**
	 * Add Filter by survey
	 * 
	 * @param int|Belitsoft_Survey_Model_Answer|Belitsoft_Survey_Model_Survey $survey Survey to be filtered
	 * @return Belitsoft_Survey_Model_Mysql4_Category_Collection
	 */
	public function addSurveyFilter($survey)
	{
		if ($survey instanceof Belitsoft_Survey_Model_Answer) {
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
			);
		
		return $this;
	}


	/**
	 * Add Filter by start id
	 * 
	 * @param int $start_id Start id to be filtered
	 * @return Belitsoft_Survey_Model_Mysql4_Answer_Collection
	 */
	public function addStartIdFilter($start_id)
	{
		$this->addFilter('start_id', $start_id);

		return $this;
	}

	/**
	 * Add Filter by start id
	 * 
	 * @param int $start_id Start id to be filtered
	 * @return Belitsoft_Survey_Model_Mysql4_Answer_Collection
	 */
	public function addCustomerFilter($customerId=null)
	{
		if(!$customerId) {
			$customerId = Mage::getSingleton('customer/session')->getCustomerId();
		} 
		$this->addFilter(
			'customer_id',
			$this->getConnection()->quoteInto('main_table.customer_id=?', $customerId),
			'string'
		);

		return $this;
	}
	
	
	/**
	 * Add Filter by store
	 *
	 * @param int|Mage_Core_Model_Store $store Store to be filtered
	 * @return Belitsoft_Survey_Model_Mysql4_Answer_Collection
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
}