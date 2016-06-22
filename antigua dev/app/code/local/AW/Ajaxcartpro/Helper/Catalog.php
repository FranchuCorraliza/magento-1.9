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

class AW_Ajaxcartpro_Helper_Catalog extends Mage_Core_Helper_Abstract
{
    const CACHE_KEY = 'aw_catalog_data';

    protected $_isCacheLoaded = false;
    protected $_disabledIds = null;
    protected $_disabledWLItems = null;

    public function getDisabledForProducts($asArray = false) {
        $this->_awCheckCache();
        if($this->_disabledIds === null) {
            $_productIds = array();
            $_productCollection = Mage::getResourceModel('ajaxcartpro/product_collection');
            $_productCollection->addAttributeToFilter(AW_Ajaxcartpro_Helper_Data::CATALOG_PRODUCT_ATTRIBUTE_CODE, '1');
            $_productIds = $_productCollection->getAllIds();
            $this->_disabledIds = $_productIds;
            $this->_awSaveCache();
        }
        return $asArray ? $this->_disabledIds : '['.implode(',', $this->_disabledIds).']';
    }
    
    /**
     * Add 'noacp' flag into additional options array for getAddToCartUrl()
     *
     * @param   int $productId
     * @param   array  $additional 
     * @return  array
     */
    public function getAddToCartUrlAdditional($productId, $additional = array())
    {
        if(Mage::helper('ajaxcartpro')->extensionEnabled('AW_Ajaxcartpro')) {
            if(in_array($productId, $this->getDisabledForProducts(true))) {
                if(!isset($additional['_query'])) {
                    $additional['_query'] = array();
                }
                $additional['_query']['noacp'] = 1;
            }
        }
        return $additional;
    }

    public function getDisabledWishlistItems()
    {
        if($this->_disabledWLItems === null) {
            $_dItems = array();
            $wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer());
            if($wishlist->getData()) {
                $collection = $wishlist->getItemCollection();
                foreach($collection as $item) {
                    if(in_array($item->getProductId(), $this->getDisabledForProducts(true)))
                        $_dItems[] = $item->getId();
                }
            }
            $this->_disabledWLItems = $_dItems;
        }
        return '['.implode(',', $this->_disabledWLItems).']';
    }

    protected function _awCheckCache()
    {
        if(!$this->_isCacheLoaded) {
            $this->_awLoadCache();
        }
    }

    protected function _awLoadCache() {
        if(Mage::app()->loadCache(self::CACHE_KEY)) {
            $this->_disabledIds = @unserialize(Mage::app()->loadCache(self::CACHE_KEY));
        }
        return $this;
    }
    
    protected function _awSaveCache() {
        Mage::app()->saveCache(serialize($this->_disabledIds), self::CACHE_KEY, array(), 60);
        return $this;
    }
}
