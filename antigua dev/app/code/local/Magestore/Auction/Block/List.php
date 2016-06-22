<?php

class Magestore_Auction_Block_List extends Mage_Core_Block_Template {

    protected function getListAuction() {
        $store_id = Mage::app()->getStore()->getId();
        $Ids = Mage::helper('auction')->getProductAuctionIds($store_id, true);
        $collection = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToSelect('small_image')
                ->addFieldToFilter('entity_id', array('in' => $Ids));
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInSearchFilterToCollection($collection);
        return $collection;
    }

}
