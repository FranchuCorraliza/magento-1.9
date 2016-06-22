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
		$id_grupo= "26";
		$customer = Mage::getModel('customer/customer')->setWebsiteId(Mage::app()->getStore()->getWebsiteId())->loadByEmail($email);
		if($customer->getId()):
			echo "Cliente ".$customer->getId()."<br/>";
			if (($customer->getGroup_id()==1) || ($customer->getGroup_id()==13)):
				try{
					echo "El cliente con email $email ha sido cambiado al grupo $id_grupo<br>";
					$customer->setGroupId($id_grupo)->save();
                    Mage::log('cliente' . $email . '<br/>', null, 'mylogfile.log');
				 } catch (Exception $ex){
					exit($ex->getMessage());
				 }
				 return $customer->getId() ; 
				 unset($customer);
			else:
				echo "El cliente con email $email pertenece al grupo".$customer->getGroup_id()."<br>";	
			endif;
		else:
			echo "El cliente con email $email no existe<br>";	
        endif;
}

?>