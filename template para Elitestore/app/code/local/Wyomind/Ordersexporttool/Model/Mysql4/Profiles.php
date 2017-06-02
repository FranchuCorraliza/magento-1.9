<?php

class Wyomind_Ordersexporttool_Model_Mysql4_Profiles extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
	{
		// Note that the ordersexporttool_id refers to the key field in your database table.
		$this->_init('ordersexporttool/profiles', 'file_id');
	}
}