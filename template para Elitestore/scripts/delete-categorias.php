<?php
ob_end_clean();
require_once "../app/Mage.php";
Mage::app();
umask(0);

Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$storesview = array(2,4,6,8,10,12);
$rutaArchivo="../magmi/files/temporal/";
$nombreArchivo = "eliminar-categorias.csv";
$fp = fopen ( $rutaArchivo.$nombreArchivo , "r" );
$i = 0;


while(($data=fgetcsv($fp,1000,";")) !== false){
    if($i>0){
		var_dump($data);
        echo ' borramos la categoria '. $data[0];
        flush();
       
		if (Mage::getModel("catalog/category")->load($data[0])){
			Mage::getModel("catalog/category")->load($data[0])->delete(); 
		}else{
			echo 'La categoría '.$data[0].' no existe';
		}
        echo "<hr>";
        flush();
    }
    $i++;
    
}
?>