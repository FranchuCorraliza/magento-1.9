<?php

class Magestore_Auction_Model_System_Template_Noticehighest
{
    public function toOptionArray()
    {
        if(!$collection = Mage::registry('config_system_email_template')) {
            $collection = Mage::getResourceModel('core/email_template_collection')
                ->load();

            Mage::register('config_system_email_template', $collection);
        }

        $options = $collection->toOptionArray();
        
        array_unshift(
            $options,
            array(
                'value'=> 'magestore_highest_bid',
                'label' => 'The highest bid notice to bidder (Default)'
            )
        );		
		
		return $options;
    }
}