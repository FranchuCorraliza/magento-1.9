<?php

class Magestore_Auction_Model_Watcher extends Mage_Core_Model_Abstract
{
	public function _construct()
    {
        parent::_construct();
        $this->_init('auction/watcher');
    }
	
	public function getListByCustomerId($customer_id)
	{
		$auctionIds = array(0);
		$watchers = $this->getCollection()
						->addFieldToFilter('customer_id',$customer_id)
						->addFieldToFilter('status',1)
						;
		if(count($watchers)){
			foreach($watchers as $watcher){
				$auctionIds[] = $watcher->getProductauctionId();
			}
		}
		$collection = Mage::getResourceModel('auction/productauction_collection')
							->addFieldToFilter('productauction_id',array('in'=>$auctionIds))
							->addFieldToFilter('status',array('neq'=>2))
						;
		return $collection;
	}
}