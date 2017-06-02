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

class AW_Ajaxcartpro_Helper_Data extends Mage_Core_Helper_Abstract {
    const RETURN_CARTBAR_ON_BIGCART = true;
    const CATALOG_PRODUCT_ATTRIBUTE_CODE = 'aw_acp_disabled';

    const EE_PLATFORM = 100;
    const PE_PLATFORM = 10;
    const CE_PLATFORM = 0;
    const ENTERPRISE_DETECT_COMPANY = 'Enterprise';
    const ENTERPRISE_DETECT_EXTENSION = 'Enterprise';
    const ENTERPRISE_DESIGN_NAME = "enterprise";
    const PROFESSIONAL_DESIGN_NAME = "pro";

    protected static $instance;
    protected static $_platform = -1;

    /**
     * Checks which edition is used
     * @return int
     */
    protected function getPlatform() {
        if (self::$_platform == -1) {
            $pathToClaim = BP . DS . "app" . DS . "etc" . DS . "modules" . DS . self::ENTERPRISE_DETECT_COMPANY . "_" . self::ENTERPRISE_DETECT_EXTENSION . ".xml";
            $pathToEEConfig = BP . DS . "app" . DS . "code" . DS . "core" . DS . self::ENTERPRISE_DETECT_COMPANY . DS . self::ENTERPRISE_DETECT_EXTENSION . DS . "etc" . DS . "config.xml";
            $isCommunity = !file_exists($pathToClaim) || !file_exists($pathToEEConfig);
            if ($isCommunity) {
                self::$_platform = self::CE_PLATFORM;
            } else {
                $_xml = @simplexml_load_file($pathToEEConfig, 'SimpleXMLElement', LIBXML_NOCDATA);
                if (!$_xml === FALSE) {
                    $package = (string) $_xml->default->design->package->name;
                    $theme = (string) $_xml->install->design->theme->default;
                    $skin = (string) $_xml->stores->admin->design->theme->skin;
                    $isProffessional = ($package == self::PROFESSIONAL_DESIGN_NAME) && ($theme == self::PROFESSIONAL_DESIGN_NAME) && ($skin == self::PROFESSIONAL_DESIGN_NAME);
                    if ($isProffessional) {
                        self::$_platform = self::PE_PLATFORM;
                        return self::$_platform;
                    }
                }
                self::$_platform = self::EE_PLATFORM;
            }
        }
        return self::$_platform;
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = (preg_match('/^(1.[7-9]|1.1[0-9])/', Mage::getVersion())) ?
                    Mage::helper('ajaxcartpro/data_mag18') :
                    Mage::helper('ajaxcartpro/data_mag14');
        }
        return self::$instance;
    }

    public function renderWishlist() {

        if ($this->_checkBlock("wishlist/customer_wishlist_items")) {
            $L = Mage::app()->getLayout();
            $L->getUpdate()->addUpdate('
                <block type="wishlist/customer_wishlist" name="customer.wishlist" template="wishlist/view.phtml">
                    <action method="setTitle" translate="title">
                        <title>My Wishlist</title>
                    </action>
                    <block type="wishlist/customer_wishlist_items" name="customer.wishlist.items" as="items" template="wishlist/item/list.phtml">
                        <block type="wishlist/customer_wishlist_item_column_image" name="customer.wishlist.item.image" template="wishlist/item/column/image.phtml" />
                        <block type="wishlist/customer_wishlist_item_column_comment" name="customer.wishlist.item.info" template="wishlist/item/column/info.phtml">
                            <action method="setTitle" translate="title">
                                <title>Product Details and Comment</title>
                            </action>
                        </block>
                        <block type="wishlist/customer_wishlist_item_column_cart" name="customer.wishlist.item.cart" template="wishlist/item/column/cart.phtml">
                            <action method="setTitle" translate="title">
                                <title>Add to Cart</title>
                            </action>
                            <block type="wishlist/customer_wishlist_item_options" name="customer.wishlist.item.options" />
                        </block>
                        <block type="wishlist/customer_wishlist_item_column_remove" name="customer.wishlist.item.remove" template="wishlist/item/column/remove.phtml" />
                    </block>
                    <block type="core/text_list" name="customer.wishlist.buttons" as="control_buttons">
                        <block type="wishlist/customer_wishlist_button" name="customer.wishlist.button.share" template="wishlist/button/share.phtml" />
                        <block type="wishlist/customer_wishlist_button" name="customer.wishlist.button.toCart" template="wishlist/button/tocart.phtml" />
                        <block type="wishlist/customer_wishlist_button" name="customer.wishlist.button.update" template="wishlist/button/update.phtml" />
                    </block>
                </block>');

            $L->generateXml()->generateBlocks();
            $wishlistBlock = $L->getBlock('customer.wishlist');
        } else {

            $wishlistBlock = Mage::getSingleton('core/layout')
                    ->createBlock('wishlist/customer_wishlist')
                    ->setTemplate('wishlist/view.phtml');
        }

        if (@method_exists($wishlistBlock, 'addOptionsRenderCfg')) {
            $itemOptionsBlock = Mage::getSingleton('core/layout')->createBlock('wishlist/customer_wishlist_item_options', 'customer.wishlist.item.options');
            if ($itemOptionsBlock) {
                $wishlistBlock->append($itemOptionsBlock, 'item_options');
            }
            $wishlistBlock->addOptionsRenderCfg('bundle', 'bundle/catalog_product_configuration')
                    ->addOptionsRenderCfg('downloadable', 'downloadable/catalog_product_configuration');
        }
        if (@method_exists($wishlistBlock, 'addPriceBlockType')) {
            $wishlistBlock->addPriceBlockType('bundle', 'bundle/catalog_product_price', 'bundle/catalog/product/price.phtml');
        }
        return trim($wishlistBlock->renderView());
    }

    /**
     * Return wishlist rendered HTML
     * @return string
     */
    public function renderWishlistSidebar() {
        $wishlistSidebar = Mage::getSingleton('core/layout')
            ->createBlock('wishlist/customer_sidebar')
            ->setTemplate('wishlist/sidebar.phtml');
        if(@method_exists($wishlistSidebar, 'addPriceBlockType')) {
            $wishlistSidebar->addPriceBlockType('bundle', 'bundle/catalog_product_price', 'bundle/catalog/product/price.phtml');
        }
        return  trim($wishlistSidebar->renderView());
    }

    /**
     * Return wishlist top links rendered text
     * @return string
     */
    public function renderWishlistTopLinks() {
        $count = Mage::helper('wishlist')->getItemCount();
        if( $count == 1 ) {
            $text = Mage::helper('checkout')->__('My Wishlist (%s item)', $count);
        } elseif( $count > 0 ) {
            $text = Mage::helper('checkout')->__('My Wishlist (%s items)', $count);
        } else {
            $text = Mage::helper('checkout')->__('My Wishlist');
        }
        return $text;
    }

    /**
     * Return small cart rendered HTML
     * @return string
     */
    public function renderCart() {
        return trim(self::getInstance()->renderCart());
    }

    /**
     * Render big cart and return its HTML
     * @return string
     */
    public function renderBigCart() {

        $L = Mage::getSingleton('core/layout');


        $totals = $L
                ->createBlock('checkout/cart_totals')
                ->setTemplate('checkout/cart/totals.phtml')
        ;
        $shipping = $L
                ->createBlock('checkout/cart_shipping')
                ->setTemplate('checkout/cart/shipping.phtml')
        ;

        $coupon = $L
                ->createBlock('checkout/cart_coupon')
                ->setTemplate('checkout/cart/coupon.phtml')
        ;

        // top methods

        $t_onepage = $L
                ->createBlock('checkout/onepage_link')
                ->setTemplate('checkout/onepage/link.phtml')
        ;
        $t_methods = $L
                ->createBlock('core/text_list')
                ->append($t_onepage, 'top_methods');


        //methods
        $onepage = $L
                ->createBlock('checkout/onepage_link')
                ->setTemplate('checkout/onepage/link.phtml')
        ;

        $multishipping = $L
                ->createBlock('checkout/multishipping_link')
                ->setTemplate('checkout/multishipping/link.phtml')
        ;




        $methods = 	$L
                ->createBlock('core/text_list')
                ->append($onepage, "onepage")
                ->append($multishipping, "multishipping");


        // Cross-sales etc

        $crossel = $L
                ->createBlock('checkout/cart_crosssell')
                ->setTemplate('checkout/cart/crosssell.phtml')
        ;


        Mage::getSingleton('checkout/session')->setCartWasUpdated(true);

        $cart1 = $L
                ->createBlock('checkout/cart')
                ->setEmptyTemplate('checkout/cart/noItems.phtml')
                ->setCartTemplate('checkout/cart.phtml')
                ->addItemRender('simple', 'checkout/cart_item_renderer', 'checkout/cart/item/default.phtml')
                ->addItemRender('configurable', 'checkout/cart_item_renderer_configurable', 'checkout/cart/item/default.phtml')
                ->addItemRender('grouped', 'checkout/cart_item_renderer_grouped', 'checkout/cart/item/default.phtml')
                ->addItemRender('downloadable', 'downloadable/checkout_cart_item_renderer', 'downloadable/checkout/cart/item/default.phtml')
                ->addItemRender('bundle', 'bundle/checkout_cart_item_renderer', 'checkout/cart/item/default.phtml')
                ->addItemRender('subscription_simple', 'sarp/checkout_cart_item_renderer_simple', 'checkout/cart/item/default.phtml')
                ->addItemRender('bookable', 'booking/checkout_cart_item_renderer', 'checkout/cart/item/default.phtml')
                ->setTemplate('checkout/cart.phtml')
                ->setChild('top_methods',$t_methods)
                ->setChild('totals', $totals)
                ->setChild('shipping', $shipping)
                ->setChild('coupon', $coupon)
                ->setChild('methods', $methods)
                ->setChild('crosssell', $crossel)
        ;

        //TODO Add observer
        $this->getInstance()->addAdditionalBlocks($cart1);

        $cart1
                ->chooseTemplate();
        
        $readyCart = trim($cart1->renderView());

        /* Points compatibility */
        if ($this->extensionEnabled('AW_Points')) {
            $pointsInfoBlock = $L
                ->createBlock('core/template')
                ->setTemplate('aw_points/infopagelink.phtml');
            $pointsBlock = $L
                ->createBlock('points/checkout_cart_points');
            if($pointsBlock) {
                $pointsBlock
                    ->setTemplate('aw_points/checkout/cart/points.phtml')
                    ->setChild('infopage.link', $pointsInfoBlock);
                $pointsHtml = $pointsBlock->toHtml();
                $readyCart = $pointsHtml.$readyCart;
            }
        }

        /* Checkout Promo compatibility */
        $checkoutPromoName = 'AW_Checkoutpromo';
        if ($this->extensionEnabled($checkoutPromoName))
        {
            $CPcart = '';
            if (version_compare($this->getExtensionVersion($checkoutPromoName), '1.2.0') >= 0)
            {
                $appliedBlockIds = Mage::app()->getLayout()->createBlock('checkoutpromo/checkoutpromo')->getAppliedBlockIds();
            }
            else
            {
                $appliedBlockIds = Mage::helper('checkoutpromo')->getAppliedBlockIds();
            }
            if (is_array($appliedBlockIds) && array_key_exists('shoppingcartpromo', $appliedBlockIds))
            {
                foreach ($appliedBlockIds['shoppingcartpromo'] as $appliedBlockId)
                {
                    $CPcart .= $L->createBlock('cms/block')
                            ->setBlockId($appliedBlockId)
                            ->toHtml();
                }
            }
            $readyCart = $CPcart.$readyCart;
        }
        
        /* FP3 compatibility */
        if($this->extensionEnabled('AW_Featured')) {
            $FP3Cart = Mage::getSingleton('core/layout')->createBlock('awfeatured/block', 'awafp.content.top');
            $readyCart = $FP3Cart ? $FP3Cart->toHtml().$readyCart : $readyCart;
        }

        return $readyCart;
    }

    /**
     * Return top link with cart items
     * @return string
     */
    public function renderTopCartLinkTitle() {
        return trim(self::getInstance()->renderTopCartLinkTitle());
    }

    public function displayCountdown()
    {
        $countdown = Mage::getStoreConfig('ajaxcartpro/general/countdown');
        $html = '';
        if ((int)$countdown && $countdown>0)
            $html = ' (<span id="ACPcountdown">'.$countdown.'</span>)';
        return $html;
    }

    public function extensionEnabled($extension_name)
    {
        if (!isset($this->extensionEnabled[$extension_name]))
        {
            $modules = (array)Mage::getConfig()->getNode('modules')->children();
            if (!isset($modules[$extension_name])
                || $modules[$extension_name]->descend('active')->asArray()=='false'
                || Mage::getStoreConfig('advanced/modules_disable_output/'.$extension_name)
            ) $this->extensionEnabled[$extension_name] = false;
            else $this->extensionEnabled[$extension_name] = true;
        }
        return $this->extensionEnabled[$extension_name];
    }

    public static function getExtensionVersion($extensionName)
    {
        return Mage::getConfig()->getModuleConfig($extensionName)->version;
    }

    public function hasFileOption() {
        $product = Mage::registry('current_product');
        if ($product) {
            foreach ($product->getOptions() as $option) {
                if ($option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_FILE) {
                    return true;
                }
            }
        }
        return false;
    }

    public function addAdditionalBlocks($cart)
    { }
    
    /**
     * Checking version of Magento
     * @param string $version
     * @return bool true when Magento version >= $version, false - otherwise
     */
    public static function checkVersion($version, $operator = '>=') {
        return version_compare(Mage::getVersion(), $version, $operator);
    }
    
    public function isProductBundle($product) {
        return $product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE;
    }

    public function compareExtensionVersion($name, $version) {
        $_modules = (array) Mage::getConfig()->getNode('modules')->children();
        if (
                array_key_exists($name, $_modules) &&
                'true' == (string) $_modules[$name]->active &&
                !(bool) Mage::getStoreConfig('advanced/modules_disable_output/' . $name)
        ) {
            return version_compare($_modules[$name]->version, $version, '>=');
        }
        return null;
    }

    protected function _checkBlock($block) {
        if (strpos($block, '/') !== false) {
            if (!$block = Mage::getConfig()->getBlockClassName($block)) {
                return false;
            }
        }
        if (class_exists($block, false) || mageFindClassFile($block)) {
            return true;
        }
        return false;
    }
	
	public function renderCount(){
		return Mage::helper('checkout/cart')->getSummaryCount();
	}

}
