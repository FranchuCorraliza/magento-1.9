<?php
class Magestore_Auction_Block_Adminhtml_Productauction extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_productauction';
    $this->_blockGroup = 'auction';
    $this->_headerText = Mage::helper('auction')->__('Auction Manager');
    $this->_addButtonLabel = Mage::helper('auction')->__('Add Auction');
    parent::__construct();
//	$this->_addButton('import_auction',array(
//				'label'     => Mage::helper('adminhtml')->__('Import Auctions'),
//				'onclick'   => 'location.href=\''. $this->getUrl('*/adminhtml_productauction/import',array()) .'\'',
//				'class'     => 'add',
//			), -1);		
  }
}