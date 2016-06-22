<?php

class Magestore_Auction_Model_System_Template_Noticefailder
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
                'value'=> 'magestore_auction_notice_tofailder',
                'label' => 'Notice to bidder when he is not a winner.(Default) '
            )
        );		
		
		return $options;
    }
}