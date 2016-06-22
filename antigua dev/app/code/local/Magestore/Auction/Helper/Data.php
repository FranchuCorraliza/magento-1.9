<?php

class Magestore_Auction_Helper_Data extends Mage_Core_Helper_Abstract {

    public function getAuctionUrl() {
        $url = $this->_getUrl("auction/index", array());

        return $url;
    }

    public function getChangeProductUrl() {
        return $this->_getUrl('auctionadmin/adminhtml_productauction/changeproduct', array('_secure' => true));
    }

    public function checkValidBidPrice($price, $auction, $type) {
        $price = floatval($price);

        if ($price > 100000000) {
            return false;
        }
        $min_next_price = $auction->getMinNextPrice();
        $max_next_price = $auction->getMaxNextPrice();

        if ($price < ($min_next_price)) {
            return false;
        }

        if ($type)
            if ($max_next_price)
                if ($price > $max_next_price)
                    return false;

        return true;
    }

    public function getProductAuctionIds($store_id = 0, $featured = null) {
        $IDs = array();
        $productIds = array();
        $collection = Mage::getModel('auction/productauction')->getCollection()
                ->addFieldToFilter('status', array('in' => array(4)));
        if ($featured) {
            $collection->addFieldToFilter('featured', '1');
        }
        if (count($collection)) {
            foreach ($collection as $item) {
                $IDs[] = $item->getProductId();
            }
        }
        //multi stores
        if ($store_id) {
            $collection = Mage::getResourceModel('auction/value_collection')
                    ->addFieldToFilter('product_id', array('in' => $IDs))
                    ->addFieldToFilter('store_id', $store_id)
                    ->addFieldToFilter('is_applied', array('neq' => 2)) //not user
            ;
            if (count($collection)) {
                foreach ($collection as $item) {
                    $productIds[] = $item->getProductId();
                }
            }

            $IDs = $productIds;
        }

        return $IDs;
    }

    public function updateAuctionStatus($auction_id = null) {
        $collection = Mage::getModel('auction/productauction')->getCollection()
                ->addFieldToFilter('status', array('nin' => array(2, 5, 6)));

        if ($auction_id)
            $collection->addFieldToFilter('productauction_id', $auction_id);

        if (!count($collection))
            return;
        try {
            foreach ($collection as $item) {
                $status = $this->getAuctionStatus($item->getId());
                $item->setData('status', $status);
                $item->save();

                if ($status == 5) { //update bid for complete auction
                    $this->updateBisStatus($item->getId());

                    //send complete auction email
                    /* Mage::helper('auction/email')->sendAuctionCompleteEmail($item); */
                }
            }
        } catch (Exception $e) {
            
        }
    }

    public function updateBisStatus($auction_id) {
        $auction = Mage::getModel('auction/productauction')->load($auction_id);
        $collection = Mage::getModel('auction/auction')->getCollection()
                ->addFieldToFilter('status', array('nin' => array(2)))
                ->addFieldToFilter('productauction_id', $auction_id)
                ->setOrder('auctionbid_id', 'DESC')
        ;

        if (!count($collection))
            return;

        $is_winner = true;
        $email = array();
        $i = 0;
        $isSent = false;
        foreach ($collection as $item) {
            if (!in_array($item->getCustomerEmail(), $email) && $i < $auction->getMultiWinner()) {
                if ($item->getPrice() >= $auction->getReservedPrice()) {
                    $isSent = true;
                    $status = 5; //Winner
                    $email[] = $item->getCustomerEmail();
                    $i++;
                } else {
                    $status = 4;
                }
            } else {
                $status = 4;
            }
            // if($is_winner)
            // {
            // $status = 5;//winner
            // $is_winner = false;
            // } else{
            // $status = 4;//Failed
            // }

            $item->setStatus($status);
            $item->save();
        }
        if ($isSent)
            Mage::helper('auction/email')->sendAuctionCompleteEmail($auction);
    }

    public function autoUpdateBidStatus($auction_id) {
        $collection = Mage::getModel('auction/auction')->getCollection()
                ->addFieldToFilter('status', array('nin' => array(2)))
                ->addFieldToFilter('productauction_id', $auction_id)
                ->setOrder('auctionbid_id', 'DESC');

        if (!count($collection))
            return;

        $auction = Mage::getModel('auction/productauction')
                ->load($auction_id);
        if ($auction->getStatus() <= 4)
            $status = 3;
        else
            $is_winner = true;

        $i = 0;
        $email = array();
        foreach ($collection as $item) {
            if (!in_array($item->getCustomerEmail(), $email) && $i < $auction->getMultiWinner()) {
                if ($item->getPrice() >= $auction->getReservedPrice()) {
                    $status = 5; //Winner
                    $email[] = $item->getCustomerEmail();
                    $i++;
                } else {
                    $status = 4;
                }
            } else {
                $status = 4;
            }

            $item->setStatus($status);
            $item->save();
        }
        if ($auction->getStatus() == 5)
            Mage::helper('auction/email')->noticeOutBid($auction);
    }

