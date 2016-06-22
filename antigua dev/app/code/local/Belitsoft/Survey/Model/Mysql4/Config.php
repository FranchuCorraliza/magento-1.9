<?php
/**
 * SugarCRM Config Resourse Model
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Mageplace
 */
class Belitsoft_Survey_Model_Mysql4_Config extends Mage_Core_Model_Mysql4_Abstract
{
	protected function _construct()
	{
		$this->_init('belitsoft_survey/config', 'name');
	}
}
