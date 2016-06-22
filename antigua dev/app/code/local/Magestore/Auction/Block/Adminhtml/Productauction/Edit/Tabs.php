<?php

class Magestore_Auction_Block_Adminhtml_Productauction_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('auction_producttab');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('auction')->__('Auction Information'));
    }

    protected function _beforeToHtml() {
        $auction = $this->getProductauction();

        if (!$this->getRequest()->getParam('store'))
            if (!$auction || ($auction && ($auction->getStatus() <= 3 ))) {
                $this->addTab('form_listproduct', array(
                    'label' => Mage::helper('auction')->__('Select Product'),
                    'title' => Mage::helper('auction')->__('Select Product'),
                    'class' => 'ajax',
                    'url' => $this->getUrl('*/*/listproduct', array('_current' => true, 'id' => $this->getRequest()->getParam('id'))),
                ));
            }

        $this->addTab('form_product_auction', array(
            'label' => Mage::helper('auction')->__('Auction information'),
            'title' => Mage::helper('auction')->__('Auction information'),
            'content' => $this->getLayout()->createBlock('auction/adminhtml_productauction_edit_tab_form')->toHtml(),
        ));

        if (!$auction || ($auction && ($auction->getStatus() >= 4 ))) { //start
            $this->addTab('form_listbid', array(
                'label' => Mage::helper('auction')->__('Bids'),
                'title' => Mage::helper('auction')->__('Bids'),
                'class' => 'ajax',
                'url' => $this->getUrl('*/adminhtml_auction/listbid', array('id' => $this->getRequest()->getParam('id'))),
            ));
        }

        if (!$auction || ($auction && ($auction->getStatus() >= 4 ))) { //start
            $this->addTab('form_autobid', array(
                'label' => Mage::helper('auction')->__('Auto Bids'),
                'title' => Mage::helper('auction')->__('Auto Bids'),
                'class' => 'ajax',
                'url' => $this->getUrl('*/adminhtml_productauction/autobidlist', array('id' => $this->getRequest()->getParam('id'))),
            ));
        }

        if (!$auction || ($auction && ($auction->getStatus() >= 4 ))) { //start
            $this->addTab('form_watcherlist', array(
                'label' => Mage::helper('auction')->__('Watchers'),
                'title' => Mage::helper('auction')->__('Watchers'),
                'class' => 'ajax',
                'url' => $this->getUrl('*/adminhtml_productauction/watcherlist', array('id' => $this->getRequest()->getParam('id'))),
            ));
        }


        if ($auction && $auction->getStatus() == 5) { //auction end
            $this->addTab('form_winner', array(
                'label' => Mage::helper('auction')->__('Winner(s)'),
                'title' => Mage::helper('auction')->__('Winner(s)'),
                'class' => 'ajax',
                'url' => $this->getUrl('*/adminhtml_productauction/winnerlist', array('id' => $this->getRequest()->getParam('id'))),
            ));
        }

        return parent::_beforeToHtml();
    }

    public function getProductauction() {
        if (!$this->hasData('productauction_data')) {
            $this->setData('productauction_data', Mage::registry('productauction_data'));
        }
        return $this->getData('productauction_data');
    }

}
