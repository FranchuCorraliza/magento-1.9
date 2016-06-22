<?php
class Magestore_Auction_Block_Adminhtml_Auction extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_auction';
    $this->_blockGroup = 'auction';
    $this->_headerText = Mage::helper('auction')->__('Auction Bids Manager');
	
	parent::__construct();
	
	$this->_removeButton('add');
  }
}