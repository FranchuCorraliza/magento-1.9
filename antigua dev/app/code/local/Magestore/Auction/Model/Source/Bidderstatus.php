<?php

class Magestore_Auction_Model_Source_Bidderstatus
{
    public function toOptionArray()
    {
        return array(
            array('value'=>1, 'label'=>Mage::helper('auction')->__('Enable')),
            array('value'=>2, 'label'=>Mage::helper('auction')->__('Disable')),
        );
    }
}