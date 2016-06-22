<?php
class Wyomind_Ordersexporttool_Block_Adminhtml_Attributes extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_attributes';
		$this->_blockGroup = 'ordersexporttool';
		$this->_headerText = Mage::helper('ordersexporttool')->__('Orders Export Tool');
		$this->_addButtonLabel = Mage::helper('ordersexporttool')->__('Create a new custom attribute');
		parent::__construct();
	}
}

