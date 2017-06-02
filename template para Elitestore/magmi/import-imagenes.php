<?php
require_once("./inc/magmi_defs.php");
require_once("./integration/inc/magmi_datapump.php"); 
require_once "../app/Mage.php";
Mage::app();
umask(0);


$dir = "./files/";
$dir_processing = $dir.'processing/';
$fileName="imagenes.csv";
try{
	$file = $dir_processing.$fileName;
	
	if(file_exists ($file)){
		$dp = Magmi_DataPumpFactory::getDataPumpInstance("productimport"); // create a Product import Datapump using Magmi_DatapumpFactory
		$dp->beginImportSession("default","update"); // Available modes: "create" creates and updates items, "update" updates only, "xcreate creates only. || Important: for values other than "default" profile has to be an existing magmi profile 
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
						$dp->ingest($item); //insert item 
						unset($item); 		//clean item
					} catch (Exception $ex){ 
						echo "<br>Error: $ex->getMessage()";
					}
				endif;
				$contaLineas++;
			} //end while bucle for line
				fclose($handle);		//close file
		}
		$dp->endImportSession();
		echo "<hr>Importación de base Finalizada<hr>";
		$log=fopen($dir_processing."result-imagenes.log", "w");
		$txt = "1";
		fwrite($log, $txt);
		fclose($log);
	}else{
		$log=fopen($dir_processing."result-imagenes.log", "w");
		$txt = "0";
		fwrite($log, $txt);
		fclose($log);
	}
}catch(Exception $e){
	$log=fopen($dir_processing."result-imagenes.log", "w");
	$txt = "0";
	fwrite($log, $txt);
	fclose($log);
} 