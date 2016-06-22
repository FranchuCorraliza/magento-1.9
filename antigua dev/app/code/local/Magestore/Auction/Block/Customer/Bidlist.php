<?php

class Magestore_Auction_Block_Customer_Bidlist extends Mage_Core_Block_Template {

    public function _prepareLayout() {
        $return = parent::_prepareLayout();

        $type_biddername = Mage::getStoreConfig('auction/general/bidder_name_type');

        if ($type_biddername == '2') {//user create bidder name
            $customer = $this->getCustomer();

            if ($customer->getBidderName()) {
                $this->setTemplate('auction/customer/customerbid.phtml');
            } else {
                $this->setTemplate('auction/customer/form_biddername.phtml');
            }
        } else { //system created bidder name
            $this->setTemplate('auction/customer/customerbid.phtml');
        }
        return $return;
    }

    public function getCustomer() {
        if (!$this->hasData('customer')) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $this->setData('customer', $customer);
        }
        return $this->getData('customer');
    }

    public function getListCustomerbid() {
        if (!$this->hasData('listcustomerbid')) {
            $customerId = $this->getCustomer()->getId();

            $collection = Mage::getModel('auction/auction')
                    ->getListBidByCustomerId($customerId);

            // $curr_page = $this->getRequest()->getParam('page');
            // $curr_page = $curr_page ? $curr_page : 1;			
            // $collection->setPageSize(20);
            // $collection->setCurPage($this->getRequest()->getParam('page'));		

            $this->setData('listcustomerbid', $collection);
        }
        return $this->getData('listcustomerbid');
    }

    public function getNavHtml() {
        $auction_id = $this->getRequest()->getParam('id');
        $curr_page = $this->getRequest()->getParam('page');
        $curr_page = $curr_page ? $curr_page : 1;

        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();

        $collection = Mage::getModel('auction/auction')->getListBidByCustomerId($customerId);
        $collection->setPageSize(20);

        $last_page = $collection->getLastPageNumber();

        $html = ' ';

        if ($last_page > 1) {
            $html .= '<div class="auction-nav">' . $this->__('Pages') . ' ';

            for ($i = 1; $i <= $last_page; $i++) {
                if ($i != $curr_page)
                    $html .= '<a href="' . $this->getUrl('auction/index/customerbid', array('page' => $i)) . '" >' . $i . '</a>';
                else
                    $html .= '<span class="ative" >' . $i . '</span>';
            }

            $html .= '</div>';
        }
        return $html;
    }

}
