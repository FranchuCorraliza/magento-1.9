<?php
class Orange35_Wishlistpanel_Block_Wishlistpanel extends Mage_Core_Block_Template{

    public function _construct(){
        if (!Mage::getStoreConfig('wishlistpanel_section/general_group/module_enabled')){
            parent::_construct();
        }
        else{
            return;
        }
    }

    public function getItemCount(){
        //if(Mage::getSingleton('customer/session')->isLoggedIn()){

            return(Mage::helper("orange35_wishlistpanel")->getItemCount());
        //}
    }

    public function getWishListItems() {
        if(Mage::getSingleton('customer/session')->isLoggedIn()){
            $_itemCollection = Mage::helper('orange35_wishlistpanel')->getWishlistItemCollection()->setPageSize(1000);
            $_itemsInWishList = array();

            foreach ($_itemCollection as $_item) {
                $_product = $_item->getProduct();

                $_itemsInWishList[$_product->getId()] = $_product;
            }

            return $_itemsInWishList;
        }
    }
}
