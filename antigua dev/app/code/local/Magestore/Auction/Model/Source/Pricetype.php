<?php

class Magestore_Auction_Model_Source_Pricetype
{
    public function toOptionArray()
    {
        return array(
            array('value'=>0, 'label'=>Mage::helper('auction')->__('Excluding Tax')),
            array('value'=>1, 'label'=>Mage::helper('auction')->__('Including Tax')),
        );
    }
}