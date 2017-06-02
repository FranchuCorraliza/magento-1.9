<?php
require_once "../app/Mage.php";
Mage::app();
umask(0);
ob_end_clean();
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
$collection = Mage::getResourceModel('manufacturer/manufacturer_collection');
foreach ($collection as $manufacturer){
		echo "<hr>";
		echo "Nombre:".$manufacturer->getName();
		echo "<hr>";
		$manufacturer = Mage::getModel('manufacturer/manufacturer')->getManufacturerByName($manufacturer->getName());	
		$manufacturer->save();
		$manufacturer->updateUrlKey();
}
