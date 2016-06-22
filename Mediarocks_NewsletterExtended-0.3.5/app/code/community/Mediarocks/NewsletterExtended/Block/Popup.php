<?php
/**
 * Media Rocks GbR
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA that is bundled with 
 * this package in the file MEDIAROCKS-LICENSE-COMMUNITY.txt.
 * It is also available through the world-wide-web at this URL:
 * http://solutions.mediarocks.de/MEDIAROCKS-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package is designed for Magento COMMUNITY edition. 
 * Media Rocks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Media Rocks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please send an email to support@mediarocks.de
 *
 */

/**
 * Newsletter Popup Subscription block
 *
 * @category   Mediarocks
 * @package    Mediarocks_NewsletterExtended
 * @author     Media Rocks Developer
 */

class Mediarocks_NewsletterExtended_Block_Popup extends Mage_Newsletter_Block_Subscribe
{

    protected function _toHtml()
    {
        $helper = Mage::helper('mediarocks_newsletterextended');
        $popupConfig = $helper->getPopupConfig();
        
        // fast quit if module is disabled
        if (!$helper->isPopupEnabled()) {
            return;
        }
        
        // show/hide and cookie stuff only if not set to "embed only"
        if (!$helper->isEmbedOnly()) {
        
            // check if customers is logged in
            $isLoggedIn = Mage::getSingleton('customer/session')->isLoggedIn();
            if ($isLoggedIn) {
                
                // hide popup from logged-in customers that have already subscribed to the newsletter
                if ($popupConfig['show_already_subscribed'] != 1) {
                    $email = Mage::getSingleton('customer/session')->getCustomer()->getData('email');
                    $subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($email);
                    
                    if ($subscriber->getId() && $subscriber->isSubscribed()) {
                        
                        // set long expire cookie
                        if ($popupConfig['customers_longexpire'] == 1) {
                            Mage::getModel('core/cookie')->set('mrnle_forcehide', 1, (time() + 31536000)); // hide for one year (if the mode is active)
                        }
                        return;
                    }
                }
            }
            // if not show to everybody
            if ($popupConfig['visibility'] != 0) {
            
                // hide popup if customer is logged in and visibility is set to "guests only"
                // or if customer is logged out and visibility is set to "customers only"
                if (($isLoggedIn && $popupConfig['visibility'] == 2) 
                || (!$isLoggedIn && $popupConfig['visibility'] == 1)) {
                    return;
                }
            }
            
            // hide popup from logged-in customers that have already subscribed to the newsletter
            if (Mage::getModel('core/cookie')->get('mrnle_forcehide') == 1 
             && $popupConfig['customers_longexpire'] == 1 
             && $popupConfig['show_already_subscribed'] != 1) {
                return;
            }
            
            
            // check if cookie is set
            if ($cookieTime = Mage::getModel('core/cookie')->get('mrnle_hide')) {
                
                // get Zend_Date object for current datetime and the expiration datetime
                $expires = $popupConfig['expires'] ? ($popupConfig['expires'] * 60) : 2592000; // default: 30 days
                
                // dont show popup if expiration date is in the future
                if (time() < $cookieTime + $expires) {
                    return;
                }
                
                /*
                // BUGGY
                $dateFormat = Varien_Date::DATETIME_INTERNAL_FORMAT;
                $expires = Mage::getStoreConfig('newsletterextended/popup/expires') ? (Mage::getStoreConfig('newsletterextended/popup/expires') * 60) : 2592000; // default: 30 days
                $dateNow = new Zend_Date(time(), $dateFormat);
                $dateExpires = new Zend_Date($cookieTime + $expires, $dateFormat);
                
                // dont show popup if expiration date is in the future
                if (!$dateNow->isLater($dateExpires)) {
                    return;
                }*/
            }
            
        }
        
		// set extended template if no template or the default template is set (that makes it possible to override the template via layout.xml)
    	if (!$this->getTemplate()) {
			$this->setTemplate('mediarocks/newsletterextended/popup.phtml');
        }
        return parent::_toHtml();
    }
}
