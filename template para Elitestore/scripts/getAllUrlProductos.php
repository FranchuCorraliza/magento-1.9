<?php
ob_end_clean();
//obtenemos todas las categorias de la web con su traduccion en el view 2 
//require_once 'variables.php';
require_once '../app/Mage.php';
Mage::app();

$products = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToFilter('type_id', 'configurable')
                ->addAttributeToFilter('status', 1)
                ->addAttributeToFilter('visibility', array(2, 4)); //only enabled product
foreach($products as $prod) {
$product = Mage::getModel('catalog/product')->load($prod->getId());
echo $product->getSku() . ";" . $product->getProductUrl() . "<br>";
}