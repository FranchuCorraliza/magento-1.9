<?php 
set_time_limit(0);
class MGS_Fastimport_Model_Manual{

    public function importar(){
		
	/*	echo "cron";
	} 
	
	
	public function actualizarstock(){
	*/
		//primero eliminamos el bom
		//exec("tail --bytes=+4 /var/www/vhosts/elitestore.es/httpdocs/var/import/ftp/articulos.csv > /var/www/vhosts/elitestore.es/httpdocs/var/import/ftp/articulos-nobom.csv");
	
 		//importante para los updates
 		Mage::setIsDeveloperMode(true);
        Mage::app()->setCurrentStore(Mage::getModel('core/store')->load(Mage_Core_Model_App::ADMIN_STORE_ID));
        
        
		//leemos el fichero de la carpeta y lo procesamos
		//ASEGURARSE DE LOS PERMISOS DEL DIRECTORIO 777 /magmi/files/
		//$proyect = "/elite/";
		//$dir_files = $_SERVER["DOCUMENT_ROOT"].$proyect."var/import/ftp/";
		$dir_files = $_SERVER["DOCUMENT_ROOT"]."/var/import/ftp/";
		$dir_files = $_SERVER["DOCUMENT_ROOT"]."/var/import/";

		//READ FOLDER
		$files = scandir($dir_files);

		$files = array_diff(scandir($dir_files), array('..', '.'));
		foreach($files as $file){
				//Mage::log("trabajamos con ".$id_erp_file."---".$_FILES['file']['name'], null, 'direct-sql.log');

				//if($file=='articulos-nobom.csv'):
					echo "Procesamos -> ".$file."<br>";
					$importacion = $this->importar_fichero($dir_files.$file,$file);
				//endif;
			
				//rename files para no procesar mas de una vez
				/*
				if($importacion == true):
					rename($dir_processing.$file,$dir_processed.$file) or die(alertOnError("Unable to rename ".$dir_processing.$file." to ".$dir_processed.$file));
				else:
					rename($dir_processing.$file,$dir_processed."ERROR-".$file) or die(alertOnError("Unable to rename ".$dir_processing.$file." to ".$dir_processed."ERROR-".$file));			
				endif;
				*/
		}
		
		
		exit('termino lectura');
 	}
 	
 
 	
 	//IMPORT FILE CREATE PRODUCTS
	private function importar_fichero($file,$name){
	
	$read = Mage::getSingleton('core/resource')->getConnection('core_read');
	
	$handle = fopen($file,"r")  or die("no puedo leer el fichero ->".$file);
	
	$valoresLinea = array(); //donde guardaremos los datos
		
		if($handle){
			$contaLineas = 0;
			$cabecerasLinea = array();

			while (($data = fgetcsv($handle, 6000, ';')) !== FALSE) {
			
				if($contaLineas==0):
					$cabecerasLinea = $data;
					$posicion_sku = array_search('sku',$cabecerasLinea);
					$posicion_stock = array_search('qty',$cabecerasLinea);
					//if(!is_numeric($posicion_sku)):
					//	Mage::log("No tenemos Sku en el fichero ".$file, null, 'direct-stock.log');
					//	return false;
					//endif;
				else:
					$datos = explode(",",$data[0]);
					//exit($datos[0]."<---->".$datos[1]);
					
					$sku = $datos[0];
					$stock = $datos[1];
					$product_id = $read->fetchOne("select entity_id from catalog_product_entity where sku='".str_replace("'","",$sku)."'");
 	 		
				 	if($product_id!='' and $stock!=''):
				 	 	$this->_updateProductQty ($product_id,$stock);
					endif;
						
				endif;
				$contaLineas++;		//increment line
				
				
			}
			fclose($handle);		//close file
			
		}
			return true;
	}

		
 	
 	
 	
 	
 	 private function _updateProductQty($product_id,$new_quantity) {
	 	$write = Mage::getSingleton('core/resource')->getConnection('core_write');
		
		$dia = date("Ymd");
		$cuando = date("Ymd_His");
		
		Mage::log("Actualizamos ".$cuando." ID: ".$product_id. " Qty: ".$new_quantity, null, 'direct-stock-'.$dia.'.log',true); 
		
	 	try{
			$write->query ( "UPDATE cataloginventory_stock_item item_stock, cataloginventory_stock_status status_stock
    	   SET item_stock.qty = '$new_quantity', item_stock.is_in_stock = IF('$new_quantity'>0, 1,0),
	       status_stock.qty = '$new_quantity', status_stock.stock_status = IF('$new_quantity'>0, 1,0)
    	   WHERE item_stock.product_id = '$product_id' AND item_stock.product_id = status_stock.product_id " );
	    	return true;
	    } catch (Exception $ex){
	    	Mage::log("ERROR ITEM: ".$ex->getMessage()." -> ".$product_id, null, 'direct-stock.log');
	    }
 	}

	
}
		