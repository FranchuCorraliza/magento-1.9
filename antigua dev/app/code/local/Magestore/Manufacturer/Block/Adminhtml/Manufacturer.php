<?php
class Magestore_Manufacturer_Block_Adminhtml_Manufacturer extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_manufacturer';
    $this->_blockGroup = 'manufacturer';
    $this->_headerText = Mage::helper('manufacturer')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('manufacturer')->__('Add Item');
    parent::__construct();
	$this->_removeButton('add');	
  }
}