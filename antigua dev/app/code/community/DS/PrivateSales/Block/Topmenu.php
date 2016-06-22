<?php
/**
 * Extended Top Menu Block
 * 
 * @author     Design:Slider GbR <magento@design-slider.de>
 * @copyright  (C)Design:Slider GbR <www.design-slider.de>
 * @license    OSL <http://opensource.org/licenses/osl-3.0.php>
 * @link       http://www.design-slider.de/magento-onlineshop/magento-extensions/private-sales/
 * @package    DS_PrivateSales
 */
class DS_PrivateSales_Block_Topmenu extends Mage_Page_Block_Html_Topmenu
{

    /**
     * Init top menu tree structure
     */
    public function _construct()
    {
        if (Mage::helper('privatesales')->canShowNavigation())
        {
            $this->_menu = new Varien_Data_Tree_Node(array(), 'root', new Varien_Data_Tree());
        }
    }

    /**
     * Supress rendering for guests if hide navigation for guests is enabled
     * 
     * @param string $outermostClass
     * @param string $childrenWrapClass
     * @return string
     * @see Mage_Page_Block_Html_Topmenu::getHtml
     */
    public function getHtml($outermostClass = '', $childrenWrapClass = '')
    {
        if (Mage::helper('privatesales')->canShowNavigation())
        {
            return parent::getHtml($outermostClass, $childrenWrapClass);
        }
        
        return '';
    }

    /**
     * Add navigation can show state to block cache keys
     * 
     * @return array
     * @see Mage_Page_Block_Html_Topmenu::getCacheKeyInfo
     */
    public function getCacheKeyInfo()
    {
        $cacheId = parent::getCacheKeyInfo();
        $cacheId['can_show_navigation'] = Mage::helper('privatesales')->canShowNavigation();
        $cacheId['short_cache_id'] = md5(implode('|', array_values($cacheId)));
        return $cacheId;
    }
}