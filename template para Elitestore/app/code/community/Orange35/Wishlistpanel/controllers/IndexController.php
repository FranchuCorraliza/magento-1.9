<?php
require_once 'Mage/Wishlist/controllers/IndexController.php';


class Orange35_Wishlistpanel_IndexController extends Mage_Wishlist_IndexController {
    protected $_skipAuthentication = true;

    public function _construct(){
        if (!Mage::getStoreConfig('wishlistpanel_section/general_group/module_enabled')) {
            $this->_skipAuthentication = false;
        }
    }

    public function indexAction(){
        if (!Mage::getStoreConfig('wishlistpanel_section/general_group/module_enabled')) {
            parent::indexAction();
            return;
        }
        if(Mage::getSingleton('customer/session')->isLoggedIn()){
            parent::indexAction();
        }
        else{
            Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getUrl('wishlistpanel'));
            Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('customer/account/login'));
        }
    }

    public function getwishlistAction(){
        $data = array();
        if(Mage::getSingleton('customer/session')->isLoggedIn()){
            $data["wishlist_count"] = Mage::helper("orange35_wishlistpanel")->getItemCount();
            $data["wishlist_items"] = array();
            foreach(Mage::helper("orange35_wishlistpanel")->getWishlistItemCollection() as $item){
                $product = $item->getProduct();
                $data["wishlist_items"][] = array("id"=>$item->getId(), "productId"=>$product->getId(), "productSku"=>$product->getSku(), "productName"=>$product->getName(), "productImage"=>(String)Mage::helper('catalog/image')->init($product, 'small_image')->resize(170), "productUrl"=>$product->getProductUrl(), "removeUrl"=>Mage::helper("orange35_wishlistpanel")->getRemoveUrlAjax($item), "productBrand"=>$product->getAttributeText('manufacturer'), "productPrice"=> round($product->getFinalPrice()) . Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol());
            }
        }
        else{
            $session = Mage::getSingleton('orange35_wishlistpanel/session');
            $wishlist = $session->getWishlistToJson();
            $data["success"] = true;
            $data["wishlist_items"] = $wishlist;
            $data["wishlist_count"] = count($wishlist);
        }
        echo(json_encode($data));
        exit;
    }

    public function addAction(){
        if(!Mage::getSingleton('customer/session')->isLoggedIn()){
            Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('customer/account'));
        }
        else{
            $this->_skipAuthentication = false;
            parent::addAction();
            return;
        }
    }

    public function addAjaxAction()
    {
        if (!Mage::getStoreConfig('wishlistpanel_section/general_group/module_enabled')) {
            parent::addAction();
            return;
        }
        $data = array();

        if(method_exists('Mage',"getEdition")&&(Mage::getEdition() == "Community")&&( (int)str_replace(".", "", Mage::getVersion())>1800 )&&!$this->_validateFormKey()){
            $data["success"] = false;
            $data["messages"] = array();
            $data["messages"][] = $this->__("Session Expired. Please reload the page and make sure cookies are enabled.");
        }
        else{
            if(Mage::getSingleton('customer/session')->isLoggedIn()){
                $data = $this->_addItemToWishListAuth();
                $data["wishlist_count"] = Mage::helper("orange35_wishlistpanel")->getItemCount();
                $data["wishlist_items"] = array();
                foreach(Mage::helper("orange35_wishlistpanel")->getWishlistItemCollection() as $item){
                    $product = $item->getProduct();
                    $data["wishlist_items"][] = array("id"=>$item->getId(), "productId"=>$product->getId(), "productSku"=>$product->getSku(), "productName"=>$product->getName(), "productImage"=>(String)Mage::helper('catalog/image')->init($product, 'small_image')->resize(170), "productUrl"=>$product->getProductUrl(), "removeUrl"=>Mage::helper("orange35_wishlistpanel")->getRemoveUrlAjax($item), "productBrand"=>$product->getAttributeText('manufacturer'), "productPrice"=> round($product->getFinalPrice()) . Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol());

                }
            }
            else{
                $wishlist = $this->_addItemToWishListNoAuth();
                $data["success"] = true;
                $data["wishlist_items"] = $wishlist;
                $data["wishlist_count"] = count($wishlist);
            }
        }
        echo(json_encode($data));
        exit;
    }

    protected function _addItemToWishListNoAuth(){
        $session = Mage::getSingleton('orange35_wishlistpanel/session');
        $productId = (int)$this->getRequest()->getParam('product');
        if (!$productId) {
            $data["success"] = false;
            $data["messages"] = array();
            $data["messages"][] = "Unexpected Error";
            return $data;
        }

        $product = Mage::getModel('catalog/product')->load($productId);
        if (!$product->getId() || !$product->isVisibleInCatalog()) {
            $data["success"] = false;
            $data["messages"] = array();
            $data["messages"][] = "Cannot specify product.";
            return $data;
        }

        $requestParams = $this->getRequest()->getParams();
        $buyRequest = new Varien_Object($requestParams);
        $session->addProductToWishlist($product, $buyRequest);
        return $session->getWishlistToJson();
    }

    protected function _addItemToWishListAuth()
    {
        if (!Mage::getStoreConfig('wishlistpanel_section/general_group/module_enabled')) {
            parent::_addItemToWishList();
            return;
        }
        $data = array();
        $wishlist = $this->_getWishlist();
        if (!$wishlist) {
            return $this->norouteAction();
        }

        $session = Mage::getSingleton('customer/session');

        $productId = (int)$this->getRequest()->getParam('product');
        if (!$productId) {
            $data["success"] = false;
            $data["messages"] = array();
            $data["messages"][] = "Unexpected Error";
            return $data;
        }

        $product = Mage::getModel('catalog/product')->load($productId);
        if (!$product->getId() || !$product->isVisibleInCatalog()) {
            $data["success"] = false;
            $data["messages"] = array();
            $data["messages"][] = "Cannot specify product.";
            return $data;
        }

        try {
            $requestParams = $this->getRequest()->getParams();
            if ($session->getBeforeWishlistRequest()) {
                $requestParams = $session->getBeforeWishlistRequest();
                $session->unsBeforeWishlistRequest();
            }
            $buyRequest = new Varien_Object($requestParams);

            $result = $wishlist->addNewItem($product, $buyRequest);
            if (is_string($result)) {
                Mage::throwException($result);
            }
            $wishlist->save();

            Mage::dispatchEvent(
                'wishlist_add_product',
                array(
                    'wishlist' => $wishlist,
                    'product' => $product,
                    'item' => $result
                )
            );

            $referer = $session->getBeforeWishlistUrl();
            if ($referer) {
                $session->setBeforeWishlistUrl(null);
            } else {
                $referer = $this->_getRefererUrl();
            }

            /**
             *  Set referer to avoid referring to the compare popup window
             */
            $session->setAddActionReferer($referer);

            Mage::helper('wishlist')->calculate();

            $message = $this->__('%1$s has been added to your wishlist. Click <a href="%2$s">here</a> to continue shopping.',
                $product->getName(), Mage::helper('core')->escapeUrl($referer));
            //TODO $session->addSuccess($message);
        } catch (Mage_Core_Exception $e) {
            $data["success"] = false;
            $data["messages"] = array();
            $data["messages"][] = $this->__('An error occurred while adding item to wishlist: %s', $e->getMessage());
            return $data;
            //$session->addError($this->__('An error occurred while adding item to wishlist: %s', $e->getMessage()));
        }
        catch (Exception $e) {
            //$session->addError($this->__('An error occurred while adding item to wishlist.'));
            $data["success"] = false;
            $data["messages"] = array();
            $data["messages"][] = $this->__($this->__('An error occurred while adding item to wishlist.'));
            return $data;
        }

        $data["success"] = true;
        /*$data["product"] = array();
        $data["product"]["id"] = $product->getId();
        $data["product"]["name"] = $product->getName();
        $data["product"]["image"] = Mage::helper('catalog/image')->init($product, 'small_image')->resize(135);*/
        return $data;
        //$this->_redirect('*', array('wishlist_id' => $wishlist->getId()));
    }

    public function panelAllCartAction(){
        if(!Mage::getSingleton('customer/session')->isLoggedIn()){
            $session = Mage::getSingleton('orange35_wishlistpanel/session');
            $wishlist = $session->getWishlist();
            $messages = array();
            foreach($wishlist as $item){
                $_product = Mage::getModel('catalog/product')->load($item["productId"]);
                $cart = Mage::getModel('checkout/cart');
                $cart->init();
                $added = true;
                try{
                   $cart->addProduct($_product, $item["buyRequest"]);
                }
                catch (Exception $e) {
                    $messages[] = $this->__('%s for "%s".', trim($e->getMessage(), '.'), $_product->getName());
                    $added = false;
                }
                if($added){
                    unset($wishlist[$_product->getId()]);
                    $session->setWishlist($wishlist);
                }
            }
            try{
                $cart->save();
                Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
            }
            catch(Exception $e){
                $messages[] = $this->__("Session Expired. Please reload the page and make sure cookies are enabled.");
                $added = false;
            }
            foreach ($messages as $message) {
                Mage::getSingleton('checkout/session')->addError($message);
            }
            $this->_redirectUrl(Mage::helper('checkout/cart')->getCartUrl());
        }
        else{
            $wishlist   = $this->_getWishlist();
            if (!$wishlist) {
                $this->_forward('noRoute');
                return;
            }
            $isOwner    = $wishlist->isOwner(Mage::getSingleton('customer/session')->getCustomerId());

            $messages   = array();
            $addedItems = array();
            $notSalable = array();
            $hasOptions = array();

            $cart       = Mage::getSingleton('checkout/cart');
            $collection = $wishlist->getItemCollection()
                ->setVisibilityFilter();

            $qtysString = $this->getRequest()->getParam('qty');
            $qtys =  array_filter(json_decode($qtysString), 'strlen');

            foreach ($collection as $item) {
                /** @var Mage_Wishlist_Model_Item */
                try {
                    $disableAddToCart = $item->getProduct()->getDisableAddToCart();
                    $item->unsProduct();

                    // Set qty
                    if (isset($qtys[$item->getId()])) {
                        $qty = $this->_processLocalizedQty($qtys[$item->getId()]);
                        if ($qty) {
                            $item->setQty($qty);
                        }
                    }
                    $item->getProduct()->setDisableAddToCart($disableAddToCart);
                    // Add to cart
                    if ($item->addToCart($cart, $isOwner)) {
                        $addedItems[] = $item->getProduct();
                    }

                } catch (Mage_Core_Exception $e) {
                    if ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_NOT_SALABLE) {
                        $notSalable[] = $item;
                    } else if ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_HAS_REQUIRED_OPTIONS) {
                        $hasOptions[] = $item;
                    } else {
                        $messages[] = $this->__('%s for "%s".', trim($e->getMessage(), '.'), $item->getProduct()->getName());
                    }

                    $cartItem = $cart->getQuote()->getItemByProduct($item->getProduct());
                    if ($cartItem) {
                        $cart->getQuote()->deleteItem($cartItem);
                    }
                } catch (Exception $e) {
                    Mage::logException($e);
                    $messages[] = Mage::helper('wishlist')->__('Cannot add the item to shopping cart.');
                }
            }

            if ($isOwner) {
                $indexUrl = Mage::helper('wishlist')->getListUrl($wishlist->getId());
            } else {
                $indexUrl = Mage::getUrl('wishlist/shared', array('code' => $wishlist->getSharingCode()));
            }
            if (Mage::helper('checkout/cart')->getShouldRedirectToCart()) {
                $redirectUrl = Mage::helper('checkout/cart')->getCartUrl();
            } else if ($this->_getRefererUrl()) {
                $redirectUrl = $this->_getRefererUrl();
            } else {
                $redirectUrl = $indexUrl;
            }

            if ($notSalable) {
                $products = array();
                foreach ($notSalable as $item) {
                    $products[] = '"' . $item->getProduct()->getName() . '"';
                }
                $messages[] = Mage::helper('wishlist')->__('Unable to add the following product(s) to shopping cart: %s.', join(', ', $products));
            }

            if ($hasOptions) {
                $products = array();
                foreach ($hasOptions as $item) {
                    $products[] = '"' . $item->getProduct()->getName() . '"';
                }
                $messages[] = Mage::helper('wishlist')->__('Product(s) %s have required options. Each of them can be added to cart separately only.', join(', ', $products));
            }

            if ($messages) {
                $isMessageSole = (count($messages) == 1);
                if ($isMessageSole && count($hasOptions) == 1) {
                    $item = $hasOptions[0];
                    if ($isOwner) {
                        $item->delete();
                    }
                    $redirectUrl = $item->getProductUrl();
                } else {
                    $wishlistSession = Mage::getSingleton('wishlist/session');
                    foreach ($messages as $message) {
                        $wishlistSession->addError($message);
                    }
                    $redirectUrl = $indexUrl;
                }
            }

            if ($addedItems) {
                // save wishlist model for setting date of last update
                try {
                    $wishlist->save();
                }
                catch (Exception $e) {
                    Mage::getSingleton('wishlist/session')->addError($this->__('Cannot update wishlist'));
                    $redirectUrl = $indexUrl;
                }

                $products = array();
                foreach ($addedItems as $product) {
                    $products[] = '"' . $product->getName() . '"';
                }

                Mage::getSingleton('checkout/session')->addSuccess(
                    Mage::helper('wishlist')->__('%d product(s) have been added to shopping cart: %s.', count($addedItems), join(', ', $products))
                );

                // save cart and collect totals
                $cart->save()->getQuote()->collectTotals();
            }

            Mage::helper('wishlist')->calculate();

            $this->_redirectUrl($redirectUrl);
        }
    }

    public function emptyAjaxAction(){
        if(!Mage::getSingleton('customer/session')->isLoggedIn()){
            $session = Mage::getSingleton('orange35_wishlistpanel/session');
            $session ->setWishlist(array());
            $wishlist = $session->getWishlistToJson();
            $data["wishlist_items"] = $wishlist;
            $data["wishlist_count"] = count($wishlist);
        }
        else{
            foreach(Mage::helper("orange35_wishlistpanel")->getWishlistItemCollection() as $item){
                $item->delete();
            }
            $data["wishlist_count"] = Mage::helper("orange35_wishlistpanel")->getItemCount();
            $data["wishlist_items"] = array();
            /*foreach(Mage::helper("orange35_wishlistpanel")->getWishlistItemCollection() as $item){
                $product = $item->getProduct();
                $data["wishlist_items"][] = array("id"=>$item->getId(), "productId"=>$product->getId(), "productName"=>(strlen($product->getName()) > 25) ? substr($product->getName(),0,22).'...' : $product->getName(), "productImage"=>(String)Mage::helper('catalog/image')->init($product, 'small_image')->resize(170), "productUrl"=>$product->getProductUrl(), "removeUrl"=>Mage::helper("orange35_wishlistpanel")->getRemoveUrlAjax($item));
            }*/
        }

        echo(json_encode($data));
        exit;
    }

    public function removeAjaxAction(){
        if(!Mage::getSingleton('customer/session')->isLoggedIn()){
			$wishlist = $this->_removeAjax();
            $data["wishlist_items"] = $wishlist;
            $data["wishlist_count"] = count($wishlist);
        }
        else{
			$data = $this->_removeAjax();
            $data["wishlist_count"] = Mage::helper("orange35_wishlistpanel")->getItemCount();
            $data["wishlist_items"] = array();
            foreach(Mage::helper("orange35_wishlistpanel")->getWishlistItemCollection() as $item){
                $product = $item->getProduct();
                $data["wishlist_items"][] = array("id"=>$item->getId(), "productId"=>$product->getId(), "productSku"=>$product->getSku(), "productName"=>$product->getName(), "productImage"=>(String)Mage::helper('catalog/image')->init($product, 'small_image')->resize(170), "productUrl"=>$product->getProductUrl(), "removeUrl"=>Mage::helper("orange35_wishlistpanel")->getRemoveUrlAjax($item),"productBrand"=>$product->getAttributeText('manufacturer'), "productPrice"=> round($product->getFinalPrice()) . Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol());
            }
        }
        $data["success"] = true;
        echo(json_encode($data));
        exit;
    }

    public function _removeAjax()
    {

        $data = array();
        $id = (int) $this->getRequest()->getParam('item');
        if(!Mage::getSingleton('customer/session')->isLoggedIn()){
            $session = Mage::getSingleton('orange35_wishlistpanel/session');
            $wishlist = $session->getWishlist();
            unset($wishlist[$id]);
            $session->setWishlist($wishlist);
            $wishlist = $session->getWishlistToJson();
            return($wishlist);
        }
        $item = Mage::getModel('wishlist/item')->load($id);
        if (!$item->getId()) {
            //return $this->norouteAction();
            $data["success"] = false;
            $data["messages"] = array();
            $data["messages"][] = "Item Not Found";
            return $data;
        }

        $wishlist = $this->_getWishlist($item->getWishlistId());
        if (!$wishlist) {
            $data["success"] = false;
            $data["messages"] = array();
            $data["messages"][] = "Wishlist Not Found";
            return $data;
        }
        try {
            $item->delete();
            $wishlist->save();

        } catch (Mage_Core_Exception $e) {
            $data["success"] = false;
            $data["messages"] = array();
            $data["messages"][] = 'An error occurred while deleting the item from wishlist: '.$e->getMessage();
            return $data;
            /*Mage::getSingleton('customer/session')->addError(
                $this->__('An error occurred while deleting the item from wishlist: %s', $e->getMessage())
            );*/
        } catch (Exception $e) {
            $data["success"] = false;
            $data["messages"] = array();
            $data["messages"][] = 'An error occurred while deleting the item from wishlist.';
            return $data;
            /*Mage::getSingleton('customer/session')->addError(
                $this->__('An error occurred while deleting the item from wishlist.')
            );*/
        }

        Mage::helper('wishlist')->calculate();
        $data["success"] = true;
        $data["product"] = array();
        $data["product"]["id"] = $item->getId();
        $data["product"]["name"] = $item->getName();
        //$data["product"]["image"] = Mage::helper('catalog/image')->init($product, 'small_image')->resize(135);
        return $data;
        //$this->_redirectReferer(Mage::getUrl('*/*'));
    }
}