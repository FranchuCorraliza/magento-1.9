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

class AW_Ajaxcartpro_Block_Init extends Mage_Core_Block_Template
{
    public function getIsDisabled()
    {
        if(($_product = Mage::registry('current_product'))) {
            return intval($_product->getData(AW_Ajaxcartpro_Helper_Data::CATALOG_PRODUCT_ATTRIBUTE_CODE)) ? 1 : 0;
        }
        return 0;
    }

    public function getDisabledForProducts($asArray = false)
    {
        return Mage::helper('ajaxcartpro/catalog')->getDisabledForProducts($asArray);
    }

    public function getDisabledWishlistItems()
    {
        return Mage::helper('ajaxcartpro/catalog')->getDisabledWishlistItems();
    }

    public function getWishlistVersionMatch()
    {
        return Mage::helper('ajaxcartpro')->compareExtensionVersion('Mage_Wishlist', '0.7.7') ? 1 : 0;
    }
}
