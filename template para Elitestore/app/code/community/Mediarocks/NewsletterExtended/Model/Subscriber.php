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
 * NewsletterExtended module subscriber model
 *
 * @category   Mediarocks
 * @package    Mediarocks_NewsletterExtended
 * @author     Media Rocks Developer
 */

class Mediarocks_NewsletterExtended_Model_Subscriber extends Mage_Newsletter_Model_Subscriber
{
    /**
     * Customer model
     *
     * @var Mage_Customer_Model_Customer
     */
    protected $customer;
    
    public function getCustomer()
    {
        if (!isset($this->customer)) {
            $this->customer = Mage::getModel('customer/customer')->load($this->getCustomerId());
        }
        
        return $this->customer;
    }
    
    /**
     * Get the subscribers gender with fallback to customer data
     *
     * @return string
     */
    public function getSubscriberGender()
    {
        $gender = parent::getSubscriberGender();
        
        if (!$gender && Mage::getStoreConfig('newsletterextended/fields/customer_fallback') && $this->getCustomer()) {
            $gender = $this->getCustomer()->getGender();
        }
        
        return $gender;
    }
    
    /**
     * Get the translated subscribers gender label with fallback to customer data
     *
     * @return string
     */
    public function getSubscriberGenderLabel()
    {
        $helper = Mage::helper('mediarocks_newsletterextended');
        $gender = $helper->getGenderLabelByType($this->getSubscriberGender());
        return $helper->__($gender ? 'Salutation_' . $gender : '');
    }
    
    /**
     * Get the subscribers prefix with fallback to customer data
     *
     * @return string
     */
    public function getSubscriberPrefix()
    {
        $prefix = parent::getSubscriberPrefix();
        
        if (!$prefix && Mage::getStoreConfig('newsletterextended/fields/customer_fallback') && $this->getCustomer()) {
            $prefix = $this->getCustomer()->getPrefix();
        }
        return $prefix;
    }
    
    /**
     * Get the subscribers first name with fallback to customer data
     *
     * @return string
     */
    public function getSubscriberFirstname()
    {
        $firstname = parent::getSubscriberFirstname();
        
        if (!$firstname && Mage::getStoreConfig('newsletterextended/fields/customer_fallback') && $this->getCustomer()) {
            $firstname = $this->getCustomer()->getFirstname();
        }
        return $firstname;
    }
    
    /**
     * Get the subscribers last name with fallback to customer data
     *
     * @return string
     */
    public function getSubscriberLastname()
    {
        $lastname = parent::getSubscriberLastname();
        
        if (!$lastname && Mage::getStoreConfig('newsletterextended/fields/customer_fallback') && $this->getCustomer()) {
            $lastname = $this->getCustomer()->getLastname();
        }
        return $lastname;
    }
    
    /**
     * Get the subscribers suffix with fallback to customer data
     *
     * @return string
     */
    public function getSubscriberSuffix()
    {
        $suffix = parent::getSubscriberSuffix();
        
        if (!$suffix && Mage::getStoreConfig('newsletterextended/fields/customer_fallback') && $this->getCustomer()) {
            $suffix = $this->getCustomer()->getSuffix();
        }
        return $suffix;
    }
    
    /**
     * Get the subscribers dob (date of birth) with fallback to customer data
     *
     * @return string
     */
    public function getSubscriberDob()
    {
        $dob = parent::getSubscriberDob();
        
        if (!$dob && Mage::getStoreConfig('newsletterextended/fields/customer_fallback') && $this->getCustomer()) {
            $dob = $this->getCustomer()->getDob();
        }
        return $dob;
    }

    /**
     * Retrieve Subscribers Full Name with fallback to customers full name
     *
     * @return string|null
     */
    public function getSubscriberFullName()
    {
        return trim($this->getSubscriberFirstname() . ' ' . $this->getSubscriberLastname());
    }
}