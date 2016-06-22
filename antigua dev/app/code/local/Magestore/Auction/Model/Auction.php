<?php

class Magestore_Auction_Model_Auction extends Mage_Core_Model_Abstract {

    const XML_PATH_SALES_EMAIL_IDENTITY = "trans_email/ident_sales";
    const XML_PATH_ADMIN_EMAIL_IDENTITY = "trans_email/ident_general";
    const XML_PATH_NEW_BID_TO_BIDDER = "auction/emails/newbid_to_bidder_email_template";
    const XML_PATH_NEW_BID_TO_WATCHER = "auction/emails/newbid_to_watcher_email_template";
    const XML_PATH_NEW_BID_TO_ADMIN = "auction/emails/newbid_to_admin_email_template";
    const XML_PATH_NOTICE_WINNER = "auction/emails/notice_winner_email_template";
    const XML_PATH_NOTICE_CANCELED = "auction/emails/notice_cancel_bid_email_template";
    const XML_PATH_NOTICE_HIGHEST = "auction/emails/notice_highest_bid_email_template";
    const XML_PATH_NOTICE_OVERBID = "auction/emails/overbid_to_bidder_email_template";
    const XML_PATH_NOTICE_OVERAUTOBID = "auction/emails/overautobid_to_bidder_email_template";

    public function _construct() {
        parent::_construct();
        $this->_init('auction/auction');
    }

    public function getLastAuctionBid($productauction_id) {
        $collection = $this->getCollection()
                ->addFieldToFilter('productauction_id', $productauction_id)
                ->addFieldToFilter('status', array('nin' => 2))
                ->setOrder('auctionbid_id', 'DESC');

        return $collection->getFirstItem();
    }

    public function getListBidByCustomerId($customerId) {
        $collection = $this->getCollection()
                ->addFieldToFilter('customer_id', $customerId)
                //	->addFieldToFilter('status',array('nin'=>2))
                ->setOrder('auctionbid_id', 'DESC');

        return $collection;
    }

    public function getBidStatus() {
        
    }

    public function is_bidwinner() {
        $bid_winner = Mage::helper('auction')->getWinnerBid($this->getProductauctionId());

        if ($bid_winner && $bid_winner->getId() == $this->getId())
            return true;

        return false;
    }

    public function getFormatedTime() {
        $bid_date = new Zend_Date($this->getCreatedDate() . ' ' . $this->getCreatedTime(), null, 'en_GB');
        return Mage::helper('core')->formatDate($bid_date, 'medium', true);
    }

    public function getFormatedPrice() {
        return Mage::helper('core')->currency($this->getPrice());
    }

    public function getTimeLeft() {
        if (!$this->getData('timeleft')) {
            $this->setData('timeleft', $this->getAuction()->getTimeLeft());
        }
        return $this->getData('timeleft');
    }

    public function getAuction() {
        if (!$this->getData('auction')) {
            $auction = Mage::getModel('auction/productauction')->load($this->getProductauctionId());
            $auction->setStoreId($this->getStoreId());
            $this->setData('auction', $auction);
        }
        return $this->getData('auction');
    }

    public function getOverBid() {
        $multi_winners = $this->getAuction()->getMultiWinner();
        $multi_winners = $multi_winners ? $multi_winners : 1;

        $overBids = $this->getCollection()
                ->addFieldToFilter('productauction_id', $this->getProductauctionId())
                ->addFieldToFilter('auctionbid_id', array('neq' => $this->getId()))
                ->addFieldToFilter('customer_email', array('neq' => $this->getCustomerEmail()))
                ->addFieldToFilter('status', array('neq' => 2))
                ->setOrder('auctionbid_id', 'DESC')
        ;
        $overBids->getSelect()->limit(1, $multi_winners - 1);

        return $overBids->getFirstItem();
    }

    public function save() {
        $is_canceled = false;
        if ($this->getId()) {
            $data = $this->getData();
            $this->load($this->getId());
            if ($this->getStatus() != 2 && isset($data['status']) && $data['status'] == 2) {
                $is_canceled = true;
                $this->noticeCanceled();
            }
            $this->setData($data);
        }
        parent::save();
        if ($is_canceled) {
            $auction = Mage::getModel('auction/productauction')->load($this->getProductauctionId());
            $lastBid = $auction->getLastBid();
            if ($lastBid->getId() < $this->getId())
                $lastBid->noticeHighest();
        }
    }

