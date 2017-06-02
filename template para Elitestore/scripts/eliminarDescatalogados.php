<?php
require_once "../app/Mage.php";
Mage::app();
umask(0);
ob_end_clean();


$dir = "../magmi/files/";
$dir_processing = $dir.'processing/';
$fileName="eliminar.csv";
try{
	$file = $dir_processing.$fileName;
	echo "<hr>Iniciamos rastreo de skus<hr>";
	flush();
	if(file_exists ($file)){
		
		$handle = fopen($file,"r")  or die("no puedo leer el fichero ->".$file);
		if($handle){
			$contaLineas = 0;
			$cabecerasLinea = array();
			while (($data = fgetcsv($handle, 6000, ';')) !== FALSE) {
				if($contaLineas==0):
					$cabecerasLinea = $data;
					$posicion_sku = array_search('sku',$cabecerasLinea);
				else:
					$contaCampos = 0;
					foreach($data as $valor){
						$item[$cabecerasLinea[$contaCampos]] = $valor; //asigno los valores a sus cabeceras por linea	
						$contaCampos++;
					}
					try{
						$sku=$data[$posicion_sku];
						$skus[]=$sku;
					} catch (Exception $ex){ 
						echo "<br>Error: $ex->getMessage()";
					}
				endif;
				$contaLineas++;
			} //end while bucle for line
				fclose($handle);		//close file
		}
		echo "<hr>Rastreo finalizado<hr>";
		flush();
		echo "<hr>Generamos Collección de items<hr>";
		echo "<hr>Recorremos los skus<hr>";
		$collection= Mage::getResourceModel('catalog/product_collection')->addAttributeToSelect('*')->addAttributeToFilter( 'sku', array( 'nin' => $skus ) );
		Mage::register('isSecureArea', true);
		foreach($collection as $product){
			echo $product->getSku()."-->";
			$product = Mage::getModel('catalog/product')->load($product->getId());
			if($product) {
				try {
					$product->delete();
					echo " Deleted<br>";
				} catch (Exception $e) {
					echo " Not Deleted<br>";
					var_dump($e);
					echo ("<br/>");
				}
			} else {
				echo " Not Available<br>";
			}
		}
		Mage::unregister('isSecureArea');
	}else{
		echo "<hr>Error<hr>";
	}
}catch(Exception $e){
	echo "<hr>Error en la importación<hr>";
	var_dump($e);
	
} 

