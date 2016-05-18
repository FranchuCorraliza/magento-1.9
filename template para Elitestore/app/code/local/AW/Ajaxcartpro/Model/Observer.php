<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Ajaxcartpro
 * @version    2.5.1
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */

class AW_Ajaxcartpro_Model_Observer {
    /**
     * Return product URL according to it type
     * @param Mage_Catalog_Model_Product $product
     * @return String
     */

    public function recalcQuote()
    {
        $cart=Mage::getModel('checkout/cart');
        $cart->getQuote();
        $cart->getQuote()->setTotalsCollectedFlag(false);
        $cart->save();
    }

    protected function _getProductUrl(Mage_Catalog_Model_Product $product) {
        $query = array();
        if($product->getHasOptions()) {
            $query = array('options' => 'cart');
        }
        return $product->getUrlModel()->getUrl($product, array('_query' => $query, '_secure' => Mage::app()->getRequest()->isSecure()));
    }

    public function addToCartEvent($observer){
        $request = Mage::app()->getFrontController()->getRequest();
        if ( !$request->getParam('in_cart') && !$request->getParam('is_checkout')
            && $request->getParam('awacp') ) {
            /* Checking product Qty */
            $_product = $observer->getData('product');
            $_quote = Mage::getSingleton('checkout/session')->getQuote();
            $_helper = Mage::helper('ajaxcartpro');
            $_quoteItem = null;
            $_qtyPassed = true;
            foreach($_quote->getItemsCollection() as $_qa)
                if($_qa->getProduct()->getId() == $_product->getId())
                    $_quoteItem = $_qa;
            if($_quoteItem)
                $_qtyPassed = $_product->getStockItem()->checkQty($_quoteItem->getQty());
            Mage::getSingleton('checkout/session')->setNoCartRedirect(true);
            $_response = Mage::getModel('ajaxcartpro/response')
                ->setCart($_helper->renderCart())
                ->setLinks($_helper->renderTopCartLinkTitle())
                ->setProductName($observer->getProduct()->getName())
                ->setQ($_product->getStockItem()->getQty());
            if(!Mage::registry('wishlist') && Mage::getSingleton('customer/session')->getCustomer()->getId()) {
                $wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer(), true);
                Mage::register('wishlist', $wishlist); 
            }
            if(Mage::registry('wishlist')) {
                $_response->setWishlist(Mage::helper('ajaxcartpro')->renderWishlist())
                    ->setWishlistSidebar(Mage::helper('ajaxcartpro')->renderWishlistSidebar())
                    ->setWishlistLinks(Mage::helper('ajaxcartpro')->renderWishlistTopLinks());
            }
                
            if(!$_qtyPassed && !$_product->isGrouped() && !$_product->isConfigurable() && !$_helper->isProductBundle($_product))
                $_response->setError($_helper->__('Wrong Qty'));
            $_response->send();
        }
        if ( $request->getParam('is_checkout')	) {
            Mage::getSingleton('checkout/session')->setNoCartRedirect(true);

            $_response = Mage::getModel('ajaxcartpro/response')
                ->setCart(Mage::helper('ajaxcartpro')->renderBigCart())
                ->setLinks(Mage::helper('ajaxcartpro')->renderTopCartLinkTitle())
                ->setProductName($observer->getProduct()->getName())
            ;
            if (AW_Ajaxcartpro_Helper_Data::RETURN_CARTBAR_ON_BIGCART)
                $_response->setCartbar(Mage::helper('ajaxcartpro')->renderCart());
            $_response->send();
        }
    }

    public function addCustomOptions($observer) {
        $params = $observer->getControllerAction()->getRequest()->getParams();
        if (!isset($params['options']) || $params['options'] != 'cart' || !isset($params['ajaxcartpro'])) {
            return;
        }
        
        $product = Mage::registry('current_product');

        /* If product type is not simple, downloadable or virtual -- return false (will move to product page) */

        if (
                !$product->isConfigurable()
                && $product->getTypeId() != 'simple'
                && $product->getTypeId() != 'downloadable'
                && $product->getTypeId() != 'virtual'
        ) {
            echo 'false';
            die;
        }

        /* If product have custom option of file type -- return false (will move to product page) */
        if (Mage::helper('ajaxcartpro')->hasFileOption()) {echo 'false'; die;}
        $block = Mage::getSingleton('core/layout');
        $options = $block->createBlock('catalog/product_view_options', 'product_options')
            ->setTemplate('catalog/product/view/options.phtml')
            ->addOptionRenderer('text', 'catalog/product_view_options_type_text', 'catalog/product/view/options/type/text.phtml')
            ->addOptionRenderer('select', 'catalog/product_view_options_type_select', 'catalog/product/view/options/type/select.phtml')
            ->addOptionRenderer('date', 'catalog/product_view_options_type_date', 'catalog/product/view/options/type/date.phtml');
        $price = $block->createBlock('catalog/product_view', 'product_price')
            ->setTemplate('catalog/product/view/price_clone.phtml');
        $js = $block->createBlock('core/template', 'product_js')
            ->setTemplate('catalog/product/view/options/js.phtml');

        if ($product->isConfigurable()) {
            $configurable = $block->createBlock('catalog/product_view_type_configurable', 'product_configurable_options')
                ->setTemplate('ajaxcartpro/options/configurable.phtml');
            $configurableData = $block->createBlock('catalog/product_view_type_configurable', 'product_type_data')
                ->setTemplate('catalog/product/view/type/configurable.phtml');
        }
        if ($product->getTypeId() == 'downloadable') {
            $downloadable = $block->createBlock('downloadable/catalog_product_links', 'product_downloadable_options')
                ->setTemplate('ajaxcartpro/options/downloadable.phtml');
            $downloadableData = $block->createBlock('downloadable/catalog_product_view_type', 'product_type_data')
                ->setTemplate('downloadable/catalog/product/type.phtml');
        }
        $main = $block->createBlock('catalog/product_view')
            ->setTemplate('ajaxcartpro/options.phtml')
            ->append($options);

        if ($product->isConfigurable()) {
            $main->append($configurableData);
            $main->append($configurable);
        }
        if ($product->getTypeId() == 'downloadable') {
            $main->append($downloadableData);
            $main->append($downloadable);
        }

        $main->append($js)->append($price);

        $observer->getControllerAction()->getResponse()->setBody($main->renderView());
    }

    public function addToCartFromWishlist($observer)
    {
        if (preg_match('/^1.3/', Mage::getVersion())) return;
        $controller =  $observer->getControllerAction();
        $request = $controller->getRequest();
        if ($request->getParam('awwishl'))
        {
            $response = Mage::getModel('ajaxcartpro/response');
            $this->wishlistProcessing($request, $response);
            $response
                    ->setCart(Mage::helper('ajaxcartpro')->renderCart())
                    ->setLinks(Mage::helper('ajaxcartpro')->renderTopCartLinkTitle())
                    ->setWishlist(Mage::helper('ajaxcartpro')->renderWishlist())
                    ->setWishlistSidebar(Mage::helper('ajaxcartpro')->renderWishlistSidebar())
                    ->setWishlistLinks(Mage::helper('ajaxcartpro')->renderWishlistTopLinks())
                    ->send();
            $controllerAction = $observer->getControllerAction();
            if(Mage::helper('ajaxcartpro')->extensionEnabled('AW_Ajaxcartpro')) {
                if(!$controllerAction->getRequest()->getParam('awacpskip'))
                    $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
            }
        }
    }

    public function wishlistProcessing($request, $response)
    {
        try {
            $wishlist = Mage::getModel('wishlist/wishlist')
                ->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer(), true);
            Mage::register('wishlist', $wishlist);
        } catch (Exception $e) {
            return $response->setError(true);
        }

        $itemId     = (int)$request->getParam('item');
        $item       = Mage::getModel('wishlist/item')->load($itemId);
        $session    = Mage::getSingleton('wishlist/session');
        $cart       = Mage::getSingleton('checkout/cart');

        // Magento 1.5. Set qty
        $qtys = $request->getParam('qty');
        if (isset($qtys[$itemId])) {
            $qty = $this->_processLocalizedQty($qtys[$itemId]);
            if ($qty) {
                $item->setQty($qty);
            }
        }

        $product = Mage::getModel('catalog/product')->load($item->getProductId());
        $response->setProductName($product->getName());
        
        try {
            if($product->getHasOptions())
                throw new Mage_Core_Exception('Options is required', Mage_Wishlist_Model_Item::EXCEPTION_CODE_HAS_REQUIRED_OPTIONS);
            $item->addToCart($cart, true);
            $cart->save()->getQuote()->collectTotals();
            $wishlist->save();

            Mage::helper('wishlist')->calculate();

            return $response->setError(false);
        } catch (Mage_Core_Exception $e) {
            if ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_NOT_SALABLE) {
                $session->addError(Mage::helper('wishlist')->__('This product(s) is currently out of stock'));
                $response->setRedirect(Mage::getUrl('*/*'));
            } else if ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_HAS_REQUIRED_OPTIONS) {
                if($product->getHasOptions()) {
                    if($item->getProductUrl()) {
                        if($item->getProduct()->getTypeId() == 'bundle') {
                            $item->delete();
                        }
                        $response->setRedirect($this->_getProductUrl($product));
                        $response->setIsConfigurable($product->isConfigurable());
                        $response->setConfRemoveUrl(Mage::getUrl('wishlist/index/remove', array('item' => $itemId)));
                    }
                } else {
                    $item->delete();
                    $response->setRedirect($item->getProductUrl());
                }
            } else if ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_IS_GROUPED_PRODUCT) {
                $item->delete();
                $response->setRedirect($item->getProductUrl());
            } else {
                $checkoutSession = Mage::getSingleton('checkout/session');
                if($checkoutSession->getRedirectUrl()) {
                    if($checkoutSession->getUseNotice()) {
                        Mage::getSingleton('checkout/session')->addNotice($e->getMessage());
                    } else {
                        Mage::getSingleton('checkout/session')->addError($e->getMessage());
                    }
                    $response->setRedirect($checkoutSession->getRedirectUrl());
                    $response->setIsConfigurable($product->isConfigurable());
                    if($product->isConfigurable()) {
                        $response->setConfRemoveUrl(Mage::getUrl('wishlist/index/remove', array('item' => $itemId)));
                    }
                } else {
                    $session->addError($e->getMessage());
                    $response->setRedirect(Mage::getUrl('*/*'));
                }
            }
        } catch (Exception $e) {
            $session->addException($e, Mage::helper('wishlist')->__('Cannot add item to shopping cart'));
            $response->setRedirect(Mage::getUrl('*/*'));
        }

        Mage::helper('wishlist')->calculate();
        return $response->setError(true);
    }

    /**
     * Magento 1.5. Processes localized qty (entered by user at frontend) into internal php format
     *
     * @param string $qty
     * @return float|int|null
     */
    protected function _processLocalizedQty($qty) {
        if (!isset($this->_localFilter) || !$this->_localFilter) {
            $this->_localFilter = new Zend_Filter_LocalizedToNormalized(array('locale' => Mage::app()->getLocale()->getLocaleCode()));
        }
        $qty = $this->_localFilter->filter($qty);
        if ($qty < 0) {
            $qty = null;
        }
        return $qty;
    }

    public function predispatchCheckoutCartAdd($observer) {
        $controllerAction = $observer->getControllerAction();
        $request = $controllerAction->getRequest();
        if($request->getParam('awacp')) {
            if(($pId = $request->getParam('product'))) {
                $product = Mage::getModel('catalog/product')->load($pId);
                if($product->getData()) {
                    if(!$request->getParam('awacp_options_form')
                            && ($request->getParam('awacp') || $request->getParam('ajaxcartpro'))
                            && $product->getHasOptions() && ($productUrl = $this->_getProductUrl($product))) {
                            $_otherPostCount = false;
                        foreach($request->getPost() as $postOption => $postValue) {
                            if(!in_array($postOption, array(
                                'qty',
                                'product',
                                'related_product'
                            ))) {
                                $_otherPostCount = true;
                                break;
                            }
                        }                            
                        if((!$request->getPost() && $_otherPostCount === false) || (!$request->getPost() && $product->getHasOptions())) {
                            $controllerAction->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                            $_response = Mage::getModel('ajaxcartpro/response')
                                ->setError(true)
                                ->setRedirect($productUrl)
                                ->send();
                        }
                    }
                }
            }
        }
    }

    public function provideIE9Compatibility($observer)
    {
        $body = $observer->getResponse()->getBody();
        if (strpos(strToLower($body), 'x-ua-compatible') !== false) { return; }
        $body = preg_replace('{(</title>)}i', '$1' . '<meta http-equiv="X-UA-Compatible" content="IE=8" />', $body);
        $observer->getResponse()->setBody($body);
    }

    public function pageLoadBeforeFront($observer)
    {
        if(Mage::helper('ajaxcartpro')->extensionEnabled('AW_Ajaxcartpro')) {
            $node = Mage::getConfig()->getNode('global/blocks/wishlist/rewrite');
            $dnode = Mage::getConfig()->getNode('global/blocks/wishlist/drewrite/links');
            $node->appendChild($dnode);
        }
    }
}
