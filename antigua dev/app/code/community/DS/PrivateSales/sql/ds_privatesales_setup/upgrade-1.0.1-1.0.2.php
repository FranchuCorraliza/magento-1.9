<?php
/**
 * Resource Setup for Update from 1.0.1 to 1.0.2
 * 
 * @author     Design:Slider GbR <magento@design-slider.de>
 * @copyright  (C)Design:Slider GbR <www.design-slider.de>
 * @license    OSL <http://opensource.org/licenses/osl-3.0.php>
 * @link       http://www.design-slider.de/magento-onlineshop/magento-extensions/private-sales/
 * @package    DS_PrivateSales
 */
$installer = $this;
/* @var $installer Mage_Eav_Model_Entity_Setup */ 

$installer->startSetup();

# Move old config option for disabling registration to new one
$configCollection = Mage::getModel('core/config_data')->getCollection();
$configCollection->addFieldToFilter('path', array('eq' => 'privatesales/accounting/registration'));
foreach($configCollection as $configData) {
    $configData->setPath('privatesales/registration/disable')->save();
}

# Move old config option for disabling forgot password to new one
$configCollection = Mage::getModel('core/config_data')->getCollection();
$configCollection->addFieldToFilter('path', array('eq' => 'privatesales/accounting/password'));
foreach($configCollection as $configData) {
    $configData->setPath('privatesales/forgot_password/disable')->save();
}

$installer->endSetup();