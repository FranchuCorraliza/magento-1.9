<?php
class Orange35_Wishlistpanel_Helper_Data extends Mage_Wishlist_Helper_Data {
    public function getRemoveUrlAjax($item)
    {
        return $this->_getUrl('wishlist/index/removeAjax',
            array('item' => $item->getWishlistItemId())
			//array('item' => $item->getProductId())
        );
    }
    public function getRemoveUrlSession($product)
    {
        return Mage::getUrl('wishlist/index/removeAjax',
            array('item' => $product->getId())
        );
    }
    public function checkProductInWishlist($productId){
        if(Mage::getSingleton('customer/session')->isLoggedIn()){
            $_productCollection = Mage::helper('wishlist')->getProductCollection()
                ->addFieldToFilter('id', $productId);

            if($_productCollection->count()) {
                return true;
            }
        }
        else{
            $session = Mage::getSingleton('orange35_wishlistpanel/session');
        }
    }
}