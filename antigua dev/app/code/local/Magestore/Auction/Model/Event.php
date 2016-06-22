<?php

class Magestore_Auction_Model_Event {

    public function getlink() {
        $link = Mage::app()->getRequest()->getRouteName() .
                Mage::app()->getRequest()->getControllerName() .
                Mage::app()->getRequest()->getActionName() .
                Mage::app()->getRequest()->getModuleName();
        return $link;
    }

    public function catalog_product_collection_apply_limitations_before($observer) {
        if (!Mage::registry('load_list_auction')) {
            Mage::register('load_list_auction', '1');
            if ($this->getlink() != 'auctionindexindexauction')
                return $this;
            $productCollection = $observer['collection'];
            $productCollection->addFieldToFilter('entity_id', array('in' => $Ids = Mage::helper('auction')->getProductAuctionIds(Mage::app()->getStore()->getId())));
            return $this;
        }
    }

    public function catalog_product_save_after($observer) {
        $product = $observer->getProduct();

        $auctions = Mage::getResourceModel('auction/productauction_collection')
                ->addFieldToFilter('product_id', $product->getId());
        try {
            if (count($auctions))
                foreach ($auctions as $auction) {
                    if ($auction->getProductName() != $product->getName())
                        $auction->setProductName($product->getName())
                                ->save();
                }

            $bids = Mage::getResourceModel('auction/auction_collection')
                    ->addFieldToFilter('product_id', $product->getId());

            if (count($bids))
                foreach ($bids as $bid) {
                    if ($bid->getProductName() != $product->getName())
                        $bid->setProductName($product->getName())
                                ->save();
                }
        } catch (Exception $e) {
            
        }
    }

    public function customer_save_after($observer) {
        $customer = $observer->getCustomer();

        $bids = Mage::getResourceModel('auction/auction_collection')
                ->addFieldToFilter('customer_id', $customer->getId());

        $bidderNameType = Mage::getStoreConfig('auction/general/bidder_name_type');
        $changed = false;

        try {
            if (count($bids))
                foreach ($bids as $bid) {
                    if ($bid->getCustomerName() != $customer->getName()) {
                        $changed = true;
                        $bid->setCustomerName($customer->getName());
                    }
                    if ($bidderNameType == '2' && $bid->getBidderName() != $customer->getBidderName()) {
                        $changed = true;
                        $bid->setBidderName($customer->getBidderName());
                        $bid->setCustomerName($customer->getName());
                    }
                    if ($bidderNameType == '3' && $bid->getBidderName() != $customer->getBidderName()) {
                        $changed = true;
                        $bid->setBidderName($customer->getName());
                        $bid->setCustomerName($customer->getName());
                    }
                    if ($changed)
                        $bid->save();
                }
            if ($changed && $bidderNameType == '3') {
                $customer->setBidderName($customer->getName())
                        ->save();
            }
        } catch (Exception $e) {
            
        }
    }

    public function customer_login($observer) {
        $backUrl = Mage::getSingleton('core/session')->getData('auction_backurl');
        if ($backUrl) {
            Mage::getSingleton('customer/session')->setBeforeAuthUrl($backUrl);
            Mage::getSingleton('core/session')->unsetData('auction_backurl');
        }
    }

    public function update() {
        Mage::helper('auction')->updateAuctionStatus();
    }

