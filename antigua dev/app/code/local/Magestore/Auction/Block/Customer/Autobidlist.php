<?php
class Magestore_Auction_Block_Customer_Autobidlist extends Mage_Core_Block_Template
{	
	public function getCustomer()
	{
		if(! $this->hasData('customer'))
		{
			$customer = Mage::getSingleton('customer/session')->getCustomer();
			$this->setData('customer',$customer);
		}
		return $this->getData('customer');
	}
    
    public function getBidCollection()     
     { 
        if (!$this->hasData('autobidlist')) 
		{
			$customerId  = $this->getCustomer()->getId();
			
			$collection = Mage::getModel('auction/autobid')->getListByCustomerId($customerId);		
			
			$this->setData('autobidlist',$collection);
		}
        return $this->getData('autobidlist');
    }
	
	public function getAuction($bid)
	{
		if(!$this->hasData('auction_'.$bid->getProductauctionId())){
			$auction = Mage::getModel('auction/productauction')->load($bid->getProductauctionId());
			$this->setData('auction_'.$bid->getProductauctionId(),$auction);
		}
		return $this->getData('auction_'.$bid->getProductauctionId());
	}
	
	public function getProduct($bid)
	{
		$auction = $this->getAuction($bid);
		if(!$this->hasData('product_'.$auction->getProductId())){
			$product = Mage::getModel('catalog/product')->load($auction->getProductId());
			$this->setData('product_'.$auction->getProductId(),$product);
		}
		return $this->getData('product_'.$auction->getProductId());
	}
	
	public function getHistoryUrl($bid)
	{
		return $this->getUrl('auction/index/viewbids',array('id'=>$this->getAuction($bid)->getId()));
	}
	
	public function getTotalBid($bid)
	{
		return $this->getAuction($bid)->getTotalBid();
	}
}