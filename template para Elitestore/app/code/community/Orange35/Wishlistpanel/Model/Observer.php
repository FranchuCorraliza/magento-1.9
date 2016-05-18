<?php
class Orange35_Wishlistpanel_Model_Observer{
    public function customerLogin(Varien_Event_Observer $observer){
        if (!Mage::getStoreConfig('wishlistpanel_section/general_group/module_enabled')) {
            return;
        }
        $session = Mage::getSingleton('orange35_wishlistpanel/session');
        $wishlist = $session->getWishlist();
        $wishList = Mage::getModel('wishlist/wishlist')->loadByCustomer($observer->getCustomer(), true);
        foreach($wishlist as $key=>$item){
            $product = Mage::getModel('catalog/product')->load($key);
            $wishList->addNewItem($product, $item["buyRequest"]);
        }
        $session->setWishlist(array());
        $wishList->save();
    }
}

