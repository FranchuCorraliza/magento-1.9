<?php 
	require_once "../app/Mage.php";
	Mage::app();
	umask(0);
	$storeId=2;
	$categoryId=1003;
	Mage::app()->getStore()->setId(Mage_Core_Model_App::ADMIN_STORE_ID);

	$descripcion="Prueba definitiva";
	echo "<hr>Guardando Descripcion<br/>";
	try{
		$category=Mage::getModel("catalog/category")->setStoreId($storeId)->load($categoryId);
		//$category->setName("Probando clothing");
		$category->setDescription($descripcion);
		/*
		$data=$category->getData();
		$data['description']=$description;
		$category->addData($data);
		*/
		$category->save();
	}catch( Exception $e){
		echo "Error";
		var_dump($e);
	}
	
	echo "<hr>";
	$category=Mage::getModel("catalog/category")->setStoreId($storeId)->load($categoryId);
	echo $category->getDescription();
	echo "<hr>";
	