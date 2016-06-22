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

class AW_Ajaxcartpro_Helper_Data_Mag14 extends AW_Ajaxcartpro_Helper_Data {

    /**
     * Return small cart rendered HTML
     * @return string
     */
    public function renderCart() {
        $layout = Mage::getSingleton('core/layout');

        $cartSidebar = $layout
                ->createBlock('checkout/cart_sidebar')
                ->addItemRender('simple', 'checkout/cart_item_renderer', 'checkout/cart/sidebar/default.phtml')
                ->addItemRender('configurable', 'checkout/cart_item_renderer_configurable', 'checkout/cart/sidebar/default.phtml')
                ->addItemRender('grouped', 'checkout/cart_item_renderer_grouped', 'checkout/cart/sidebar/default.phtml')
                ->addItemRender('bundle', 'bundle/checkout_cart_item_renderer', 'checkout/cart/sidebar/default.phtml');
        
        if(self::checkVersion('1.4.1') && Mage::helper('ajaxcartpro')->extensionEnabled('Mage_PaypalUk') && Mage::helper('ajaxcartpro')->extensionEnabled('Mage_Paypal')) {
            $_extraActions = $layout->createBlock('core/text_list', 'extra_actions');
            $_paypalukEC = $layout->createBlock('paypaluk/express_shortcut', 'paypaluk.partner.cart_sidebar.shortcut');
            $_paypalukEC->setTemplate('paypal/express/shortcut.phtml');
            $_extraActions->append($_paypalukEC, 'shortcut');
            $cartSidebar->setChild('extra_actions', $_extraActions);
        }
        
        if (Mage::helper('ajaxcartpro')->extensionEnabled('AW_Points'))
        {
            $pointsInfoBlock = $layout
                    ->createBlock('core/template')
                    ->setTemplate('aw_points/infopagelink.phtml');
            $pointsBlock = $layout
                    ->createBlock('points/checkout_cart_points')
                    ->setTemplate('aw_points/checkout/cart/sidebar/points.phtml')
                    ->setChild('infopage.link', $pointsInfoBlock);
            $cartSidebar
                ->setChild('checkout.cart.sidebar.points', $pointsBlock)
                ->setTemplate('aw_points/checkout/cart/sidebar.phtml');
        }
        else
        {
            $cartSidebar->setTemplate('checkout/cart/sidebar.phtml');
        }
        Mage::getSingleton('ajaxcartpro/observer')->recalcQuote();
        return $cartSidebar->renderView();
    }

    /**
     * Return top link with cart items
     * @return string
     */
    public function renderTopCartLinkTitle() {
        $count = Mage::helper('checkout/cart')->getSummaryCount();
        if( $count == 1 ) {
            $text = Mage::helper('checkout')->__('My Cart (%s item)', $count);
        } elseif( $count > 0 ) {
            $text = Mage::helper('checkout')->__('My Cart (%s items)', $count);
        } else {
            $text = Mage::helper('checkout')->__('My Cart');
        }
        return $text;
    }
}
