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
 * NewsletterExtended module channel model
 *
 * @category   Mediarocks
 * @package    Mediarocks_NewsletterExtended
 * @author     Media Rocks Developer
 */

class Mediarocks_NewsletterExtended_Model_Channel extends Mage_Core_Model_Abstract
{
    /**
     * Channel model
     *
     * @var Mediarocks_NewsletterExtended_Model_Channel
     */
    protected $channel;
    
    protected function _construct()
    {
        $this->_init('mediarocks_newsletterextended/channel');
    }
    
    public function getChannel()
    {
        if (!isset($this->channel)) {
            $this->channel = Mage::getModel('mediarocks_newsletterextended/channel')->load($this->getChannelId());
        }
        
        return $this->channel;
    }
    
    /**
     * Get the translated subscribers gender label with fallback to customer data
     *
     * @return string
     */
    public function getChannelLabel()
    {
        $helper = Mage::helper('mediarocks_newsletterextended');
        $gender = $helper->getGenderLabelByType($this->getSubscriberGender());
        return $helper->__($gender ? 'Salutation_' . $gender : '');
    }
}