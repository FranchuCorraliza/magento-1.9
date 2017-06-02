<?php 
require_once "../app/Mage.php";
Mage::app();
umask(0);

Mage::app()->setCurrentStore('admin');

	$productCollection= Mage::getModel('catalog/product')->getCollection();
	$productCollection->addAttributeToSelect('*')
					->addAttributeToFilter('status', 1)
 					->addAttributeToFilter('type_id', 'configurable')
					->addAttributeToFilter('outlet', 1);
	Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($productCollection);					
  	echo 'Productos Rebajados:';
	foreach ($productCollection as $product):
			echo $product->getSku().", ";
			$_product=Mage::getModel("catalog/product")->load($product->getId());
			$childProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null,$_product);
			foreach ($childProducts as $simple):
				echo $simple->getSku();
				echo ", ";
			endforeach;
	endforeach;
?>