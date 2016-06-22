<?php

class Magestore_Auction_IndexController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        if (Mage::getStoreConfig('auction/general/bidder_status') != 1) {
            $this->_redirect('', array());
            return;
        }
        if (!Mage::registry('current_category')) {
            $category = Mage::getModel('catalog/category')->load(Mage::app()->getStore()->getRootCategoryId())
                    ->setIsAnchor(1)
                    ->setName(Mage::helper('core')->__('Auctions'))
                    ->setDisplayMode('PRODUCTS');
            Mage::register('current_category', $category);
        }
        Mage::helper('auction')->updateAuctionStatus();
        $this->loadLayout();
        $this->getLayout()
                ->getBlock('head')
                ->setTitle(Mage::helper('core')->__('Auctions'));
        $this->renderLayout();
    }

    public function checkbiddernameAction() {
        $html = "";
        $bidder_name = $this->getRequest()->getParam('bidder_name');

        $collection = Mage::getResourceModel('customer/customer_collection')
                ->addAttributeToFilter('bidder_name', $bidder_name);
        if (count($collection)&&$bidder_name!='') {
            $html .= '<input type="hidden" value="2" id="is_valid_bidder_name">';
            $html .= '<div class="error-msg"><p>' . Mage::helper('core')->__('This biddder name is existed') . '</p></div>';
        } else {
            $html .= '<input type="hidden" value="1" id="is_valid_bidder_name">';
            $html .= '<div class="success-msg"><p>' . Mage::helper('core')->__('You can use this bidder name') . '</p></div>';
        }
        $this->getResponse()->setBody($html);
    }

    public function savebiddernameAction() {
        $bidder_name = $this->getRequest()->getPost('bidder_name');
        if($bidder_name==''){
            Mage::getSingleton('core/session')->setData('save_biddername_error', Mage::helper('auction')->__('Please enter your bidder name!'));
            $this->_redirect('auction/index/customerbid', array());
            return;
        }
        $collection = Mage::getResourceModel('customer/customer_collection')
                ->addAttributeToFilter('bidder_name', $bidder_name);
        
        if (!count($collection)&&$bidder_name!='') {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            try {
                $customer->setBidderName($bidder_name)
                        ->save()
                ;
                Mage::getSingleton('core/session')->addSuccess(Mage::helper('auction')->__('Your bidder name has been successfully created.'));
                $backUrl = Mage::getSingleton('core/session')->getData('auction_backurl');
                if ($backUrl) {
                    Mage::getSingleton('core/session')->unsetData('auction_backurl');
                    $this->getResponse()->setRedirect($backUrl);
                    return;
                }

                $this->_redirect('auction/index/customerbid', array());
                return;
            } catch (Exception $e) {
                $this->_redirect('auction/index/customerbid', array());
                Mage::getSingleton('core/session')->addError($e->getMessage());
                return;
            }
        } else {
            Mage::getSingleton('core/session')->setData('save_biddername_error', Mage::helper('auction')->__('Bidder name already exists!'));
            $this->_redirect('auction/index/customerbid', array());
            return;
        }
    }

    public function updateauctioninfoAction() {
        $auction_id = $this->getRequest()->getParam('id');
        $tmpl = $this->getRequest()->getParam('tmpl');
        $cur_bid_id = $this->getRequest()->getParam('current_bid_id');

        Mage::helper('auction')->updateAuctionStatus($auction_id);

        $auction = Mage::getModel('auction/productauction')->load($auction_id);
        $lastBid = $auction->getLastBid();
        $auction->setLastBid($lastBid);

        if ((int) $cur_bid_id == (int) $lastBid->getId()) //not updated
            return;

        $result = null;
        if ($tmpl == 'auctioninfo') {
            $new_endtime = strtotime($auction->getEndTime() . ' ' . $auction->getEndDate());
            $now_time = Mage::getSingleton('core/date')->timestamp(time());

            $result .= '<div id="result_auction_id">' . $auction->getId() . '</div>';
            $result .= '<div id="result_auction_end_time_' . $auction->getId() . '">' . $new_endtime . '</div>';
            $result .= '<div id="result_auction_now_time_' . $auction->getId() . '">' . $now_time . '</div>';
            $result .= '<div id="result_auction_info_' . $auction->getId() . '">' . $this->_getAuctionInfo($auction, $lastBid) . '</div>';
            $result .= '<div id="result_price_condition_' . $auction->getId() . '">' . $this->_getPriceAuction($auction, $lastBid) . '</div>';
            $result .= '<div id="result_current_bid_id_' . $auction->getId() . '">' . $lastBid->getId() . '</div>';
        } else {
            $result .= '<div id="result_product_id">' . $auction->getProductId() . '</div>';
            $result .= '<div id="result_auction_info_' . $auction->getProductId() . '">' . $this->_getAuctionInfo($auction, $lastBid, $tmpl) . '</div>';
        }
        $this->getResponse()->setHeader('Content-type', 'application/x-json');
        $this->getResponse()->setBody($result);
    }

    public function updatepriceconditionAction() {
        $auction_id = $this->getRequest()->getParam('id');
        $auction = Mage::getModel('auction/productauction')->load($auction_id);
        $lastBid = $auction->getLastBid();
        $min_next_price = $auction->getMinNextPrice();
        $max_next_price = $auction->getMaxNextPrice();
        $max_condition = $max_next_price ? ' ' . $this->__('to') . ' ' . Mage::helper('core')->currency($max_next_price) : '';

        if ($max_condition)
            $html = '(' . Mage::helper('core')->__('Enter an amount from') . ' ' . Mage::helper('core')->currency($min_next_price) . $max_condition . ')';
        else
            $html = '(' . Mage::helper('core')->__('Enter %s or more',Mage::helper('core')->currency($min_next_price)) . ')';

        $this->getResponse()->setHeader('Content-type', 'application/x-json');
        $this->getResponse()->setBody($html);
    }

    public function customerbidAction() {
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->_redirect('customer/account/login', array());
            return;
        }

        Mage::helper('auction')->updateAuctionStatus();
        $this->loadLayout();
        $this->getLayout()
                ->getBlock('head')
                ->setTitle(Mage::helper('core')->__('My Bids'));

        $listBidBlock = $this->getLayout()->getBlock('customerbid');
        $pager = $this->getLayout()->createBlock('page/html_pager', 'auction.bid.pager')
                ->setCollection($listBidBlock->getListCustomerbid());
        $listBidBlock->setChild('pager', $pager);
        $this->renderLayout();
    }

    public function watchlistAction() {
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->_redirect('customer/account/login', array());
            return;
        }

        Mage::helper('auction')->updateAuctionStatus();
        $this->loadLayout();
        $this->getLayout()
                ->getBlock('head')
                ->setTitle(Mage::helper('core')->__('My Watched Autions'));

        $listAuctionBlock = $this->getLayout()->getBlock('watchlist');
        $pager = $this->getLayout()->createBlock('page/html_pager', 'watchlist.pager')
                ->setCollection($listAuctionBlock->getAuctionCollection());
        $listAuctionBlock->setChild('pager', $pager);
        $this->renderLayout();
    }

    public function autobidlistAction() {
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->_redirect('customer/account/login', array());
            return;
        }

        Mage::helper('auction')->updateAuctionStatus();
        $this->loadLayout();
        $this->getLayout()
                ->getBlock('head')
                ->setTitle(Mage::helper('core')->__('My Auto Bids'));

        $listAutoBidBlock = $this->getLayout()->getBlock('autobidlist');
        $pager = $this->getLayout()->createBlock('page/html_pager', 'autobidlist.pager')
                ->setCollection($listAutoBidBlock->getBidCollection());
        $listAutoBidBlock->setChild('pager', $pager);
        $this->renderLayout();
    }

    public function emailsettingAction() {
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->_redirect('customer/account/login', array());
            return;
        }

        $this->loadLayout();
        $this->getLayout()
                ->getBlock('head')
                ->setTitle(Mage::helper('core')->__('Auction Email Settings'));

        $this->renderLayout();
    }

    public function saveemailAction() {
        $param = $this->getRequest()->getPost();
        if (!isset($param['place_bid'])) {
            $param['place_bid'] = '0';
        }
        if (!isset($param['place_autobid'])) {
            $param['place_autobid'] = '0';
        }
        if (!isset($param['overbid'])) {
            $param['overbid'] = '0';
        }
        if (!isset($param['overautobid'])) {
            $param['overautobid'] = '0';
        }
        if (!isset($param['cancel_bid'])) {
            $param['cancel_bid'] = '0';
        }
        if (!isset($param['highest_bid'])) {
            $param['highest_bid'] = '0';
        }
        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $param['customer_id'] = $customerId;
        $model = Mage::getModel('auction/email');
        $id = $model->getCollection()->addFieldToFilter('customer_id', $customerId)->getFirstItem()->getId();
        try {
            $model->setData($param)->setId($id)->save();
            Mage::getSingleton('core/session')->addSuccess(Mage::helper('auction')->__('Your email settings have been saved successfully.'));
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError(Mage::helper('auction')->__('An error has occurred when saving your email settings.'));
            $this->_redirect('*/*/emailsetting');
        }
        $this->_redirect('*/*/emailsetting');
    }

    public function checkoutAction() {
        $bid_id = $this->getRequest()->getParam('id');

        if (!$bid_id) {
            $this->_redirect('*/*/customerbid', array());
            return;
        }

        $bid = Mage::getModel('auction/auction')->load($bid_id);

        //check authentication
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if (!$customer || ($customer->getId() != $bid->getCustomerId())) {
            $this->_redirect('*/*/customerbid', array());
            return;
        }

        if ($bid->getStatus == 6) { //complete bid
            $this->_redirect('*/*/customerbid', array());
            return;
        }

        $product = Mage::getModel('catalog/product')->load($bid->getProductId());

        $quote = Mage::getSingleton('checkout/session')->getQuote();
        if (!$quote->getId() || $quote->getId() <= 0) {
            $quote = Mage::getModel('sales/quote')->assignCustomer(Mage::getModel('customer/customer')->load($customer->getId()));
            $quote->setStoreId(Mage::app()->getStore()->getStoreId());
        } else {
            $items = $quote->getAllItems();
            foreach ($items as $item) {
                $bidId = $item->getOptionByCode('bid_id');
                if ($bidId != null && $bidId->getValue() > 0) {
                    if ($bidId->getValue() == $bid_id) {
                        Mage::getSingleton('checkout/session')->addError($this->__('You cannot update the quantity of autioned product(s).'));
                        $this->_redirect('checkout/cart', array());
                        return;
                    }
                }
            }
        }
        try {
            $quoteItem = Mage::getModel('sales/quote_item')->setProduct($product);
            $quoteItem->setCustomPrice($bid->getPrice());
            $quoteItem->setOriginalCustomPrice($bid->getPrice());
            $quoteItem->addOption(array(
                'product_id' => $product->getId(),
                'product' => $product,
                'label' => 'Auction',
                'code' => 'bid_id',
                'value' => $bid_id,
            ));
            $quoteItem->setQty(1);
            $quoteItem->getProduct()->setIsSuperMode(true);
            Mage::getSingleton('core/session')->setData('checkout_auction', true);
            $quote->addItem($quoteItem);
            $quote->collectTotals();
            $quote->save();

            Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
            Mage::getSingleton('checkout/session')->addSuccess(Mage::helper('checkout')->__('The auctioned product %s has been added to cart successfully at your winning price.', $product->getName()));
            $this->_redirect('checkout/cart', array());
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->setData('bid_addcart_error', $e->getMessage());
            $this->_redirect('*/*/customerbid', array());
        }
    }

    public function viewbidsAction() {
        $auction_id = $this->getRequest()->getParam('id');

        Mage::helper('auction')->updateAuctionStatus($auction_id);

        if (!$auction_id) {
            $this->_redirect('');
            return;
        }

        $auction = Mage::getModel('auction/productauction')->load($auction_id);

        if (($auction->getStatus() != 4) && ($auction->getStatus() != 5)) { //not processing, not completed
            if ($auction->getId())
                $this->_redirect('catalog/product/view', array('id' => $auction->getProductId()));
            else
                $this->_redirect('');

            return;
        }
        Mage::register('productauction_data', $auction);
        Mage::register('product', Mage::getModel('catalog/product')->load($auction->getProductId()));

        $this->loadLayout();
        $this->getLayout()
                ->getBlock('head')
                ->setTitle(Mage::helper('core')->__('Bid History') . ' - ' . $auction->getProductName());

        $listBidBlock = $this->getLayout()->getBlock('auction.history');

        $pager = $this->getLayout()->createBlock('page/html_pager', 'auction.bid.pager')
                ->setCollection($listBidBlock->getListProductBid());

        $listBidBlock->setChild('pager', $pager);

        $this->renderLayout();
    }

    public function changewatcherAction() {
        $result = null;
        $_helper = Mage::helper('auction');
        $notice = Mage::getSingleton('auction/notice');
        $this->getResponse()->setHeader('Content-type', 'application/x-json');
        $productId = $this->getRequest()->getParam('product_id');
        $isWatcher = $this->getRequest()->getParam('is_watcher');
        //check login
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            if (isset($_SERVER['HTTP_REFERER'])) {
                $backUrl = $_SERVER['HTTP_REFERER'];
                Mage::getSingleton('core/session')->setData('auction_backurl', $backUrl);
            }
            $this->_redirect('customer/account/login');
            return;
        }

        $auction = Mage::getModel('auction/productauction')->loadAuctionByProductId($productId);
        if ($auction && $auction->getId()) {
            $product = Mage::getModel('catalog/product')->load($auction->getProductId());
        } else {
            $this->getResponse()->setRedirect($this->getRequest()->getServer('HTTP_REFERER'));
            return;
        }
        if ($auction->getStatus() == 5) { //complete auction
            $this->getResponse()->setRedirect($product->getProductUrl());
            return;
        }

        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $model = Mage::getModel('auction/watcher')->getCollection()
                ->addFieldToFilter('productauction_id', $auction->getId())
                ->addFieldToFilter('customer_id', $customer->getId())
                ->getFirstItem();

        $storeId = Mage::app()->getStore()->getId();

        if ($model->getId()) {
            $model->setStatus($isWatcher);
        } else {
            $model->setProductauctionId($auction->getId())
                    ->setCustomerId($customer->getId())
                    ->setCustomerName($customer->getName())
                    ->setCustomerEmail($customer->getEmail())
                    ->setStoreId($storeId)
                    ->setStatus($isWatcher);
        }

        try {
            $model->setCreatedTime(Mage::getSingleton('core/date')->timestamp(time()))
                    ->save();

            $this->getResponse()->setRedirect($product->getProductUrl());

            return;
        } catch (Exception $e) {
            $this->getResponse()->setRedirect($product->getProductUrl());
        }
    }

    public function bidAction() {
        $result = null;
        $_helper = Mage::helper('auction');
        $notice = Mage::getSingleton('auction/notice');
        $this->getResponse()->setHeader('Content-type', 'application/x-json');

        //check login
        $customerSession = Mage::getSingleton('customer/session');
        if (!$customerSession->isLoggedIn()) {
            if (isset($_SERVER['HTTP_REFERER'])) {
                $backUrl = $_SERVER['HTTP_REFERER'];
                Mage::getSingleton('core/session')->setData('auction_backurl', $backUrl);
            }
            $result .= $notice->getNoticeError($_helper->__('You have to log in to bid.'));
            $this->getResponse()->setBody($result);
            return;
        }

        $bidType = $this->getRequest()->getParam('bid_type');
        $data['price'] = $this->getRequest()->getParam('bid_price');
        $data['product_id'] = $this->getRequest()->getParam('product_id');

        if (!isset($data['price']) || !$data['price']) {
            $result .= $notice->getNoticeError($_helper->__('You bid price is invalid.'));
            $this->getResponse()->setBody($result);
            return;
        }

        $data['price'] = str_replace(',', '', $data['price']);
        $data['price'] = str_replace(' ', '', $data['price']);

        $customer = $customerSession->getCustomer();
        $bidderNameType = Mage::getStoreConfig('auction/general/bidder_name_type');
        if (!$customer->getBidderName()) {
            if ($bidderNameType == '2') {
                if (isset($_SERVER['HTTP_REFERER'])) {
                    $backUrl = $_SERVER['HTTP_REFERER'];
                    Mage::getSingleton('core/session')->setData('auction_backurl', $backUrl);
                }
                $result .= $notice->getNoticeError($_helper->__('You have to create a bidder name before bidding for auctioned products.'));
                $this->getResponse()->setBody($result);
                return;
            }
            if ($bidderNameType == '3') {
                if (!$customer->getBidderName()) {
                    $customer->setBidderName($customer->getName())
                            ->save();
                }
            }
        }
        $product = Mage::getModel('catalog/product')->load($data['product_id']);
        $auction = Mage::getModel('auction/productauction')->loadAuctionByProductId($data['product_id']);

        if ($auction->getStatus() == 5) { //complete auction
            $result .= $notice->getNoticeError($_helper->__('Completed Auction'));
            $this->getResponse()->setBody($result);
            return;
        }

        $timestamp = Mage::getModel('core/date')->timestamp(time());

        $lastBid = $auction->getLastBid();


        if (!Mage::helper('auction')->checkValidBidPrice($data['price'], $auction, $bidType)) {
            $result .= $notice->getNoticeError($_helper->__('You bid price is invalid.'));
            $this->getResponse()->setBody($result);
            return;
        }
        $data['productauction_id'] = $auction->getId();
        $data['customer_id'] = $customer->getId();
        $data['customer_name'] = $customer->getName();
        $data['customer_email'] = $customer->getEmail();
        $store_id = Mage::app()->getStore()->getId();

        //prepare bidder name
        if ($bidderNameType == '1') {
            $data['bidder_name'] = $_helper->encodeBidderName($auction, $customer);
        } else {
            $data['bidder_name'] = $customer->getBidderName();
        }
        //end bidder name

        /* check standard bid (1) or auto bid (0) */
        if ($bidType) {
            $auctionbid = Mage::getModel('auction/auction');
            $lastAuctionBid = $auction->getLastBid();

            if ($customer->getId() == $lastAuctionBid->getCustomerId()) {

                $result .= $notice->getNoticeError($_helper->__('You have placed the highest bid.'));
                $this->getResponse()->setBody($result);
                return;
            }

            $data['product_name'] = $product->getName();
            $data['created_date'] = date('Y-m-d', $timestamp);
            $data['created_time'] = date('H:i:s', $timestamp);
            $data['status'] = 3; //waiting
            $auctionbid->setData($data)
                    ->setStoreId($store_id);

            //get autobids greater  current price (before save)
            $customersId = Mage::getModel('auction/email')->getCollection()->addFieldToFilter('overautobid', 0)->getAllCustomerIds();
            $autobids = Mage::getModel('auction/autobid')->getCollection()
                    ->addFieldToFilter('productauction_id', $auction->getProductauctionId())
                    ->addFieldToFilter('price', array('gteq' => $auction->getMinNextPrice()));

            if (count($customersId) > 0) {
                $autobids->addFieldToFilter('customer_id', array('nin' => $customersId));
            }
            $autobidIds = array();
            foreach ($autobids as $autobid) {
                $autobidIds[] = $autobid->getId();
            }

            try {
                $auctionbid->save();

                $auction->setLastBid($auctionbid);
                $auctionbid->setAuction($auction);
                $auctionbid->emailToWatcher();
                $auctionbid->emailToBidder();
                $auctionbid->emailToAdmin();

                $auctionbid->sendNoticToAllBider($auctionbid->getCustomerId(), $auctionbid->getProductauctionId());

                //get autobids over
                $overAutobids = Mage::getModel('auction/autobid')->getCollection()
                        ->addFieldToFilter('productauction_id', $auction->getProductauctionId())
                        ->addFieldToFilter('price', array('lt' => $auction->getMinNextPrice()))
                        ->addFieldToFilter('autobid_id', array('in' => $autobidIds))
                ;

                if (count($overAutobids))
                    $auctionbid->noticeOverautobid($overAutobids);

                if (strtotime($auction->getEndDate() . ' ' . $auction->getEndTime()) - $timestamp <= $auction->getLimitTime()) {
                    $newTime = $timestamp + (int) $auction->getLimitTime();
                    $new_endDate = date('Y-m-d', $newTime);
                    $new_endTime = date('H:i:s', $newTime);
                    $auction->setEndDate($new_endDate)
                            ->setEndTime($new_endTime);
                    $auction->save();
                }

                if (isset($newTime)) {
                    $result .= '<div id="result_auction_end_time_' . $auction->getId() . '">' . $newTime . '</div>';
                    $result .= '<div id="result_auction_now_time_' . $auction->getId() . '">' . $timestamp . '</div>';
                }
                $result .= '<div id="result_auction_id">' . $auction->getId() . '</div>';
                $result .= '<div id="result_auction_info_' . $auction->getId() . '">' . $this->_getAuctionInfo($auction, $auctionbid) . '</div>';
                $result .= '<div id="result_price_condition_' . $auction->getId() . '">' . $this->_getPriceAuction($auction, $auctionbid) . '</div>';
                $result .= '<div id="result_current_bid_id_' . $auction->getId() . '">' . $auctionbid->getId() . '</div>';
                $result .= $notice->getNoticeSuccess();
                $this->getResponse()->setBody($result);

                $store = Mage::app()->getStore();
                $baseCurrency = $store->getBaseCurrency();
                $currCurrency = $store->getCurrentCurrency();
                if ($baseCurrency->getCode() != $currCurrency->getCode()) {
                    $store->setCurrentCurrencyCode($baseCurrency->getCode());
                    $store->setData('current_currency', $baseCurrency);
                }
                $lastBid = $auction->getLastBid();
                $new_endtime = strtotime($auction->getEndTime() . ' ' . $auction->getEndDate());
                $now_time = Mage::getSingleton('core/date')->timestamp(time());
                $result = '<div id="result_auction_id">' . $auction->getId() . '</div>';
                $result .= '<div id="result_auction_end_time_' . $auction->getId() . '">' . $new_endtime . '</div>';
                $result .= '<div id="result_auction_now_time_' . $auction->getId() . '">' . $now_time . '</div>';
                $result .= '<div id="result_auction_info_' . $auction->getId() . '">' . $this->_getAuctionInfo($auction, $lastBid) . '</div>';
                $result .= '<div id="result_price_condition_' . $auction->getId() . '">' . $this->_getPriceAuction($auction, $lastBid) . '</div>';
                $result .= '<div id="result_current_bid_id_' . $auction->getId() . '">' . $lastBid->getId() . '</div>';

                $auctionbid->setAuctioninfo($result);

                $result = '<div id="result_product_id">' . $auction->getProductId() . '</div>';
                $result .= '<div id="result_auction_info_' . $auction->getProductId() . '">' . $this->_getAuctionInfo($auction, $lastBid, 'auctionlistinfo') . '</div>';

                $auctionbid->setAuctionlistinfo($result)->save();
                if ($baseCurrency->getCode() != $currCurrency->getCode()) {
                    $store->setCurrentCurrencyCode($currCurrency->getCode());
                    $store->setData('current_currency', $currCurrency);
                }
                $check = true;
            } catch (Exception $e) {
                $result .= $notice->getNoticeError($e->getMessage());
                $this->getResponse()->setBody($result);
            }
        } else {
            $autobid = Mage::getModel('auction/autobid')->getCollection()
                    ->addFieldToFilter('customer_id', $customer->getId())
                    ->addFieldToFilter('productauction_id', $auction->getId())
                    ->addFieldToFilter('price', array('gt' => $auction->getMinNextPrice()))
                    ->getFirstItem();
            $check_autobid = Mage::getStoreConfig('auction/general/auto_bid');

            // check allow customer config change autobid price for multiple times.
            if ($check_autobid == 1) {
                $data['created_time'] = date('Y-m-d H:i:s', Mage::getSingleton('core/date')->timestamp(time()));
                $autobid->setData($data)
                        ->setStoreId($store_id);
                try {
                    $autobid->save();
                    $autobid->emailToBidder();
                    $check = true;

                    $result .= $notice->getNoticeSuccess($_helper->__('You have placed an auto bid successfully.'));
                    $this->getResponse()->setBody($result);
                } catch (Exception $e) {
                    $result .= $notice->getNoticeError($e->getMessage());
                    $this->getResponse()->setBody($result);
                }
            } elseif ($check_autobid == 2 && !($autobid->getId())) {
                $data['created_time'] = date('Y-m-d H:i:s', Mage::getSingleton('core/date')->timestamp(time()));
                $autobid->setData($data)
                        ->setStoreId($store_id);

                try {
                    $autobid->save();
                    $autobid->emailToBidder();
                    $check = true;
                    $result .= $notice->getNoticeSuccess($_helper->__('You have placed an auto bid successfully.'));
                    $this->getResponse()->setBody($result);
                } catch (Exception $e) {
                    $result .= $notice->getNoticeError($e->getMessage());
                    $this->getResponse()->setBody($result);
                }
            } else {
                $result .= $notice->getNoticeError($_helper->__('You have already placed an auto bid for this auction.'));
                $this->getResponse()->setBody($result);
                return;
            }
        }
        if ($check && $check == true) {
            Mage::getModel('auction/event')->autobid($auction->getProductauctionId());
            if ($bidType) {
                $lastAuctionBid = $auction->getLastBid();
                if ($lastAuctionBid->getId() == $auctionbid->getId()) {
                    Mage::getModel('auction/event')->autobid($auction->getProductauctionId());
                }
            }
        }
    }

    public function cancelBidAction() {
        $id = $this->getRequest()->getParam('id');

        $bid = Mage::getModel('auction/auction')->load($id);

        if ($bid->getStatus() != 1 && $bid->getStatus() != 3) {
            $this->_redirect('auction/index/customerbid');
            return;
        }
        try {
            $bid->setStatus(2)->save();
            Mage::getSingleton('core/session')->addSuccess(Mage::helper('auction')->__('The bid has been canceled.'));
            $this->_redirect('auction/index/customerbid');
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addSuccess(Mage::helper('auction')->__('The bid cancelation has been failed. Please try again.'));
            $this->_redirect('auction/index/customerbid');
        }
    }

    protected function _getAuctionInfo($auction, $lastBid = null, $tmpl = null) {
        $lastBid = $lastBid ? $lastBid : $auction->getLastBid();
        $tmpl = $tmpl ? $tmpl : 'auctioninfo';
        $auction->setLastBid($lastBid);
        $block = $this->getLayout()->createBlock('auction/auction');
        $block->setTemplate('auction/' . $tmpl . '.phtml');
        $block->setData('auction', $auction);

        return $block->toHtml();
    }

    protected function _getPriceAuction($auction, $lastBid = null) {
        $auction->setCurrentPrice(null)
                ->setMinNextPrice(null)
                ->setMaxNextPrice(null);

        $min_next_price = $auction->getMinNextPrice();
        $max_next_price = $auction->getMaxNextPrice();
        $max_condition = $max_next_price ? ' ' . Mage::helper('core')->__('to') . ' ' . Mage::helper('core')->currency($max_next_price) : '';
        if ($max_condition)
            $html = '(' . Mage::helper('core')->__('Enter an amount from') . ' ' . Mage::helper('core')->currency($min_next_price) . $max_condition . ')';
        else
            $html = '(' . Mage::helper('core')->__('Enter %s or more',Mage::helper('core')->currency($min_next_price)) . ')';

        return $html;
    }
}