    public function getListBuyoutStatus() {
        return array(1 => $this->__('Yes'),
            2 => $this->__('No'),
        );
    }

    public function getListFeaturedStatus() {
        return array(1 => $this->__('Yes'),
            2 => $this->__('No'),
        );
    }

    public function getListBidStatus() {
        return array(1 => $this->__('Enabled'),
            2 => $this->__('Disabled'),
            3 => $this->__('Waiting'),
            4 => $this->__('Failed'),
            5 => $this->__('Winner'),
            6 => $this->__('Complete'),
        );
    }

    public function getListAuctionStatus() {
        return array(1 => $this->__('Enabled'),
            2 => $this->__('Disabled'),
            3 => $this->__('Not Start'),
            4 => $this->__('Processing'),
            5 => $this->__('Complete'),
            6 => $this->__('Closed')
        );
    }

    public function getOptionStatus() {
        return array(array('value' => 1, 'label' => $this->__('Enabled')),
            array('value' => 2, 'label' => $this->__('Disabled')),
            array('value' => 3, 'label' => $this->__('Not Start')),
            array('value' => 4, 'label' => $this->__('Processing')),
            array('value' => 5, 'label' => $this->__('Complete')),
            array('value' => 6, 'label' => $this->__('Closed')),
        );
    }

    public function isAuctionProduct($product_id) {
        $collection = Mage::getModel('auction/productauction')->getCollection()
                ->addFieldToFilter('product_id', $product_id)
                ->setOrder('productauction_id', 'DESC');

        if (!count($collection))
            return false;

        foreach ($collection as $item) {
            if ($this->getAuctionStatus($item->getId()) == 4) //auction processing
                return true;
        }

        return false;
    }

    public function getAuctionStatus($auction_id) {
        $productauction = Mage::getModel('auction/productauction')->load($auction_id);

        $timestamp = Mage::getModel('core/date')->timestamp(time());

        $start_time = strtotime($productauction->getStartTime() . ' ' . $productauction->getStartDate());

        $end_time = strtotime($productauction->getEndTime() . ' ' . $productauction->getEndDate());

        if ($start_time > $timestamp)
            $status = 3; //not start

        elseif ($end_time < $timestamp)
            $status = 5; //end
        else {
            $status = 4; //processing
            if ($productauction->getStatus() != 4 && $productauction->getAllowBuyout() == 2) {
                $quoteId = array();
                $quotes = Mage::getModel('sales/quote')->getCollection()->addFieldToFilter('is_active', 1);
                $quotes->getSelect()->join(array('a' => Mage::getSingleton('core/resource')->getTableName('sales/quote_item')), 'main_table.entity_id = a.quote_id', 'a.item_id');
                $quotes->addFieldToFilter('a.product_id', $productauction->getProductId());
                if (count($quotes) > 0) {
                    foreach ($quotes as $quot) {
                        $quoteId[] = $quot->getId();
                    }
                    $quotes = Mage::getModel('sales/quote')->getCollection()->addFieldToFilter('is_active', 1)->addFieldToFilter('entity_id', array('in' => array($quoteId)));
                    foreach ($quotes as $quote) {
                        $items = $quote->getAllItems();
                        foreach ($items as $quoteItems) {
                            $product = $quoteItems->getProduct();
                            if ($product->getId() == $productauction->getProductId()) {
                                $bidId = $quoteItems->getOptionByCode('bid_id');
                                if ($bidId == null || $bidId->getValue() <= 0) {
                                    $quote->removeItem($quoteItems->getId())->save();
                                    $quote->collectTotals();
                                    $quote->save();
                                }
                            }
                        }
                    }
                }
            }
        }
        $preStatus = $productauction->getData('status');
        if ($preStatus == 6)
            $status = 6;
        elseif ($preStatus == 5)
            $status = 5;
        $productauction->setData('status', $status);

        try {
            $productauction->save();
            if ($preStatus != 5 && $status == 5)
                $this->updateBisStatus($auction_id);
            return $status;
        } catch (Exception $e) {
            return $status;
        }
    }

