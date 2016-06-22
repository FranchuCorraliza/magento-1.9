<?php 
require_once "../app/Mage.php";
Mage::app();
umask(0);

Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$store_id = "1";

$productCollection = Mage::getModel('catalog/product')->getCollection();
$productCollection->setOrder('entity_id','DESC');
$productCollection->addStoreFilter($store_id);

echo "\n tengo ".count($productCollection);


foreach ($productCollection as $product) {
    $product->getSku();
    $_product = Mage::getModel('catalog/product')->load($product->getId());
    
    //if($_product->getId()<9223):
    // 	continue;
	//endif;    
    
    echo "\n".$_product->getName(); ///."-> ids = ".implode(",",$product->getWebsiteIds());
    //echo "voy a asignarle = ".implode(",",$store_ids)."<br>";
	if($store_id==1){
	    $_product->setWebsiteIds(array(1,3));
	}
    try {
        $_product->save();
        echo " - saved.";
    } catch (Exception $e) {
        echo ' - '.$e->getMessage();
    }
    echo "\n";

}

	
echo "\n\nfinal del script "; 

?>