    public function autobid($auctionid = null) {
        if ($auctionid)
            Mage::helper('auction')->updateAuctionStatus($auctionid);
        else
            Mage::helper('auction')->updateAuctionStatus();
        if (Mage::getStoreConfig('auction/general/enable_autobid') != 1) {
            return;
        }
        $auctions = Mage::getModel('auction/productauction')->getCollection()
                ->addFieldToFilter('status', 4);

        if ($auctionid) {
            $auctions->addFieldToFilter('productauction_id', $auctionid);
        }
        foreach ($auctions as $auction) {
            // get product which is auction
            $product = Mage::getModel('catalog/product')->load($auction->getProductId());

            // get price NEXT = price of last bid + min_inteval_price
            $price = $auction->getMinNextPrice();
            // get all auto_bid of productauction
            $autobids = Mage::getModel('auction/autobid')->getCollection()
                    ->addFieldToFilter('productauction_id', $auction->getId())
                    ->setOrder('created_time', 'ASC');

            //last auto bid run
            $lastautobid = $auction->getLastautobid();
            if (!$lastautobid->getId()) {
                $autobid = $autobids->getFirstItem(); //no autobid run
            } else {
                foreach ($autobids as $currentAutobid) {
                    if ($lastautobid->getAutobidId() == $currentAutobid->getId()) {
                        $autobid = Mage::helper('auction')->getNextAutobidToRun($auction, $currentAutobid);
                        break;
                    }
                }
            }
            if (!$autobid || !$autobid->getId()) {
                continue;
            } else {
                $model = Mage::getModel('auction/lastautobid');
                $model->setProductauctionId($auction->getId())
                        ->setAutobidId($autobid->getId())
                        ->save();
            }

            $lastAuctionBid = $auction->getLastBid();

            if ($lastAuctionBid->getCustomerId() != $autobid->getCustomerId()) {
                $timestamp = Mage::getModel('core/date')->timestamp(time());
                $auctionbid = Mage::getModel('auction/auction');
                $customer = Mage::getModel('customer/customer')->load($autobid->getCustomerId());
                $data['price'] = $price;
                $data['product_id'] = $auction->getProductId();
                $data['productauction_id'] = $auction->getId();
                $data['customer_id'] = $customer->getId();
                $data['customer_name'] = $customer->getName();
                $data['customer_email'] = $customer->getEmail();
                $data['product_name'] = $product->getName();
                $data['created_date'] = date('Y-m-d', $timestamp);
                $data['created_time'] = date('H:i:s', $timestamp);
                $data['status'] = 3; //waiting
                $store_id = $autobid->getStoreId();

                //prepare bidder name
                if (Mage::getStoreConfig('auction/general/bidder_name_type') == '1') {
                    $data['bidder_name'] = Mage::helper('auction')->encodeBidderName($auction, $customer);
                } else {
                    $data['bidder_name'] = $customer->getBidderName();
                }
                //end bidder name

                $auctionbid->setData($data)
                        ->setStoreId($store_id);
                
                //get autobids greater  current price (before save)
                $customersId = Mage::getModel('auction/email')->getCollection()->addFieldToFilter('overautobid',0)->getAllCustomerIds();
                $activeAutobids = Mage::getModel('auction/autobid')->getCollection()
                        ->addFieldToFilter('productauction_id', $auction->getProductauctionId())
                        ->addFieldToFilter('price', array('gteq' => $auction->getMinNextPrice()));
                if(count($customersId)>0){
                    $activeAutobids->addFieldToFilter('customer_id', array('nin' => $customersId));
                }
                $autobidIds = array();
                foreach ($activeAutobids as $autobid) {
                    $autobidIds[] = $autobid->getId();
                }

                $auctionbid->save();
                $auction->setLastBid($auctionbid);

                // fix not reset extend time when autobid
                if (strtotime($auction->getEndDate() . ' ' . $auction->getEndTime()) - $timestamp <= $auction->getLimitTime()) {
                    $newTime = $timestamp + (int) $auction->getLimitTime();
                    $new_endDate = date('Y-m-d', $newTime);
                    $new_endTime = date('H:i:s', $newTime);
                    $auction->setEndDate($new_endDate)
                            ->setEndTime($new_endTime);
                    $auction->save();
                }

                $auctionbid->emailToBidder();
                $auctionbid->emailToAdmin();
                $auctionbid->emailToWatcher();
                // $auctionbid->noticeOverbid();
                $auctionbid->sendNoticToAllBider($auctionbid->getCustomerId(), $auctionbid->getProductauctionId());

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

                //get autobids over
                $overAutobids = Mage::getModel('auction/autobid')->getCollection()
                        ->addFieldToFilter('productauction_id', $auction->getProductauctionId())
                        ->addFieldToFilter('price', array('lt' => $auction->getMinNextPrice()))
                        ->addFieldToFilter('autobid_id', array('in' => $autobidIds));

                if (count($overAutobids))
                    $auctionbid->noticeOverautobid($overAutobids);
            }
        }
    }

    protected function _getAuctionInfo($auction, $lastBid = null, $tmpl = null) {
        $lastBid = $lastBid ? $lastBid : $auction->getLastBid();
        $tmpl = $tmpl ? $tmpl : 'auctioninfo';
        $auction->setLastBid($lastBid);
        $block = Mage::getModel('core/layout')->createBlock('auction/auction');
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
        $max_condition = $max_next_price ? ' ' . Mage::helper('core')->__('and') . ' ' . Mage::helper('core')->currency($max_next_price) : '';
        if ($max_condition)
            $html = '(' . Mage::helper('core')->__('Enter an amount from') . ' ' . Mage::helper('core')->currency($min_next_price) . $max_condition . ')';
        else
            $html = '(' . Mage::helper('core')->__('Enter %s or more',Mage::helper('core')->currency($min_next_price)) . ')';
        return $html;
    }

    public function catalog_product_is_salable_after($observer) {
        $links = Mage::app()->getRequest()->getRouteName() .
                Mage::app()->getRequest()->getControllerName() .
                Mage::app()->getRequest()->getActionName();
        if ($links != 'catalogcategoryview' && $links != 'catalogsearchresultindex') {
            $observer->getEvent()->getControllerAction();
            $product = $observer->getProduct();
            $salable = $observer->getSalable();
            $auction = Mage::getModel('auction/productauction')->checkAuctionBuyoutByProductId($product->getId());
            if ($auction) {
                $salable->setIsSalable(false);
            }
        }
        return $this;
    }