    public function getWinnerBids($auction_id) {
        if ($this->getAuctionStatus($auction_id) != 5) //not end
            return null;

        $winnerBids = Mage::getModel('auction/auction')->getCollection()
                ->addFieldToFilter('productauction_id', $auction_id)
                ->addFieldToFilter('status', 5)
                ->setOrder('auctionbid_id', 'DESC');

        return $winnerBids;
    }

    public function getAuctionTimeLeft($auction_id) {
        $auction = Mage::getModel('auction/productauction')->load($auction_id);

        return $auction->getTimeLeft();
    }

    public function convertMysqlDate($date) {
        return substr($date, 5, 3) . substr($date, -2, 2) . '-' . substr($date, 0, 4);
    }

    public function getTablePrefix() {
        $table = Mage::getResourceSingleton("eav/entity_attribute")->getTable("eav/attribute");

        $prefix = str_replace("eav_attribute", "", $table);

        return $prefix;
    }

    public function encodeBidderName($auction, $customer) {
        $storeId = Mage::app()->getStore()->getId();

        $biddername_prefix = Mage::getStoreConfig('auction/general/bidder_name_prefix', $storeId);

        $bid = Mage::getResourceModel('auction/auction_collection')
                ->addFieldToFilter('productauction_id', $auction->getId())
                ->addFieldToFilter('customer_id', $customer->getId())
                ->addFieldToFilter('bidder_name', array('like' => $biddername_prefix . '%'))
                ->addFieldToFilter('store_id', $storeId)
                ->getFirstItem();

        if (count($bid->getId())) {
            return $bid->getBidderName();
        } else {

            $bid = Mage::getResourceModel('auction/auction_collection')
                    ->addFieldToFilter('productauction_id', $auction->getId())
                    ->addFieldToFilter('bidder_name', array('like' => $biddername_prefix . '%'))
                    ->addFieldToFilter('store_id', $storeId)
                    ->setOrder('auctionbid_id', 'DESC')
                    ->getFirstItem();

            if ($bid->getId()) {
                $no = intval(trim(str_replace($biddername_prefix, '', $bid->getBidderName())));
                return $biddername_prefix . ' ' . ($no + 1);
            } else {
                return $biddername_prefix . ' 1';
            }
        }
    }

    public function getMultiWinnerBid($auction_id) {
        if ($this->getAuctionStatus($auction_id) != 5) //not end
            return null;

        $auction = Mage::getModel('auction/productauction')->load($auction_id);

        $items = Mage::getModel('auction/auction')->getCollection()
                ->addFieldToFilter('productauction_id', $auction_id)
                ->addFieldToFilter('status', array('neq' => 2))
                ->setOrder('auctionbid_id', 'DESC')
        ;


        $items->getSelect()
                ->limit($auction->getMultiWinner())
        ;

        return $items;
    }

    public function getBidderStatus() {
        $status = Mage::getStoreConfig('auction/general/bidder_status', $this->getStoreId());
        if ($status == 1)
            return true;
        return false;
    }

    /* public function getAuctionJs() {
      //$storeId = Mage::app()->getStore()->getId();
      $status = Mage::getStoreConfig('auction/general/is_load_auction_javascript', $this->getStoreId());
      if($status == 1) return true;
      return false;
      }

      public function getAuctionCss() {
      //$storeId = Mage::app()->getStore()->getId();
      $status = Mage::getStoreConfig('auction/general/is_load_auction_css', $this->getStoreId());
      if($status == 1) return true;
      return false;
      }
     */

    public function getStoreId() {
        return Mage::app()->getStore()->getId();
    }

    public function getAddditionTime() {
        $storeId = $this->getStoreId();
        return Mage::getStoreConfig('auction/auction_time/add_time', $storeId) ? Mage::getStoreConfig('auction/auction_time/add_time', $storeId) : '60';
    }

    public function getNextAutobidToRun($auction, $currentAutobid) {
        $autobids = Mage::getModel('auction/autobid')->getCollection()
                ->addFieldToFilter('productauction_id', $auction->getId())
                ->addFieldToFilter('created_time', array('datetime' => true, 'from' => $currentAutobid->getCreatedTime()))
                ->addFieldToFilter('autobid_id', array('neq' => $currentAutobid->getId()))
                ->addFieldToFilter('price', array('gteq' => $auction->getMinNextPrice()))
                ->setOrder('created_time', 'ASC');

        //print_r(count($autobids));die();
        if (count($autobids)) {
            $autobid = $autobids->getFirstItem();
        } else {// get autobid at tail list, return head list 
            $autobid = Mage::getModel('auction/autobid')->getCollection()
                    ->addFieldToFilter('productauction_id', $auction->getId())
                    ->addFieldToFilter('price', array('gteq' => $auction->getMinNextPrice()))
                    ->setOrder('created_time', 'ASC')
                    ->getFirstItem();
        }

        return $autobid;
    }

