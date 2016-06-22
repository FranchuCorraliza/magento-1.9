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

class AW_Ajaxcartpro_WishlistController extends Mage_Core_Controller_Front_Action
{

    protected function _getWishlist()
    {
        try {
            $wishlist = Mage::getModel('wishlist/wishlist')
                ->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer(), true);
            Mage::register('wishlist', $wishlist);
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('wishlist/session')->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::getSingleton('wishlist/session')->addException($e,
                Mage::helper('wishlist')->__('Cannot create wishlist.')
            );
            return false;
        }
        return $wishlist;
    }

    public function cartAction()
    {
        $wishlist   = $this->_getWishlist();
        if (!$wishlist) {
            return $this->_redirect('*/*');
        }
        Mage::getSingleton('checkout/session')->setNoCartRedirect(true);
        $itemId     = (int)$this->getRequest()->getParam('item');
        $item       = Mage::getModel('wishlist/item')->load($itemId);
        $response = Mage::getModel('ajaxcartpro/response');
        $session    = Mage::getSingleton('wishlist/session');
        $cart       = Mage::getSingleton('checkout/cart');
        $_helper = Mage::helper('ajaxcartpro');
        $product=Mage::getSingleton('catalog/product')->load($item->getProductId());
        try {
           
            $item->addToCart($cart, true);
            $cart->save()-> getQuote()->collectTotals();
            $wishlist->save();
            Mage::helper('wishlist')->calculate();

        } catch (Mage_Core_Exception $e) {
                    if ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_NOT_SALABLE) {
                        $session->addError(Mage::helper('wishlist')->__('This product(s) is currently out of stock'));
                        $response->setError(true);
                    } else if ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_HAS_REQUIRED_OPTIONS) {
                        $redirectUrl = $item->getProductUrl();
                        $item->delete();
                        $response->setRedirect($redirectUrl);
                        $response->setError(true);
                    } else if ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_IS_GROUPED_PRODUCT) {
                        $redirectUrl = $item->getProductUrl();
                        $item->delete();
                        $response->setRedirect($redirectUrl);
                        $response->setError(true);
                    } else {
                        $redirectUrl = $item->getProductUrl();
                        $item->delete();
                        $session->addError($e->getMessage());
                        $response->setError(true);
                        $response->setRedirect($redirectUrl);
                    }
            } catch (Exception $e) {
                    $redirectUrl = $item->getProductUrl();
                    $session->addException($e, Mage::helper('wishlist')->__('Cannot add item to shopping cart'));
                    $response->setError(true);
                    $response->setRedirect($redirectUrl);
            }
        Mage::helper('wishlist')->calculate();

        $response->setCart($_helper->renderCart())
                ->setLinks($_helper->renderTopCartLinkTitle())
                ->setProductName($product->getName())
                ->setWishlist($_helper->renderWishlist())
                ->setWishlistSidebar($_helper->renderWishlistSidebar())
                ->setWishlistLinks($_helper->renderWishlistTopLinks())
                ->send();

    }
}
