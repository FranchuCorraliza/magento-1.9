<?php

class Magestore_Auction_Block_Adminhtml_Sales_Tab_Auction 
	extends Mage_Adminhtml_Block_Widget_Form
	implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('auction/auction.phtml');
	}
	
	public function getTabLabel()	{
		return Mage::helper('auction')->__('AuctionBid');
	}

	public function getTabTitle() {
		return Mage::helper('sales')->__('AuctionBid');
	}
	
	public function canShowTab()	{
		if($this->getAuctionbid())	
			return true;
		else
			return false;
	}
	
	public function isHidden()	{
		if($this->getAuctionbid())
			return false;
		else
			return true;
	}		
	
	public function getAuctionbid()
	{
		if(!$this->hasData('auction'))
		{
			$auction = null;
			
			$order = $this->getOrder();
			
			if (!$order) 
			{
				$this->setData('auction',null);
				return $this->getData('auction');
			}
			
			$order_id = $order->getId();
			
			// $entity_type = Mage::getSingleton("eav/entity_type")->loadByCode("order");
			
			// $entity_type_id = $entity_type->getId();
			
			// $attribute = Mage::getModel("eav/entity_attribute")->load("order_bid_id","attribute_code");

			// $attribute_id = $attribute->getId();
				
			// $pretable =  Mage::helper('auction')->getTablePrefix();
			
			// $resource = Mage::getSingleton('core/resource');			
			
			// $read = $resource->getConnection('core_read');
			
			// $select = $read->select()
						   // ->from( $pretable ."sales_order_entity_int",array('value'))
						   // ->where("entity_type_id=?",$entity_type_id)
						   // ->where("attribute_id=?",$attribute_id)
						   // ->where("entity_id=?",$order_id);
			
			// $attribute = $read->fetchRow($select);		
			
			// if(isset($attribute['value']))
				
				$auction = Mage::getModel('auction/auction')->load($order_id, "order_id");	
				// var_dump($auction->getData()); die();
			$this->setData('auction',$auction);
		}
		
		return 	$this->getData('auction');
		
	}
	
	public function getOrder()
    {       
        if (Mage::registry('current_order')) {
            return Mage::registry('current_order');
        }
        if (Mage::registry('order')) {
            return Mage::registry('order');
        } 
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
	
 	public function getCustomerUrl($customer_id)
	{
		return $this->getUrl('adminhtml/customer/edit',array('id'=>$customer_id));
	}	
	
	public function getProductauction()
	{
		$productauctionId = $this->getAuctionbid()->getProductauctionId();
		
		if($productauctionId)
		{
			return Mage::getModel('auction/productauction')->load($productauctionId);
		}
	}
        
        public function getCustomer(){
            return Mage::getModel('customer/customer')->load($this->getOrder()->getCustomerId());
        }

}