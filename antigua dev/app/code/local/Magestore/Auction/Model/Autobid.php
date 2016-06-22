<?php

class Magestore_Auction_Model_Autobid extends Mage_Core_Model_Abstract {

    const XML_PATH_NOTICE_OVERAUTOBID = "auction/emails/notice_over_autobid_email_template";
    const XML_PATH_NEW_AUTOBID_TO_BIDDER = "auction/emails/newautobid_to_bidder_email_template";
    const XML_PATH_SALES_EMAIL_IDENTITY = "trans_email/ident_sales";

    public function _construct() {
        parent::_construct();
        $this->_init('auction/autobid');
    }

    public function getTimeLeft() {
        if (!$this->getData('timeleft')) {
            $auction = Mage::getModel('auction/productauction')->load($this->getProductauctionId());
            $this->setData('timeleft', $auction->getTimeLeft());
        }
        return $this->getData('timeleft');
    }

    public function getProductName() {
        if (!$this->getData('timeleft')) {
            $auction = Mage::getModel('auction/productauction')->load($this->getProductauctionId());
            $this->setData('product_name', $auction->getProductName());
        }
        return $this->getData('product_name');
    }

    public function getFormatedTime() {
        $bid_date = new Zend_Date($this->getCreatedDate() . ' ' . $this->getCreatedTime(), null, 'en_GB');
        return Mage::helper('core')->formatDate($bid_date, 'medium', true);
    }

    public function getFormatedPrice() {
        return Mage::helper('core')->currency($this->getPrice());
    }

    public function getListByCustomerId($customer_id) {
        $collection = $this->getCollection()
                ->addFieldToFilter('customer_id', $customer_id);
        return $collection;
    }

    public function emailToBidder() {
        $cusId = $this->getCustomerId();
        $customer = Mage::getModel('auction/email')->getCollection()->addFieldToFilter('customer_id', $cusId)->getFirstItem();
        if ($customer->getPlaceBid() == null || $customer->getPlaceBid() == 1) {
            $storeID = $this->getStoreId();
            $translate = Mage::getSingleton('core/translate');
            $translate->setTranslateInline(false);

            $template = Mage::getStoreConfig(self::XML_PATH_NEW_AUTOBID_TO_BIDDER, $storeID);

            $sendTo = array(
                array(
                    'name' => $this->getCustomerName(),
                    'email' => $this->getCustomerEmail(),
                )
            );

            $mailTemplate = Mage::getModel('core/email_template');

            foreach ($sendTo as $recipient) {
                $mailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $storeID))
                        ->sendTransactional(
                                $template, Mage::getStoreConfig(self::XML_PATH_SALES_EMAIL_IDENTITY, $storeID), $recipient['email'], $recipient['name'], array(
                            'autobid' => $this
                                )
                );
            }

            $translate->setTranslateInline(true);

            return $this;
        }
    }

}