    public function getLoginButtonImage() {
        $storeId = Mage::app()->getStore()->getId();
        $imageName = Mage::getStoreConfig('auction/auction_image/image_login', $storeId);
        $imgSrc = '';
        if ($imageName) {
            $imgSrc = file_exists(Mage::getBaseDir('media') . DS . 'auction' . DS . 'login' . DS . $imageName) ? Mage::getBaseUrl('media') . 'auction/login/' . $imageName : '';
        }
        return $imgSrc;
    }

    public function getBidButtonImage() {
        $storeId = Mage::app()->getStore()->getId();
        $imgSrc = '';
        $imageName = Mage::getStoreConfig('auction/auction_image/image_bid', $storeId);
        if ($imageName) {
            $imgSrc = file_exists(Mage::getBaseDir('media') . DS . 'auction' . DS . 'bid' . DS . $imageName) ? Mage::getBaseUrl('media') . 'auction/bid/' . $imageName : '';
        }
        return $imgSrc;
    }

    public function getCreateBidNameButtonImage() {
        $storeId = Mage::app()->getStore()->getId();
        $imgSrc = '';
        $imageName = Mage::getStoreConfig('auction/auction_image/image_create_bid_name', $storeId);
        if ($imageName) {
            $imgSrc = file_exists(Mage::getBaseDir('media') . DS . 'auction' . DS . 'createbidname' . DS . $imageName) ? Mage::getBaseUrl('media') . 'auction/createbidname/' . $imageName : '';
        }
        return $imgSrc;
    }

    public function getWatchButtonImage() {
        $storeId = Mage::app()->getStore()->getId();
        $imgSrc = '';
        $imageName = Mage::getStoreConfig('auction/auction_image/image_watch', $storeId);
        if ($imageName) {
            $imgSrc = file_exists(Mage::getBaseDir('media') . DS . 'auction' . DS . 'watch' . DS . $imageName) ? Mage::getBaseUrl('media') . 'auction/watch/' . $imageName : '';
        }
        return $imgSrc;
    }

    public function getStopWatchingButtonImage() {
        $storeId = Mage::app()->getStore()->getId();
        $imgSrc = '';
        $imageName = Mage::getStoreConfig('auction/auction_image/image_stopwatching', $storeId);
        if ($imageName) {
            $imgSrc = file_exists(Mage::getBaseDir('media') . DS . 'auction' . DS . 'stopwatching' . DS . $imageName) ? Mage::getBaseUrl('media') . 'auction/stopwatching/' . $imageName : '';
        }
        return $imgSrc;
    }

    /**
     * Change end time if stauts is complete or close
     * and send mail to all bider when status be changed complete
     * PS: Use in file ProductauctionController.php
     */
    public function setStautsAution($stauts, $id) {

        if ($stauts == 6) {
            $model = Mage::getModel('auction/productauction')->load($id);
            $timestamp = Mage::getModel('core/date')->timestamp(time());
            $datenow = date('Y-m-d', $timestamp);
            $timenow = date('H:i:s', $timestamp);
            $model->setData('end_date', $datenow);
            $model->setData('end_time', $timenow);
            $model->save();
        } elseif ($stauts == 5) {
            $model = Mage::getModel('auction/productauction')->load($id);
            $timestamp = Mage::getModel('core/date')->timestamp(time());
            $timenow = date('H:i:s', $timestamp);
            $datenow = date('Y-m-d', $timestamp);
            $model->setData('end_date', $datenow);
            $model->setData('end_time', $timenow);
            $model->save();
            $this->updateBisStatus($id);
        }
    }

    public function returntext() {
        return 'Featured Auction box can be placed on different positions on your website by using one of the following options below:</br>
                Note: These options are recommended for developers. You shouldn\'t add them on the auction listing page either.';
    }
    
    public function returnblock() {
        return '&nbsp;&nbsp{{block type="auction/list" template="auction/featuredauction.phtml"}}<br>';
    }
    
    public function returntemplate() {
        return "&nbsp;\$this->getLayout()->createBlock('auction/list')->setTemplate('auction/featuredauction.phtml')<br/>&nbsp;&nbsp;->tohtml();";
    }
    
    public function returnlayout() {
        return '&nbsp;&lt;block name="featuredauctions" type="auction/list" template="auction/featuredauction.phtml"/&gt<br/>';
    }

}
