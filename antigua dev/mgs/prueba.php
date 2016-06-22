<?php
echo "hola";

//THIS SCRIPT JUST INITIALS THE PROFILE TO BE RUN VIA MAGENTO ADMIN "RUN PROFILE IN POPUP". Its the same thing as click just via this file that you can run via cron
$profileId = 7; // SYSTEM - IMPORT/EXPORT - ADVANCED PROFILES <-- you need to go into your magento admin and grab the exact profile ID
  
require_once 'app/Mage.php';
umask(0);
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
  
$profile = Mage::getModel('dataflow/profile');

$root = '/var/www/vhosts/elitestore.es/httpdocs/';
require_once $root.'/app/Mage.php';
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$username = 'importador';
$password = 'importador2013';
 
//starting the import
Mage::getSingleton("admin/session", array("name"=>"adminhtml"));
$session = Mage::getSingleton("admin/session");
 
$sessionId = $session->getEncryptedSessionId();
 
$formKey = Mage::getSingleton('core/session')->getFormKey();
 
echo json_encode(array('sessionId' => $sessionId, 'formKey' => $formKey));


$profile->load($profileId);
if (!$profile->getId()) {
    Mage::getSingleton('adminhtml/session')->addError('ERROR: Incorrect profile id');
}else{
echo "error";
}
  
Mage::register('current_convert_profile', $profile);
$profile->run();
$recordCount = 0;
$batchModel = Mage::getSingleton('dataflow/batch');
echo "EXPORT COMPLETE. BATCHID: " . $batchModel->getId();
?>