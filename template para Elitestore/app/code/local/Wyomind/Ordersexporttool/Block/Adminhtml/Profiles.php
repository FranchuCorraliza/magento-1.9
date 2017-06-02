<?php
class Wyomind_Ordersexporttool_Block_Adminhtml_profiles extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_profiles';
		$this->_blockGroup = 'ordersexporttool';
		$this->_headerText = Mage::helper('ordersexporttool')->__('Orders Export Tool');
		$this->_addButtonLabel = Mage::helper('ordersexporttool')->__('Create a new export profile');
		parent::__construct();
	}
}

