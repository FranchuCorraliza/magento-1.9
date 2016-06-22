<?php
class Customex_Orderrequest_Block_Adminhtml_Orderrequest extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_orderrequest';
    $this->_blockGroup = 'orderrequest';
    $this->_headerText = Mage::helper('orderrequest')->__('Manager Order by Request');
    $this->_addButtonLabel = Mage::helper('orderrequest')->__('Add Order');
    parent::__construct();
	$this->_removeButton('add');
  }
}