<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */

class Belitsoft_Survey_Block_Frontend_Question_Abstract extends Mage_Core_Block_Template
{
	/**
	 * Function to gather the current survey
	 *
	 * @return Belitsoft_Survey_Model_Survey The current survey
	 */
	public function getSurvey()
	{
		$survey = $this->getData('survey');
		if (is_null($survey)) {
			$survey = Mage::registry('survey_current_survey');
			$this->setData('survey', $survey);
		}

		return $survey;
	}
	
	/**
	 * Function to gather the current question
	 *
	 * @return Belitsoft_Survey_Model_Question The current question
	 */
	public function getCurrentQuestion()
	{
		return Mage::registry('survey_current_question');
	}
	
	/**
	 * Function to get if exists data that the user entered previously
	 *
	 * @return array POST data
	 */
	public function getUserData()
	{
		return (array) $this->getCurrentQuestion()->getUserData();
	}
	
	/**
	 * Function to get if exists data that the user entered previously
	 *
	 * @param string $el_name Element name
	 * @return string|array|boolean
	 */
	public function getSelected($el_name, $value=null)
	{
		$data = $this->getUserData();
		
		if(array_key_exists($el_name, $data)) {
			return $data[$el_name];
		}

		return '';
	}

	public function isEditMode()
	{
		return Mage::helper('belitsoft_survey')->isEditMode();
	}
	
	public function isViewMode()
	{
		return Mage::helper('belitsoft_survey')->isViewMode();
	}
		
	/**
	 * Function to gather the fields of question
	 *
	 * @return array
	 */
	public function getFields()
	{
		return $this->getCurrentQuestion()->getFields();
	}
		
	/**
	 * Function to gather the fields objects
	 *
	 * @return array
	 */
	public function getFieldsObjects()
	{
		return $this->getCurrentQuestion()->getFieldsObjects();
	}
}