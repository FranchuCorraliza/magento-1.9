<?php

class Magestore_Auction_Block_Adminhtml_Productauction_Edit_Bidtabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('auction_producttab');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('auction')->__('Auction Information'));
    }

    protected function _beforeToHtml() {
        $this->addTab('form_listbid', array(
            'label' => Mage::helper('auction')->__('List Bids'),
            'title' => Mage::helper('auction')->__('List Bids'),
            'class' => 'ajax',
            'url' => $this->getUrl('*/adminhtml_auction/listbid', array('id' => $this->getRequest()->getParam('id'))),
        ));

        $this->addTab('form_product_auction', array(
            'label' => Mage::helper('auction')->__('ProductAuction information'),
            'title' => Mage::helper('auction')->__('ProductAuction information'),
            'content' => $this->getLayout()->createBlock('auction/productauction')->setTemplate('auction/productauction.phtml')->toHtml(),
        ));

        if (Mage::helper('auction')->getAuctionStatus($this->getRequest()->getParam('id')) == 5) { //auction end
            $this->addTab('form_listauctionbid', array(
                'label' => Mage::helper('auction')->__('Bid Winner'),
                'title' => Mage::helper('auction')->__('Bid Winner'),
                'class' => 'ajax',
                'url' => $this->getUrl('*/adminhtml_productauction/winnerlist', array('id' => $this->getRequest()->getParam('id'))),
            ));
        }

        return parent::_beforeToHtml();
    }

}
