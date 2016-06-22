<?php

class Magestore_Auction_Model_Rewrite_TaxConfig extends Mage_Tax_Model_Config{

    public function priceIncludesTax($store=null)
    {
		$bid_id = Mage::getSingleton('core/session')->getData('bid_id');
		
		if(!$bid_id){
			return parent::priceIncludesTax($store);
		} else {
			if((int)Mage::getStoreConfig('auction/tax/is_included_tax')==1)
				return true;
			else
				return false;
		}		
    }	

}