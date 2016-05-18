<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */
class Belitsoft_Survey_Model_Mysql4_Field_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	/**
	 * Constructor
	 */
	protected function _construct()
	{
		$this->_init('belitsoft_survey/field');
	}

	public function addQuestionFilter($question)
	{
		if ($question instanceof Belitsoft_Survey_Model_Question) {
			$question = $question->getQuestionId();
		}
				
		$this->addFilter('question_id', intval($question));

		return $this;
	}
	
	public function addFieldRankFilter($is_main = 1)
	{
		$this->addFilter('is_main', intval($is_main));

		return $this;
	}
	
}