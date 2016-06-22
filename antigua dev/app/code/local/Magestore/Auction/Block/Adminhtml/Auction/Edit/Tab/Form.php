<?php

class Magestore_Auction_Block_Adminhtml_Auction_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    public function __construct() {
        $this->setTemplate('auction/auction.phtml');
    }

    public function getCustomer() {
        $auction = $this->getAuctionbid();

        $customer_id = $auction->getCustomerId();

        if ($customer_id) {
            return Mage::getModel('customer/customer')->load($customer_id);
        }
        return;
    }

    public function getAuctionbid() {
        if (!$this->hasData('auction_data')) {
            $this->setData('auction_data', Mage::registry('auction_data'));
        }
        return $this->getData('auction_data');
    }

    public function getProduct() {
        $auction = $this->getAuctionbid();

        $product_id = $auction->getProductId();

        if ($product_id) {
            return Mage::getModel('catalog/product')->load($product_id);
        }

        return;
    }

    public function getProductUrl($product_id) {
        return $this->getUrl('adminhtml/catalog_product/edit', array('id' => $product_id));
    }

    public function getCustomerUrl($customer_id) {
        return $this->getUrl('adminhtml/customer/edit', array('id' => $customer_id));
    }

    public function getProductauction() {
        $productauctionId = $this->getAuctionbid()->getProductauctionId();

        if ($productauctionId) {
            return Mage::getModel('auction/productauction')->load($productauctionId);
        }
    }

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('auction_form', array('legend' => Mage::helper('auction')->__('Item information')));

        $fieldset->addField('product_name', 'text', array(
            'label' => Mage::helper('auction')->__('Product'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'product_name',
            'readonly' => 'readonly',
        ));

        $fieldset->addField('customer_name', 'text', array(
            'label' => Mage::helper('auction')->__('Customer Name'),
            'required' => true,
            'name' => 'customer_name',
            'readonly' => 'readonly',
        ));

        $fieldset->addField('price', 'text', array(
            'label' => Mage::helper('auction')->__('Price'),
            'required' => true,
            'name' => 'price',
            'readonly' => 'readonly',
        ));

        $fieldset->addField('created_date', 'text', array(
            'label' => Mage::helper('auction')->__('Date'),
            'required' => true,
            'name' => 'created_date',
            'readonly' => 'readonly',
        ));

        $fieldset->addField('created_time', 'text', array(
            'label' => Mage::helper('auction')->__('Time'),
            'required' => true,
            'name' => 'created_time',
            'readonly' => 'readonly',
        ));
        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('auction')->__('Status'),
            'name' => 'status',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('auction')->__('Enabled'),
                ),
                array(
                    'value' => 2,
                    'label' => Mage::helper('auction')->__('Disabled'),
                ),
            ),
        ));


        if (Mage::getSingleton('adminhtml/session')->getAuctionData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getAuctionData());
            Mage::getSingleton('adminhtml/session')->setAuctionData(null);
        } elseif (Mage::registry('auction_data')) {
            $form->setValues(Mage::registry('auction_data')->getData());
        }
        return parent::_prepareForm();
    }

}
