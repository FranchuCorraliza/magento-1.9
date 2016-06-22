<?php

class Wyomind_Ordersexporttool_Model_Mysql4_Profiles_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	public function _construct()
	{
		parent::_construct();
		$this->_init('ordersexporttool/profiles');
	}
}