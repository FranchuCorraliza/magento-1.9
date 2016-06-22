<?php
class Magestore_Auction_Block_Adminhtml_Transaction extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_transaction';
    $this->_blockGroup = 'auction';
    $this->_headerText = Mage::helper('auction')->__('Transaction Manager');
	
	parent::__construct();
	
	$this->_removeButton('add');
  }
}