<?php
class Magestore_Productcontact_Block_Adminhtml_Productcontact extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_productcontact';
    $this->_blockGroup = 'productcontact';
    $this->_headerText = Mage::helper('productcontact')->__('Manager Product Contact');
    $this->_addButtonLabel = Mage::helper('productcontact')->__('Add Contact');
    parent::__construct();
	$this->_removeButton('add');
  }
}