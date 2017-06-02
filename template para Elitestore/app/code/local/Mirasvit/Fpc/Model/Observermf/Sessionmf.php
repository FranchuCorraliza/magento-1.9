<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Full Page Cache
 * @version   1.0.32
 * @build     662
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



/**
 * The observer responsible for the customer actions
 * which change page state (cache id).
 */
class Mirasvit_Fpc_Model_Observermf_Sessionmf
{
    /**
     * @var Mirasvit_Fpc_Helper_Fpcmf_Sessionmf
     */
    protected $_session = null;

    public function __construct()
    {
        $this->_session = new Mirasvit_Fpc_Helper_Fpcmf_Sessionmf();

        return $this;
    }

    /**
     * @return void
     */
    public function onSalesQuoteSave()
    {
        $hash = array();
        $checkout = Mage::getSingleton('checkout/session');
        foreach ($checkout->getQuote()->getAllItems() as $item) {
            $hash[] = $item->getProductId().'/'.$item->getQty();
        }

        $this->_session->set('cart', $hash);
        $this->_session->set('cart_changed', true);

        return $this;
    }

    /**
     * @return void
     */
    public function onCustomerLogin()
    {
        $customer = Mage::getSingleton('customer/session');

        $this->_session->set('customer_id', $customer->getCustomerId());
        $this->_session->set('customer_group_id', $customer->getCustomerGroupId());
        // $customerName = Mage::helper('core')->__('Welcome Dear %s', Mage::helper('core')->escapeHtml(Mage::getSingleton('customer/session')->getCustomer()->getFirstname()).' '
        //         .Mage::helper('core')->escapeHtml(Mage::getSingleton('customer/session')->getCustomer()->getLastname()));

        // $this->_session->set('customer_name_data', $customerName);

        return $this;
    }

    /**
     * @return void
     */
    public function onCustomerLogout()
    {
        $this->_session->set('customer_id', false);

        return $this;
    }

    /**
     * @return void
     */
    public function onCatalogCompareItemSave()
    {
        $items = Mage::helper('catalog/product_compare')->getItemCollection();
        foreach ($items as $item) {
            $hash[] = $item->getId();
        }

        $this->_session->set('catalog_compare', $hash);

        return $this;
    }

    /**
     * @return void
     */
    public function onWishlistSave()
    {
        $wishlistHelper = Mage::helper('wishlist');

        if ($wishlistHelper->hasItems()) {
            $items = $wishlistHelper->getItemCollection();
            foreach ($items as $item) {
                $hash[] = $item->getId();
            }
        }

        $this->_session->set('wishlist', $hash);

        return $this;
    }

    /**
     * @return void
     */
    public function onAddMessage()
    {
        $this->_session->set('message', 1);

        return $this;
    }

    /**
     * @return void
     */
    public function onClearMessage()
    {
        $this->_session->set('message', 0);

        return $this;
    }

    /**
     * @return void
     */
    public function onHttpResponseSendBefore()
    {
        $this->_session->set('store_code', Mage::app()->getStore()->getCode());
        $this->_session->set('currency_code', Mage::app()->getStore()->getCurrentCurrencyCode());

        return $this;
    }
}
