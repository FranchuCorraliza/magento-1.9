<?php
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
?>