    public function emailToBidder() {
        $cusId = $this->getCustomerId();
        $customer = Mage::getModel('auction/email')->getCollection()->addFieldToFilter('customer_id', $cusId)->getFirstItem();
        if ($customer->getPlaceBid() == null || $customer->getPlaceBid() == 1) {
            $storeID = $this->getStoreId();
            $translate = Mage::getSingleton('core/translate');
            $translate->setTranslateInline(false);

            $template = Mage::getStoreConfig(self::XML_PATH_NEW_BID_TO_BIDDER, $storeID);

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
                            'bid' => $this
                                )
                );
            }

            $translate->setTranslateInline(true);
        }
        return $this;
    }

    public function emailToWatcher() {
        $storeID = $this->getStoreId();
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);

        $template = Mage::getStoreConfig(self::XML_PATH_NEW_BID_TO_WATCHER, $storeID);

        $overBid = $this->getOverBid();
        $watchers = Mage::getModel('auction/watcher')->getCollection()
                ->addFieldToFilter('productauction_id', $this->getProductauctionId())
                ->addFieldToFilter('status', 1)
                ->addFieldToFilter('customer_id', array('nin' => array($overBid->getCustomerId(), $this->getCustomerId())));
        $i = 0;

        if (!count($watchers))
            return $this;

        $sendTo = array();
        foreach ($watchers as $watcher) {
            $customer = Mage::getModel('customer/customer')->load($watcher->getCustomerId());
            $sendTo[$i]['name'] = $customer->getName();
            $sendTo[$i]['email'] = $customer->getEmail();
            $i++;
        }

        $mailTemplate = Mage::getModel('core/email_template');

        if (count($sendTo)) {
            foreach ($sendTo as $recipient) {
                $mailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $storeID))
                        ->sendTransactional(
                                $template, Mage::getStoreConfig(self::XML_PATH_SALES_EMAIL_IDENTITY, $storeID), $recipient['email'], $recipient['name'], array(
                            'bid' => $this->setWatcherName($recipient['name']),
                            'auction_url' => $this->getAuction()->getAuctionUrl(),
                                )
                );
            }
        }
        $translate->setTranslateInline(true);
        return $this;
    }

    public function emailToAdmin() {
        if (Mage::getStoreConfig(self::XML_PATH_NEW_BID_TO_ADMIN) != '0') {
            $storeID = $this->getStoreId();
            $translate = Mage::getSingleton('core/translate');
            $translate->setTranslateInline(false);

            $template = Mage::getStoreConfig(self::XML_PATH_NEW_BID_TO_ADMIN, $storeID);

            $sendTo = array(
                Mage::getStoreConfig(self::XML_PATH_ADMIN_EMAIL_IDENTITY, $storeID)
            );

            $mailTemplate = Mage::getModel('core/email_template');

            foreach ($sendTo as $recipient) {
                $mailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $storeID))
                        ->sendTransactional(
                                $template, Mage::getStoreConfig(self::XML_PATH_SALES_EMAIL_IDENTITY, $storeID), $recipient['email'], $recipient['name'], array(
                            'bid' => $this->setAdminName($recipient['name'])
                                )
                );
            }

            $translate->setTranslateInline(true);

            return $this;
        }
    }

    public function emailToWinner() {
        if (!Mage::registry('notice_to_winner')) {
            Mage::register('notice_to_winner', '1');
            $storeID = $this->getStoreId();
            $translate = Mage::getSingleton('core/translate');
            $translate->setTranslateInline(false);

            $template = Mage::getStoreConfig(self::XML_PATH_NOTICE_WINNER, $storeID);

            $sendTo = array(
                array(
                    'name' => $this->getBidderName(),
                    'email' => $this->getCustomerEmail(),
                )
            );

            $mailTemplate = Mage::getModel('core/email_template');

            foreach ($sendTo as $recipient) {
                $mailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $storeID))
                        ->sendTransactional(
                                $template, Mage::getStoreConfig(self::XML_PATH_SALES_EMAIL_IDENTITY, $storeID), $recipient['email'], $recipient['name'], array(
                            'bid' => $this
                                )
                );
            }

            $translate->setTranslateInline(true);
            return $this;
        }
    }

    public function noticeCanceled() {
        $cusId = $this->getCustomerId();
        $customer = Mage::getModel('auction/email')->getCollection()->addFieldToFilter('customer_id', $cusId)->getFirstItem();
        if ($customer->getCancelBid() == null || $customer->getCancelBid() == 1) {
            $storeID = $this->getStoreId();
            $translate = Mage::getSingleton('core/translate');
            $translate->setTranslateInline(false);

            $template = Mage::getStoreConfig(self::XML_PATH_NOTICE_CANCELED, $storeID);

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
                            'bid' => $this
                                )
                );
            }

            $translate->setTranslateInline(true);
        }
        return $this;
    }

    public function noticeHighest() {
        $cusId = $this->getCustomerId();
        $customer = Mage::getModel('auction/email')->getCollection()->addFieldToFilter('customer_id', $cusId)->getFirstItem();
        if ($customer->getHighestBid() == null || $customer->getHighestBid() == 1) {
            $storeID = $this->getStoreId();
            $translate = Mage::getSingleton('core/translate');
            $translate->setTranslateInline(false);

            $template = Mage::getStoreConfig(self::XML_PATH_NOTICE_HIGHEST, $storeID);

            $sendTo = array(
                array(
                    'name' => $this->getBidderName(),
                    'email' => $this->getCustomerEmail(),
                )
            );

            $mailTemplate = Mage::getModel('core/email_template');

            foreach ($sendTo as $recipient) {
                $mailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $storeID))
                        ->sendTransactional(
                                $template, Mage::getStoreConfig(self::XML_PATH_SALES_EMAIL_IDENTITY, $storeID), $recipient['email'], $recipient['name'], array(
                            'bid' => $this
                                )
                );
            }

            $translate->setTranslateInline(true);
        }
        return $this;
    }

    public function noticeOverbid() {
        $overBid = $this->getOverBid();
        if (!$overBid->getId())
            return $this;

        $storeID = $this->getStoreId();
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);

        $template = Mage::getStoreConfig(self::XML_PATH_NOTICE_OVERBID, $storeID);

        $sendTo = array(
            array(
                'name' => $overBid->getCustomerName(),
                'email' => $overBid->getCustomerEmail(),
            )
        );

        $mailTemplate = Mage::getModel('core/email_template');

        if (count($sendTo)) {
            foreach ($sendTo as $recipient) {
                $mailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $storeID))
                        ->sendTransactional(
                                $template, Mage::getStoreConfig(self::XML_PATH_SALES_EMAIL_IDENTITY, $storeID), $recipient['email'], $recipient['name'], array(
                            'bid' => $overBid,
                            'higher_bid' => $this,
                            'auction_url' => $this->getAuction()->getAuctionUrl(),
                                )
                );
            }
        }
        $translate->setTranslateInline(true);

        return $this;
    }

    public function noticeOverautobid($overAutobids) {
        $storeID = $this->getStoreId();
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);

        $template = Mage::getStoreConfig(self::XML_PATH_NOTICE_OVERAUTOBID, $storeID);
        $mailTemplate = Mage::getModel('core/email_template');

        foreach ($overAutobids as $overAutobid) {
            //$customer = Mage::getModel('customer/customer')->load($overAutobid->getCustomerId());
            $auction = Mage::getModel('auction/productauction')->load($overAutobid->getProductauctionId());
            $recipient['email'] = $overAutobid->getCustomerEmail();
            $recipient['name'] = $overAutobid->getCustomerName();
            $mailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $storeID))
                    ->sendTransactional(
                            $template, Mage::getStoreConfig(self::XML_PATH_SALES_EMAIL_IDENTITY, $storeID), $recipient['email'], $recipient['name'], array(
                        'autobid' => $overAutobid,
                        'higher_bid' => $this,
                        'auction_url' => $this->getAuction()->getAuctionUrl(),
                            )
            );
        }
        $translate->setTranslateInline(true);
        return $this;
    }

    public function sendNoticToAllBider($customer_id, $production_id) {
        $customersId = Mage::getModel('auction/email')->getCollection()->addFieldToFilter('overbid', 0)->getAllCustomerIds();
        $collection = $this->getCollection()->addFieldToFilter('productauction_id', $production_id)
                ->addFieldToFilter('customer_id', array('neq' => $customer_id))
                ->addFieldToFilter('status', array('neq' => 2))
                ->setOrder('auctionbid_id', 'DESC');
        if (count($customersId) > 0) {
            $collection->addFieldToFilter('customer_id', array('nin' => $customersId));
        }
        $storeID = $this->getStoreId();
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);

        $template = Mage::getStoreConfig(self::XML_PATH_NOTICE_OVERBID, $storeID);


        $mailTemplate = Mage::getModel('core/email_template');

        $customer_id = -1;

        if (count($collection)) {
            foreach ($collection as $item) {

                if ($item->getCustomerId() != $customer_id) {
                    $customer_id = $item->getCustomerId();

                    $mailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $storeID))
                            ->sendTransactional(
                                    $template, Mage::getStoreConfig(self::XML_PATH_SALES_EMAIL_IDENTITY, $storeID), $item->getCustomerEmail(), $item->getCustomerName(), array(
                                'bid' => $item,
                                'higher_bid' => $this,
                                'auction_url' => $this->getAuction()->getAuctionUrl(),
                                    )
                    );
                }
            }
        }
        $translate->setTranslateInline(true);

        return $this;
    }

}
