<?php

class Magestore_Auction_Model_Value extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('auction/value');
    }
	
	public function loadByAuctionStore($auction_id,$store_id)
	{
		$collection = $this->getCollection()
							->addFieldToFilter('productauction_id',$auction_id)
							->addFieldToFilter('store_id',$store_id)
							;
		$item = $collection->getFirstItem();
		$this->setData($item->getData());
		
		return $this;
	}
}