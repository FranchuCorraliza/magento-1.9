<?php

class Magestore_Auction_Block_Adminhtml_Auction_Edit_Tab_Productform extends Mage_Adminhtml_Block_Widget_Form
{
	public function __construct()
	{
		$this->setTemplate('auction/viewproduct.phtml');
	}
 
    public function getAuctionbid()     
    { 
		if (!$this->hasData('auction_data')) {
            $this->setData('auction_data', Mage::registry('auction_data'));
		}
        return $this->getData('auction_data');
	}
	
	public function getProduct()
	{
		$auction = $this->getAuctionbid();
		
		$product_id = $auction->getProductId();
		
		if($product_id)
		{
			return Mage::getModel('catalog/product')->load($product_id);
		}
		
		return;
	}
	
 	public function getProductUrl($product_id)
	{
		return $this->getUrl('adminhtml/catalog_product/edit',array('id'=>$product_id));
	}
}