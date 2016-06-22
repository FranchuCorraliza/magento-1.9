<?php

class Magestore_Auction_Block_Customer_Email extends Mage_Core_Block_Template {

    public function getCustomer() {
        if (!$this->hasData('customer')) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $this->setData('customer', $customer);
        }
        return $this->getData('customer');
    }
    
    public function getEmailInfor($customer){
        $model = Mage::getModel('auction/email')->getCollection()->addFieldToFilter('customer_id', $customer->getId())->getFirstItem();
        return $model->getData();
    }

}
