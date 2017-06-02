<?php 
	require_once "../app/Mage.php";
Mage::app();
umask(0);
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

echo "<hr>BaseUrl:".Mage::getUrl();
Mage::app()->setCurrentStore(7);
echo "<hr>BaseUrl:".Mage::getUrl();