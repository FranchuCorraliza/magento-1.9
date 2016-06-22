<?php

class Magestore_Auction_Block_Adminhtml_Productauction_Bid extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'auction';
        $this->_controller = 'adminhtml_auction';
        
        $this->_removeButton('save');
        $this->_removeButton('delete');
		$this->_removeButton('reset');
	}
		
    public function getHeaderText()
    {
		return Mage::helper('auction')->__("Auction Bids for '%s'", $this->htmlEscape(Mage::registry('productauction_data')->getProductName()));
    }
	
     public function getProductauction()     
     { 
        if (!$this->hasData('productauction_data')) 
		{
            $this->setData('productauction_data', Mage::registry('productauction_data'));
        }
        return $this->getData('productauction_data');
    }
		
}