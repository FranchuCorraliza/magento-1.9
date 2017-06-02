<?php require_once '../app/Mage.php'; ini_set('display_errors', 1);
#Varien_Profiler::enable();
echo "Iniciando actualizacion<br/>";
Mage::setIsDeveloperMode(true);
umask(0); 
Mage::app();
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
try {
	Mage::getModel('catalogrule/rule')->applyAll();
	Mage::app()->removeCache('catalog_rules_dirty');
}catch (Exception $e){
	echo Mage::helper('catalogrule')->__('Unable to apply rules.');
    print_r($e);
}
echo Mage::helper('catalogrule')->__('The rules have been applied.');	
/*
//foreach ($productos as $product_id) {
	$product_id=119388;
        try {
			//Mage::app()->removeCache('catalog_rules_dirty');
            $_product=Mage::getModel('catalog/product')->load($product_id);
            Mage::getModel('catalogrule/rule')->applyAllRulesToProduct($_product);
			$_product->save();
			
			$price=Mage::getModel('catalogrule/rule')->calcProductPriceRule($_product->setStoreId(2)->setCustomerGroupId(7),$_product->getPrice());
                //Mage::getModel('catalogrule/rule')->applyAll();
                
                $resource = Mage::getResourceSingleton('catalogrule/rule');
                $resource->applyAllRulesForDateRange(); // Applies all rules for yesterday, today and tomorrow.
                Mage::app()->removeCache('catalog_rules_dirty'); // clear cache
                
            echo "aplicada la regla para " . $_product->getSku() . " --> Precio antes: " .$_product->getPrice(). " --> Con precio: " .$price.  "<br/>";
            
		} catch (Exception $e) {
            echo Mage::helper('catalogrule')->__('Unable to apply rules.');
            print_r($e);
        }
//}
Mage::app()->removeCache('catalog_rules_dirty');
echo Mage::helper('catalogrule')->__('The rules have been applied.');		
*/
?>		