<?php
class Orange35_Wishlistpanel_Model_Session extends Mage_Core_Model_Session_Abstract{
    public function __construct()
    {
        $this->init('orange35_wishlistpanel');
        //$this->setWishlist(array());
    }

    public function addProductToWishlist($product, $buyRequest = false){
        //$this->setWishlist(array());
        $wishlist = $this->getWishlist();
        if(!is_array($wishlist)){
            $wishlist = array();
        }
		$sizeId=$buyRequest->getData()['super_attribute'][133];
		$attr = $product->getResource()->getAttribute('talla');
		if ($attr->usesSource()) {
			$productSize = Mage::helper("orange35_wishlistpanel")->__("Size: ") . $attr->getSource()->getOptionText($sizeId). " " . $product->getData("tallaje");
		}
		$addToCartUrl=Mage::getUrl('checkout/cart/add', array('product'=>$product->getId(),'qty'=>1, 'form_key' => Mage::getSingleton('core/session')->getFormKey())). '?super_attribute[133]='.$sizeId;
        $wishlist[$product->getId()] = array(
            "productId"=>$product->getId(),
            "productSku"=>$product->getSku(),
            "buyRequest"=>$buyRequest,
            "productName"=>$product->getName(),
            "productImage"=>(String)Mage::helper('catalog/image')->init($product, 'small_image')->resize(170),
            "productUrl"=>$product->getProductUrl(),
			"addToCartUrl"=>$addToCartUrl,
            "removeUrl"=>Mage::helper("orange35_wishlistpanel")->getRemoveUrlSession($product),
			"productBrand"=>$product->getAttributeText('manufacturer'),
			"productSize"=>$productSize,
            "productPrice"=>round($product->getFinalPrice()) . Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol()
        );
        $this->setWishlist($wishlist);
    }

    public function getWishlistToJson(){
        $wishlist = array();
        $sessionWishlist = $this->getWishlist();
        if(is_array($sessionWishlist)){
            foreach($sessionWishlist as $key=>$value){
                if(Mage::app()->getStore()->isCurrentlySecure()){
                    $value["productImage"] = str_replace("http:", "https:", $value["productImage"]);
                    $value["removeUrl"] = str_replace("http:", "https:", $value["removeUrl"]);
                    $value["productUrl"] = str_replace("http:", "https:", $value["productUrl"]);
                }
                else{
                    $value["productImage"] = str_replace("https:", "http:", $value["productImage"]);
                    $value["removeUrl"] = str_replace("https:", "http:", $value["removeUrl"]);
                    $value["productUrl"] = str_replace("https:", "http:", $value["productUrl"]);
                }
                $wishlist[] = $value;
            }
        }
        return $wishlist;
    }
}