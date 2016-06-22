<?php

class Magestore_Auction_Block_Adminhtml_Auction_Edit_Tab_Customerform extends Mage_Adminhtml_Block_Widget_Form {

    public function __construct() {
        $this->setTemplate('auction/viewcustomer.phtml');
    }

    public function getAuctionbid() {
        if (!$this->hasData('auction_data')) {
            $this->setData('auction_data', Mage::registry('auction_data'));
        }
        return $this->getData('auction_data');
    }

    public function getCustomer() {
        $auction = $this->getAuctionbid();

        $customer_id = $auction->getCustomerId();

        if ($customer_id) {
            return Mage::getModel('customer/customer')->load($customer_id);
           
        }

        return;
    }

    public function getCustomerUrl($customer_id) {
        return $this->getUrl('adminhtml/customer/edit', array('id' => $customer_id));
    }

}
