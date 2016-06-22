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
 * Adminhtml newsletter subscribers helper
 *
 * @category   Mediarocks
 * @package    Mediarocks_NewsletterExtended
 * @author     Media Rocks Developer
 */

class Mediarocks_NewsletterExtended_Helper_Data extends Mage_Core_Helper_Abstract
{   

    protected $genderOptions;
    
    protected $isTranslationEnabled;
    
    /**
     * Get a list of all customer gender types
     *
     * @return string
     */
    public function getGenderOptions()
    {
        if (!isset($genderOptions)) {
            
            $attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'gender');
            if ($attribute->usesSource()) {
                $options = $attribute->getSource()->getAllOptions(false);
            }
            
            // fallback if no gender types are set in eav_attributes
            if (empty($options)) {
                $options = array(
                    0 => array(
                        'value' => '1',
                        'label' => 'Male'
                    ),
                    1 => array(
                        'value' => '2',
                        'label' => 'Female'
                    )
                );
            }
            
            $this->genderOptions = $options;
        }
        
        return $this->genderOptions;
    }
    
    /**
     * Get Label of gender
     *
     * @var int $type
     * @return string
     */
    public function getGenderLabelByType($type)
    {
        $type = intval($type);
        foreach($this->getGenderOptions() as $option) {
            
            if (isset($option['value']) && isset($option['label']) && intval($option['value']) === $type) {
                return $option['label'];
            }
        }
        
        // fallback for subscribers until newsletter_extended v. 0.3.1 or if no gender types are set in eav_attributes 
        if ($type === 1) {
            return 'Male';
        }
        if ($type === 2) {
            return 'Female';
        }
        return '';
    }
    
    /**
     * Get gender options html for select input field
     *
     * @return string
     */
    public function getGenderOptionsHtml()
    {
        $html = '';
        foreach($this->getGenderOptions() as $option) {
            
            if (isset($option['value']) && isset($option['label'])) {
                $html .= '<option value="' . $option['value'] . '">' . $this->__($option['label']) . '</option>' . PHP_EOL;
            }
        }
        return $html;
    }
    
    /**
     * Get channels
     * 
     * @return array
     */
    public function getChannels()
    {
        $channels = explode(",", Mage::getStoreConfig('newsletterextended/fields/channels'));
        $arrayReturn = array();
        foreach($channels as $channel) {
            if (strlen($channel)) {
                $arrayReturn[] = trim($channel);
            }
        };
        return $arrayReturn;
    }
    
    /**
     * Get channels
     * 
     * @return array
     */
    public function isTranslationEnabled()
    {
        if (!isset($this->isTranslationEnabled)) {
            $this->isTranslationEnabled = Mage::getStoreConfig('newsletterextended/fields/enable_channel_translation');
        }
        return $this->isTranslationEnabled;
    }
    
    /**
     * Get channels
     * 
     * @return array
     */
    public function translateChannel($channelName)
    {
        if ($this->isTranslationEnabled()) {
            return $this->__("Channel_" . $channelName);
        }
        return $channelName;
    }
    
    /**
     * Get popup config
     * 
     * @return array
     */
    public function getPopupConfig()
    {
        return Mage::getStoreConfig('newsletterextended/popup');
    }
    
    /**
     * Get popup delay
     * 
     * @param bool $returnSeconds
     * @return int
     */
    public function getPopupDelay($returnSeconds = false)
    {
        $milliseconds = (int) Mage::getStoreConfig('newsletterextended/popup/delay');
        return $returnSeconds ? $milliseconds / 1000 : $milliseconds;
    }
    
    /**
     * Get popup fadeout duration
     * 
     * @param bool $returnSeconds
     * @return int
     */
    public function getPopupFadeDuration($returnSeconds = false)
    {
        $milliseconds = (int) Mage::getStoreConfig('newsletterextended/popup/fadeout_duration');
        return $returnSeconds ? $milliseconds / 1000 : $milliseconds;
    }
    
    /**
     * Check if popup is enabled
     * 
     * @return bool
     */
    public function isPopupEnabled()
    {
        return (int) Mage::getStoreConfig('newsletterextended/popup/enabled') > 0;
    }
    
    /**
     * Check if popup should only be embedded
     * 
     * @return bool
     */
    public function isEmbedOnly()
    {
        return (int) Mage::getStoreConfig('newsletterextended/popup/enabled') === 2;
    }
    
    /**
     * Set mrnle_hide cookie
     * 
     * @return void
     */
    public function setHideCookie()
    {
        // get expiration time from configuration (defaults to 2592000 seconds == 720 h == 30 days)
        $expires = Mage::getStoreConfig('newsletterextended/popup/expires') ? (Mage::getStoreConfig('newsletterextended/popup/expires') * 60) : 2592000;
        Mage::getModel('core/cookie')->set('mrnle_hide', time(), (time() + $expires));
    }
    
}