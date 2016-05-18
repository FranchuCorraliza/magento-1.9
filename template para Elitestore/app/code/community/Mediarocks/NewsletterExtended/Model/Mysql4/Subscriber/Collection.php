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
 * NewsletterExtended Subscribers Collection
 *
 * @category   Mediarocks
 * @package    Mediarocks_NewsletterExtended
 * @author     Media Rocks Developer
 */

class Mediarocks_NewsletterExtended_Model_Mysql4_Subscriber_Collection extends Mage_Newsletter_Model_Mysql4_Subscriber_Collection
{
    /**
     * Adds customer info to select
     *
     * @return Mage_Newsletter_Model_Resource_Subscriber_Collection
     */
    public function showCustomerGender()
    {
        $adapter = $this->getConnection();
        $customer = Mage::getModel('customer/customer');
        $gender   = $customer->getAttribute('gender');
        
        $this->getSelect()
            ->joinLeft(
                array('customer_gender_table'=>$gender->getBackend()->getTable()),
                $adapter->quoteInto('customer_gender_table.entity_id=main_table.customer_id
                 AND customer_gender_table.attribute_id = ?', (int)$gender->getAttributeId()),
                array('customer_gender'=>'value')
            );

        return $this;
    }
    
    public function showCustomerPrefix()
    {
        $adapter = $this->getConnection();
        $customer = Mage::getModel('customer/customer');
        $prefix   = $customer->getAttribute('prefix');

        $this->getSelect()
            ->joinLeft(
                array('customer_prefix_table'=>$prefix->getBackend()->getTable()),
                $adapter->quoteInto('customer_prefix_table.entity_id=main_table.customer_id
                 AND customer_prefix_table.attribute_id = ?', (int)$prefix->getAttributeId()),
                array('customer_prefix'=>'value')
            );
            
        return $this;
    }
    
    public function showCustomerSuffix()
    {
        $adapter = $this->getConnection();
        $customer = Mage::getModel('customer/customer');
        $suffix   = $customer->getAttribute('suffix');

        $this->getSelect()
            ->joinLeft(
                array('customer_suffix_table'=>$suffix->getBackend()->getTable()),
                $adapter->quoteInto('customer_suffix_table.entity_id=main_table.customer_id
                 AND customer_suffix_table.attribute_id = ?', (int)$suffix->getAttributeId()),
                array('customer_suffix'=>'value')
            );
            
        return $this;
    }
    
    public function showCustomerDob()
    {
        $adapter = $this->getConnection();
        $customer = Mage::getModel('customer/customer');
        $dob   = $customer->getAttribute('dob');

        $this->getSelect()
            ->joinLeft(
                array('customer_dob_table'=>$dob->getBackend()->getTable()),
                $adapter->quoteInto('customer_dob_table.entity_id=main_table.customer_id
                 AND customer_dob_table.attribute_id = ?', (int)$dob->getAttributeId()),
                array('customer_dob'=>'value')
            );
            
        return $this;
    }
}
