<?php

require_once '../app/Mage.php';
umask(0);

Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

echo "comienzo<hr>";

$handle = @fopen("clientes-elitestore.csv", "r");

if ($handle) {
    while (($email = fgets($handle, 4096)) !== false) {
        echo "<br>-->".MyUpdate(trim($email));
    }
    
    
    
    if (!feof($handle)) {
        echo "Error: unexpected fgets() fail\n";
    }
    fclose($handle);
}

echo "<hr>fin";


function  MyUpdate($email){
		$id_grupo= "10";
		$customer = Mage::getModel('customer/customer')->setWebsiteId(Mage::app()->getStore()->getWebsiteId())->loadByEmail($email);

        if($customer->getId()):
	        try{
	         	$customer->setGroup_id($id_grupo)->save();
	         } catch (Exception $ex){
	         	exit($ex->getMessage());
	         }
	         return $customer->getId() ; 
	         unset($customer);
        endif;
}

?>