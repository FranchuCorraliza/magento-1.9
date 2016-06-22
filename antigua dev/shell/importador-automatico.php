<?php
//THIS SCRIPT JUST INITIALS THE PROFILE TO BE RUN VIA MAGENTO ADMIN "RUN PROFILE IN POPUP". Its the same thing as click just via this file that you can run via cron
$profileId = 7; // SYSTEM - IMPORT/EXPORT - ADVANCED PROFILES <-- you need to go into your magento admin and grab the exact profile ID
  
require_once 'app/Mage.php';
umask(0);
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
  
$profile = Mage::getModel('dataflow/profile');
$userModel = Mage::getModel('admin/user');
$userModel->setUserId(1);
Mage::getSingleton('admin/session')->setUser($userModel);
$profile->load($profileId);
if (!$profile->getId()) {
    Mage::getSingleton('adminhtml/session')->addError('ERROR: Incorrect profile id');
}
  
Mage::register('current_convert_profile', $profile);
$profile->run();
$recordCount = 0;
$batchModel = Mage::getSingleton('dataflow/batch');
echo "EXPORT COMPLETE. BATCHID: " . $batchModel->getId();
?>