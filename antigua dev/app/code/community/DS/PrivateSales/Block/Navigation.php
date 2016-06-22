<?php
/**
 * Extended Navigation Block
 * 
 * @author     Design:Slider GbR <magento@design-slider.de>
 * @copyright  (C)Design:Slider GbR <www.design-slider.de>
 * @license    OSL <http://opensource.org/licenses/osl-3.0.php>
 * @link       http://www.design-slider.de/magento-onlineshop/magento-extensions/private-sales/
 * @package    DS_PrivateSales
 */
class DS_PrivateSales_Block_Navigation extends Mage_Catalog_Block_Navigation
{

    /**
     * Supress rendering for guests if hide navigation for guests is enabled
     *
     * @param int Level number for list item class to start from
     * @param string Extra class of outermost list items
     * @param string If specified wraps children list in div with this class
     * @return string
     * @see Mage_Catalog_Block_Navigation::renderCategoriesMenuHtml
     */
    public function renderCategoriesMenuHtml($level = 0, $outermostItemClass = '', $childrenWrapClass = '')
    {
        if (Mage::helper('privatesales')->canShowNavigation())
        {
            return parent::renderCategoriesMenuHtml($level, $outermostItemClass, $childrenWrapClass);
        }
        
        return '';
    }

    /**
     * Add navigation can show state to block cache keys
     * 
     * @return array
     * @see Mage_Catalog_Block_Navigation::getCacheKeyInfo
     */
    public function getCacheKeyInfo()
    {
        $cacheId = parent::getCacheKeyInfo();
        $cacheId['can_show_navigation'] = Mage::helper('privatesales')->canShowNavigation();
        $cacheId['short_cache_id'] = md5(implode('|', array_values($cacheId)));
        return $cacheId;
    }
}