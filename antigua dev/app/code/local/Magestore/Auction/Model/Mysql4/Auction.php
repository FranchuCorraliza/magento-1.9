<?php

class Magestore_Auction_Model_Mysql4_Auction extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the auction_id refers to the key field in your database table.
        $this->_init('auction/auction', 'auctionbid_id');
    }
	
	public function getTotalBidder($auction_id)
	{
		$select = $this->_getReadAdapter()->select()
			->distinct()
			->from(array('a'=>$this->getTable('auction')),'customer_id')
			->where('productauction_id=?',$auction_id);
		
		return count($this->_getReadAdapter()->fetchAll($select));
	}
}