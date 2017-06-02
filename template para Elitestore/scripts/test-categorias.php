<?php
ob_end_clean();
require_once "../app/Mage.php";
Mage::app();
umask(0);

Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
$category = Mage::getModel('catalog/category')->load(1003);
if ($category){
	echo "La categoría ".$category->getName()." existe y tiene el Id ".$category->getId();
	echo "<hr>";
	var_dump($category->debug());
	
}else{
	echo "No existe ninguna categoría con este Id";
}