<?php 
require_once "../app/Mage.php";
Mage::app();
umask(0);

$collection = Mage::getModel('catalog/product')->getCollection();
$collection->addAttributeToFilter('tipo', array('nin' => array('1000','171')));

$collection->joinField(
                        'is_in_stock',
                        'cataloginventory/stock_item',
                        'is_in_stock',
                        'product_id=entity_id',
                        '{{table}}.stock_id=1',
                        'left'
                )
                ->addAttributeToFilter('is_in_stock', array('eq' => 0));

$storeId = 0;                                       
foreach ($collection as $item){
	echo "$contador.-";
	echo "Producto:".$item->getId();
	echo "...";
	Mage::getModel('catalog/product_status')->updateProductStatus($item->getId(), $storeId, Mage_Catalog_Model_Product_Status::STATUS_DISABLED);
	echo "deshabilitado<hr>";
		
}