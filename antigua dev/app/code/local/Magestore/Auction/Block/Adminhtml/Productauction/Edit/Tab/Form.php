<?php

class Magestore_Auction_Block_Adminhtml_Productauction_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    public function __construct() {
        parent::__construct();
        //$this->setTemplate('auction/auction.phtml');
    }

    public function getProductauction() {
        if (!$this->hasData('productauction_data')) {
            $this->setData('productauction_data', Mage::registry('productauction_data'));
        }
        return $this->getData('productauction_data');
    }

    protected function _prepareForm() {

        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('auction_edit', array('legend' => Mage::helper('auction')->__('Auction information')));

        $image_calendar = Mage::getBaseUrl('skin') . 'adminhtml/default/default/images/grid-cal.gif';
        $data = $this->getProductauction();
//	   if(!$data['allow_buyout'] || $data['allow_buyout']<1){
//              $data['allow_buyout'] = '2';
//          }
        $disabled = false;
        if ($this->getRequest()->getParam('store'))
            $disabled = true;
        if ($data['status'] == 5 || $data['status'] == 6) {
            $disabled = true;
        }
        //$disabled = ($data['status'] == 5) ? true : $disabled;


        $fieldset->addField('product_name', 'text', array(
            'label' => Mage::helper('auction')->__('Product Name'),
            'class' => 'required-entry',
            'required' => true,
            'readonly' => 'readonly',
            'name' => 'product_name',
            'note' => '<a id="view_auction_product" target="_blank" href="' . $this->getUrl('adminhtml/catalog_product/edit', array('id' => $data->getProductId())) . '">' . $this->__('View product information.') . '</a>
							<input type="hidden" name="product_id" id="product_id" value="' . $data->getProductId() . '">',
        ));

        $fieldset->addField('init_price', 'text', array(
            'label' => Mage::helper('auction')->__('Starting Price'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'init_price',
            'disabled' => $disabled,
            'note' => Mage::helper('auction')->__('The price that a product is given at the beginning of an auction.'),
        ));

        $fieldset->addField('reserved_price', 'text', array(
            'label' => Mage::helper('auction')->__('Reserve Price'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'reserved_price',
            'disabled' => $disabled,
            'note' => Mage::helper('auction')->__('If the Closing Price is lower than the Reserve Price, there are no winning bidders.'),
        ));

        $fieldset->addField('min_interval_price', 'text', array(
            'label' => Mage::helper('auction')->__('Minimum Bid Increment'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'min_interval_price',
            'disabled' => $disabled,
            'note' => Mage::helper('auction')->__('The minimum amount that customer need to place higher than current bid.'),
        ));

        $fieldset->addField('max_interval_price', 'text', array(
            'label' => Mage::helper('auction')->__('Maximum Bid Increment'),
            'name' => 'max_interval_price',
            'disabled' => $disabled,
        ));

        $fieldset->addField('start_date', 'date', array(
            'label' => Mage::helper('auction')->__('Start Date'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'start_date',
            'format' => 'yyyy-MM-dd',
            'image' => $image_calendar,
            'disabled' => $disabled,
        ));

        $fieldset->addField('start_time', 'text', array(
            'label' => Mage::helper('auction')->__('Start Time'),
            'name' => 'start_time',
            'note' => 'Format H:i:s, example 12:30:00',
            'disabled' => $disabled,
            'note' => Mage::helper('auction')->__('24-hour time format: [hh]:[mm]:[ss]. For example: 14:30:00'),
        ));


        $fieldset->addField('end_date', 'date', array(
            'label' => Mage::helper('auction')->__('End Date'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'end_date',
            'format' => 'yyyy-MM-dd',
            'image' => $image_calendar,
            'disabled' => $disabled,
        ));

        $fieldset->addField('end_time', 'text', array(
            'label' => Mage::helper('auction')->__('End Time'),
            'name' => 'end_time',
            'note' => 'Format H:i:s, example 12:30:00',
            'disabled' => $disabled,
            'note' => Mage::helper('auction')->__('24-hour time format: [hh]:[mm]:[ss]. For example: 14:30:00'),
        ));


        $fieldset->addField('limit_time', 'text', array(
            'label' => Mage::helper('auction')->__('Extended Time'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'limit_time',
            'note' => 'second(s). Used in the "Going, going, gone" feature. Another bid placed within this Extended Time will make the time reset and countdown again.',
            'disabled' => $disabled,
        ));


        $fieldset->addField('multi_winner', 'text', array(
            'label' => Mage::helper('auction')->__('Multiple Winner'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'multi_winner',
            'note' => 'The number of customer(s) who bids the greatest price for the product sold.',
            'disabled' => $disabled,
        ));

        $fieldset->addField('allow_buyout', 'select', array(
            'label' => Mage::helper('auction')->__('Sell auctioned product normally'),
            'name' => 'allow_buyout',
            'values' => Mage::helper('auction')->getListBuyoutStatus(),
            'disabled' => $disabled,
            'note' => 'If Yes, all customers can buy a product at actual price without auctioning.',
        ));

        $fieldset->addField('day_to_buy', 'text', array(
            'label' => Mage::helper('auction')->__('Sell normally after'),
            'name' => 'day_to_buy',
            'note' => 'A given time period of day that the winner(s) can buy a product. After this time, the option may no longer be applied & other customers can buy this product.',
            'disabled' => $disabled,
        ));

        $fieldset->addField('featured', 'select', array(
            'label' => Mage::helper('auction')->__('Featured Auction'),
            'name' => 'featured',
            'values' => Mage::helper('auction')->getListFeaturedStatus(),
            'disabled' => $disabled,
        ));

        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('auction')->__('Status'),
            'name' => 'status',
            'values' => Mage::helper('auction')->getOptionStatus(),
            'disabled' => $disabled,
        ));

        if ($this->getRequest()->getParam('store')) {
            $fieldset->addField('is_applied', 'select', array(
                'label' => Mage::helper('auction')->__('Is Applied'),
                'name' => 'is_applied',
                'values' => array(
                    array('value' => 2, 'label' => $this->__('No')),
                    array('value' => 1, 'label' => $this->__('Yes')),
                ),
                'onchange' => 'changeIsApplyAuction();',
            ));
        }

        if (Mage::getSingleton('adminhtml/session')->getAuctionData()) {
            $data = Mage::getSingleton('adminhtml/session')->getAuctionData();
            Mage::getSingleton('adminhtml/session')->setAuctionData(null);
        } elseif (Mage::registry('productauction_data')) {
            $data = Mage::registry('productauction_data')->getData();
        }
        if ($data) {
            if (isset($data['product_id']) && $data['product_id']) {
                $product = Mage::getModel('catalog/product')->load($data['product_id']);
                $data['product_name'] = $product->getName();
            }
            $form->setValues($data);
        }

        return parent::_prepareForm();
    }

}
