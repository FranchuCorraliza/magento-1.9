<?php
/**
 * Extended Onepage Checkout Login Block
 * 
 * @author     Design:Slider GbR <magento@design-slider.de>
 * @copyright  (C)Design:Slider GbR <www.design-slider.de>
 * @license    OSL <http://opensource.org/licenses/osl-3.0.php>
 * @link       http://www.design-slider.de/magento-onlineshop/magento-extensions/private-sales/
 * @package    DS_PrivateSales
 */
class DS_PrivateSales_Block_Checkout_Onepage_Login extends Mage_Checkout_Block_Onepage_Login
{

    /**
     * Before rendering html, but after trying to load cache
     *
     * @return Mage_Core_Block_Abstract
     * @see Mage_Core_Block_Template::_beforeToHtml
     * @since 2014/11/02 added CE 1.9 support
     * @since 2014/11/02 added proper directory separators to support windows based systems
     * @since 2014/11/02 fixed that enterprise versions have been compared like there were community versions (still no really EE support yet)
     */
    protected function _beforeToHtml()
    {

        // Set latest available template as default
        $this->setTemplate('ds'.DIRECTORY_SEPARATOR.'privatesales'.DIRECTORY_SEPARATOR.'opc_ce19.phtml');

        // Use older template depending on magento version (is there a layout xml based solution for that?)
        $e = method_exists('Mage', 'getEdition') ? Mage::getEdition() : 'Community';
        $v = Mage::getVersion();

        // Use CE 1.4 - 1.6.2 compatible template
        if ($e == 'Community' && version_compare($v, '1.7') < 0) {
            $this->setTemplate('ds'.DIRECTORY_SEPARATOR.'privatesales'.DIRECTORY_SEPARATOR.'opc_ce14_15_16.phtml');
        }

        // Use CE 1.7 - 1.8 compatible template
        elseif ($e == 'Community' && version_compare($v, '1.9') < 0) {
            $this->setTemplate('ds'.DIRECTORY_SEPARATOR.'privatesales'.DIRECTORY_SEPARATOR.'opc_ce17_18.phtml');
        }

        return $this;
    }
}