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
 * NewsletterExtended module observer
 *
 * @category   Mediarocks
 * @package    Mediarocks_NewsletterExtended
 * @author     Media Rocks Developer
 */

class Mediarocks_NewsletterExtended_Model_Observer
{
    public function newsletterSubscriberChange(Varien_Event_Observer $observer)
    {
		
        $subscriber = $observer->getEvent()->getSubscriber();
        $data = Mage::app()->getRequest()->getParams() ? Mage::app()->getRequest()->getParams() : array();
        
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            
            $customer = Mage::getSingleton('customer/session')->getCustomer();
			
            // if the email entered is the same as the one of the logged-in customer, use customer's data as fallback for empty fields
            if (isset($data['email']) && $customer->getEmail() === $data['email']) {
                
                // if override of customer data is disabled, clear all data except email address, 
                // so the grid will allways use the up-to-date customers data
                if (!Mage::getStoreConfig('newsletterextended/fields/customer_override')) {
                    $data['firstname'] = '';
                    $data['lastname'] = '';
                    $data['gender'] = '';
                    $data['prefix'] = '';
                    $data['dob'] = '';
                }
                
                // WE DONT NEED THIS BECAUSE THE FALLBACK WILL ALWAYS USE THE UP-TO-DATE DATA FROM THE CUSTOMER
                /* 
                // if fallback to customer data is enabled, try to get data from the form but fallback to customer information if no value provided
                elseif (Mage::getStoreConfig('newsletterextended/fields/customer_fallback')) {
                    $data['firstname'] = isset($data['firstname']) && $data['firstname'] ? $data['firstname'] : $customer->getFirstname();
                    $data['lastname'] = isset($data['lastname']) && $data['lastname'] ? $data['lastname'] : $customer->getLastname();
                    $data['gender'] = isset($data['gender']) && $data['gender'] ? $data['gender'] : $customer->getGender();
                    $data['prefix'] = isset($data['prefix']) && $data['prefix'] ? $data['prefix'] : $customer->getPrefix();
                }*/
            }
        }
        
        // store data only if email is provided
        if (isset($data['email'])) {
            
            if (isset($data['gender'])) $subscriber->setSubscriberGender($data['gender']);
            if (isset($data['prefix'])) $subscriber->setSubscriberPrefix($data['prefix']);
            if (isset($data['firstname'])) $subscriber->setSubscriberFirstname($data['firstname']);
            if (isset($data['lastname'])) $subscriber->setSubscriberLastname($data['lastname']);
            if (isset($data['suffix'])) $subscriber->setSubscriberSuffix($data['suffix']);
            if (isset($data['dob'])) $subscriber->setSubscriberDob($data['dob']);
            if (isset($data['channels'])) {
                $subscriber->setSubscriberChannels(is_array($data['channels']) ? implode(",", $data['channels']) : $data['channels']);
            }
        }
		elseif($data['is_subscribed']==1){
			$subscriber->setSubscriberSuffix($data['suffix']);
		}
        
        return $this;
    }
}