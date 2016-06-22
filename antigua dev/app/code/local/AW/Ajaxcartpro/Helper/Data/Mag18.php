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


class AW_Ajaxcartpro_Helper_Data_Mag18 extends AW_Ajaxcartpro_Helper_Data {

    /**
     * Return small cart rendered HTML
     * @return string
     */
    public function renderCart() {
        $platform = self::getPlatform();
        $layout = Mage::getSingleton('core/layout');
        $sidebar = $layout
                ->createBlock('checkout/cart_sidebar')
                ->addItemRender('simple', 'checkout/cart_item_renderer', 'checkout/cart/sidebar/default.phtml')
                ->addItemRender('configurable', 'checkout/cart_item_renderer_configurable', 'checkout/cart/sidebar/default.phtml')
                ->addItemRender('grouped', 'checkout/cart_item_renderer_grouped', 'checkout/cart/sidebar/default.phtml')
                ->addItemRender('bundle', 'bundle/checkout_cart_item_renderer', 'checkout/cart/sidebar/default.phtml');

        switch ($platform) {

            case self::PE_PLATFORM:

                if (Mage::helper('ajaxcartpro')->extensionEnabled('AW_Points')) {
                    $pointsInfoBlock = $layout
                            ->createBlock('core/template')
                            ->setTemplate('aw_points/infopagelink.phtml');
                    $pointsBlock = $layout
                            ->createBlock('points/checkout_cart_points')
                            ->setTemplate('aw_points/checkout/cart/sidebar/points.phtml')
                            ->setChild('infopage.link', $pointsInfoBlock);
                    $sidebar
                            ->setChild('checkout.cart.sidebar.points', $pointsBlock)
                            ->setTemplate('aw_points/checkout/cart/sidebar.phtml');
                } else {
                    $sidebar->setTemplate('checkout/cart/sidebar.phtml');
                }

                break;

            case self::EE_PLATFORM:
                $sidebar->setTemplate('checkout/cart/cartheader.phtml');
                break;

            default:
                if (Mage::helper('ajaxcartpro')->extensionEnabled('AW_Points')) {
                    $pointsInfoBlock = $layout
                            ->createBlock('core/template')
                            ->setTemplate('aw_points/infopagelink.phtml');
                    $pointsBlock = $layout
                            ->createBlock('points/checkout_cart_points')
                            ->setTemplate('aw_points/checkout/cart/sidebar/points.phtml')
                            ->setChild('infopage.link', $pointsInfoBlock);
                    $sidebar
                            ->setChild('checkout.cart.sidebar.points', $pointsBlock)
                            ->setTemplate('aw_points/checkout/cart/sidebar.phtml');
                } else {
                    $sidebar->setTemplate('checkout/cart/sidebar.phtml');
                }
                break;
        }

        return $sidebar->renderView();
    }

    /**
     * Return top link with cart items
     * @return string
     */
    public function renderTopCartLinkTitle() {
        $count = Mage::helper('checkout/cart')->getSummaryCount();
        switch (self::getPlatform()) {
            case self::CE_PLATFORM:
                if ($count == 1) {
                    $title = Mage::helper('checkout')->__('My Cart (%s item)', $count);
                } elseif ($count > 0) {
                    $title = Mage::helper('checkout')->__('My Cart (%s items)', $count);
                } else {
                    $title = Mage::helper('checkout')->__('My Cart');
                }
                break;

            default:
                $title = Mage::helper('checkout')->__('My Cart <span>(%s)</span>', $count);
                break;
        }

        return $title;
    }

    public function addAdditionalBlocks($cart) {
        $L = Mage::getSingleton('core/layout');

        if ($this->_checkBlock('enterprise_giftcardaccount/checkout_cart_giftcardaccount')) {
            $giftcart = $L
                    ->createBlock('enterprise_giftcardaccount/checkout_cart_giftcardaccount');
            if ($giftcart) {
                $giftcart->setTemplate('giftcardaccount/cart/block.phtml');
                $cart->setChild('giftcards', $giftcart);
            }
        }
        if ($this->_checkBlock('enterprise_giftcard/checkout_cart_item_renderer')) {
            $giftcart = $L->createBlock('enterprise_giftcard/checkout_cart_item_renderer');
            if ($giftcart) {
                $cart->addItemRender('giftcard', 'enterprise_giftcard/checkout_cart_item_renderer', 'checkout/cart/item/default.phtml');
            }
        }
    }

 }
