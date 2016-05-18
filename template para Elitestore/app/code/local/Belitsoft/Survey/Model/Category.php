<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */
class Belitsoft_Survey_Model_Category extends Mage_Core_Model_Abstract
{
	/**
	 * Constructor
	 */
	protected function _construct()
	{
		parent::_construct();

		$this->_init('belitsoft_survey/category');
	}
	
	public function getName()
	{
		return $this->getCategoryName();
	}
}