    public function checkout_cart_add($observer) {
        if (!Mage::registry('add_product')) {
            Mage::register('add_product', '1');
            Mage::getSingleton('checkout/session')->getMessages(true);
            $productId = Mage::app()->getRequest()->getParam('product', 0);
            if (!$productId) {
                return;
            }
            $check = Mage::getModel('auction/productauction')->checkAuctionBuyoutByProductId($productId);
            if ($check) {
                $action = $observer->getEvent()->getControllerAction();
                Mage::getSingleton('checkout/session')->addError(Mage::helper('checkout')->__('You cannot purchase this product which is being auctioned or reserved by the winning bidder(s).'));
                $action->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                $product = Mage::getModel('catalog/product')->load($productId);
                $redirectUrl = $product->getProductUrl();
                Mage::app()->getResponse()->setRedirect($redirectUrl);
                return;
            }
            Mage::getSingleton('core/session')->setData('add_auction', true);
            return;
        }
    }

    public function quote_item_save_before($observer) {
        if (Mage::getSingleton('core/session')->getData('checkout_auction') == true) {
            Mage::getSingleton('core/session')->setData('checkout_auction', false);
            return;
        }
        if (Mage::getSingleton('core/session')->getData('add_auction') == true) {
            Mage::getSingleton('core/session')->setData('add_auction', false);
            return;
        }
        Mage::getSingleton('checkout/session')->getMessages(true);
        $item = $observer['item'];
        $bidId = $item->getOptionByCode('bid_id');
        if ($bidId != null && $bidId->getValue() > 0) {
            $item->setQty(1);
            Mage::getSingleton('checkout/session')->getMessages(true);
            Mage::getSingleton('checkout/session')->addError(Mage::helper('auction')->__('You cannot update the quantity of autioned product(s).'));
        }
    }

    public function ajaxUpdateCartBefore($observer) {
        $action = $observer->getEvent()->getControllerAction();
        $id = (int) $action->getRequest()->getParam('id');
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $items = $quote->getAllItems();
        foreach ($items as $item) {
            if ($item->getId() == $id) {
                $bidId = $item->getOptionByCode('bid_id');
                if ($bidId != null && $bidId->getValue() > 0) {
                    $action->loadLayout();
                    $action->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                    $result = array();
                    $result['error'] = Mage::helper('auction')->__('Can not update auction quantity!');
                    $action->getResponse()->setHeader('Content-type', 'application/json');
                    $action->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                }
                break;
            }
        }
    }

    public function updateItemOptionsCartBefore($observer) {
        $action = $observer->getEvent()->getControllerAction();
        $id = (int) $action->getRequest()->getParam('id');
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $items = $quote->getAllItems();
        foreach ($items as $item) {
            if ($item->getId() == $id) {
                $bidId = $item->getOptionByCode('bid_id');
                if ($bidId != null && $bidId->getValue() > 0) {
                    $action->loadLayout();
                    $action->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                    Mage::getSingleton('checkout/session')->addError(Mage::helper('auction')->__('Can not update auction quantity!'));
                    Mage::app()->getResponse()->setRedirect(Mage::getUrl('checkout/cart'));
                }
                break;
            }
        }
    }

    public function after_save_order($observer) {
        if (!Mage::registry('check_transaction')) {
            Mage::register('check_transaction', '1');
            $order = $observer->getEvent()->getOrder();
            $quoteId = $order->getQuoteId();
            $quote = Mage::getModel('sales/quote')->load($quoteId);
            $items = $quote->getAllItems();
            foreach ($items as $item) {
                $bidId = $item->getOptionByCode('bid_id');
                if ($bidId != null && $bidId->getValue() > 0) {
                    try {
                        $bid = Mage::getModel('auction/auction')->load($bidId->getValue());
                        $bid->setStatus(6);
                        $bid->setOrderId($order->getId());
                        $bid->save();
                        $transactionModel = Mage::getModel('auction/transaction');
                        $transactionModel->setOrderId($order->getId())
                                ->setProductauctionId($bid->getProductauctionId())
                                ->setTransactionPrice($bid->getPrice())
                                ->save();
                    } catch (Exception $e) {
                        Mage::log($e->getMessage(), null, 'auction.log');
                    }
                }
            }
        }
    }

    public function change_config_auction($observer) {
        $website = $observer->getEvent()->getWebsite();
        $store = $observer->getEvent()->getStore();
        if (Mage::app()->getStore($store)->getId() > 0) {
            $value = Mage::getStoreConfig('auction/general/delay_time', Mage::app()->getStore($store)->getId());
        } else {
            if (Mage::app()->getWebsite($website)->getId() > 0) {
                $value = Mage::app()->getWebsite($website)->getConfig('auction/general/delay_time');
            } else {
                $value = Mage::getStoreConfig('auction/general/delay_time', Mage::app()->getStore($store)->getId());
            }
        }
        if (!is_numeric($value) || $value <= 1) {
            $a = new Mage_Core_Model_Config();
            if (Mage::app()->getStore($store)->getId() > 0) {
                $a->saveConfig('auction/general/delay_time', 1, 'stores', Mage::app()->getStore($store)->getId());
            } else {
                if (Mage::app()->getWebsite($website)->getId() > 0) {
                    $a->saveConfig('auction/general/delay_time', 1, 'websites', Mage::app()->getWebsite($website)->getId());
                } else {
                    $a->saveConfig('auction/general/delay_time', 1, 'default', Mage::app()->getStore($store)->getId());
                }
            }
        }
    }

}
