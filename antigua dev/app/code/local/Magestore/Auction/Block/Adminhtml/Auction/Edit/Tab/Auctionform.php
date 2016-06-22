<?php

class Magestore_Auction_Block_Adminhtml_Auction_Edit_Tab_Auctionform extends Mage_Adminhtml_Block_Widget_Form
{
	public function __construct()
	{
		$this->setTemplate('auction/productauction.phtml');
	}
	
     public function getAuctionbid()     
     { 
        if (!$this->hasData('auction_data')) {
            $this->setData('auction_data', Mage::registry('auction_data'));
        }
        return $this->getData('auction_data');
    }
	
	public function getProductauction()
	{
		$productauctionId = $this->getAuctionbid()->getProductauctionId();
		
		if($productauctionId)
		{
			return Mage::getModel('auction/productauction')->load($productauctionId);
		}
	}
}