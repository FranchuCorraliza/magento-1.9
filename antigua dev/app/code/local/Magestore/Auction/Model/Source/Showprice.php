<?php

class Magestore_Auction_Model_Source_Showprice
{
    public function toOptionArray()
    {
        return array(
            array('value'=>1, 'label'=>Mage::helper('auction')->__('Yes')),
            array('value'=>0, 'label'=>Mage::helper('auction')->__('No')),
        );
    }
}