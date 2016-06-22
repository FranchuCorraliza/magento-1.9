<?php
/****
*
*  Autor  : Guillermo de Cáceres
*  Cliente: ELITE STORE
*  Fecha  : 01-03-2014 
*  Notas  : 
*			DELETE PRODUCTS  mysql -u elite_dev -pepsi0506xx elitestore_dev </var/www/vhosts/elitestore.es/dev/magmi/delete_all_productos.txt
*			SE EJECUTA CADA ENPUNTO Y CADA Y CUARTO
*
*****/
	set_time_limit(0); 
	ini_set('memory_limit', '2048M');
	
	//import MAGMI
	require_once("./inc/magmi_defs.php");
	require_once("./integration/inc/magmi_datapump.php"); 

	//import MAGE
	require_once "../app/Mage.php";
	Mage::app();
	umask(0);
		
inicio(); //star program

function inicio(){
	
	Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID); 
	
	try{
		$inicio = microtime(true);
		//ASEGURARSE DE LOS PERMISOS DEL DIRECTORIO 777 /magmi/files/
		$dir = "./files/";
		$dir_processing = $dir.'processing_update/';
		$dir_processed  = $dir.'processed/';
	
	
		//READ FOLDER
		$files = scandir($dir_processing);
		$files = array_diff(scandir($dir_processing), array('..', '.'));
		foreach($files as $file){
				
				echo "<br/>Procesamos ".$dir_processing.$file."<br>"; 
				
				//import elitestore ES
				//$importacion 		= importar_fichero($dir_processing,$file);
				
				//import FOTOS
				//$importacion_fotos	= importar_fotos($dir_processing,$file);
				
				//import to english version EN
				//$importacion_english= importar_extra($dir_processing,$file,'en');
				
				//import to outlet version ES
				//$importacion_outlet	= importar_extra($dir_processing,$file,'e_stock_es');
				
				//import to outlet version EN
				//$importacion_outlet	= importar_extra($dir_processing,$file,'e_stock_en'); 
				
				//control de stock
				$control_stock 		= importar_stock($dir_processing,$file);
				
				//move file to processed folder
				rename($dir_processing.$file,$dir_processed.date(now())."-".$file) or die(alertOnError("Unable to rename ".$dir_processing.$file." to ".$dir_processed.$file));
				
		}

		//REINDEX DEACTIVATED BY DEFAULT
			try{
				if($importacion>=1):
					reindex(); 
				endif;
			} catch (Exception $ex){ 
				Mage::log("ERROR REINDEX -> ".$ex->getMessage(), null, 'direct-sql-'.date("Ymd").'.log');
				alertOnError("REINDEX",$ex->getMessage());
			} 
	
	
		$final = microtime(true);
		$segundos= round($final- $inicio,4);
		$minutos= round(($final- $inicio) / 60,4);
		Mage::log("Final de importacion, duración: ".$minutos." minutos  / ".$segundos." segundos", null, 'direct-sql-'.date("Ymd").'.log');


	} catch (Exception $ex){ 
			Mage::log("ERROR EN LECTURA DE FICHEROS ".$ex->getMessage(), null, 'direct-sql-'.date("Ymd").'.log');
			alertOnError("IMPORTAR CSV",$ex->getMessage());
	} 
	
	echo "<hr>Proceso finalizado"; 

}

//IMPORT FILE CREATE PRODUCTS
function importar_fichero($dir,$file){
	$file = $dir.$file; //contact folder + file
	$iniciofichero = microtime(true);
	$read  = Mage::getSingleton('core/resource')->getConnection('core_read');
	
	
	$dp = Magmi_DataPumpFactory::getDataPumpInstance("productimport"); // create a Product import Datapump using Magmi_DatapumpFactory
	$dp->beginImportSession("default","create"); // Available modes: "create" creates and updates items, "update" updates only, "xcreate creates only. || Important: for values other than "default" profile has to be an existing magmi profile 
	
	$handle = fopen($file,"r")  or die("no puedo leer el fichero ->".$file);
	$valoresLinea = array(); //donde guardaremos los datos
		
		if($handle){
			$contaLineas = 0;
			$cabecerasLinea = array();

			while (($data = fgetcsv($handle, 6000, ';')) !== FALSE) {

				if($contaLineas==0):
					$cabecerasLinea = $data;
					
					//print_r($data);
					
					array_walk($cabecerasLinea, 'quitar_dobles_comillas');
					
					//procesamos la posicion de los elementos
					$posicion_sku = array_search('sku',$cabecerasLinea);
					$posicion_url_key = array_search('url_key',$cabecerasLinea);
					$posicion_visibility = array_search('visibility',$cabecerasLinea);
					
					//configurables
					$posicion_config_attributes = array_search('config_attributes',$cabecerasLinea);
					
					//para las categorias
					$posicion_categorias   	= array_search('category_ids',$cabecerasLinea);
					$posicion_departamento 	= array_search('departamento',$cabecerasLinea);
					$posicion_seccion 		= array_search('seccion',$cabecerasLinea);
					$posicion_familia		= array_search('familia',$cabecerasLinea);
					
					$posicion_store 		= array_search('store',$cabecerasLinea);
					
					$posicion_status 		= array_search('status',$cabecerasLinea);
					$posicion_visibility	= array_search('visibility',$cabecerasLinea);
					
					$posicion_name			= array_search('name',$cabecerasLinea);
					$posicion_campo_tres	= array_search('campo_tres',$cabecerasLinea);
					
					$posicion_price			= array_search('price',$cabecerasLinea);
					$posicion_special_price	= array_search('special_price',$cabecerasLinea);
					
					$posicion_visibleweb	= array_search('visibleweb',$cabecerasLinea);
					
					$posicion_name			= array_search('name',$cabecerasLinea);
					$posicion_tipo			= array_search('type',$cabecerasLinea);
					
					$posicion_imagen		= array_search('rutaimagen',$cabecerasLinea);
					$posicion_imagen2		= array_search('campo_cuatro',$cabecerasLinea);
					$posicion_imagen3		= array_search('campo_cinco',$cabecerasLinea);
					
					//from to special price
					$posicion_special_from 	= array_search('special_from_date',$cabecerasLinea);
					$posicion_special_to 	= array_search('special_to_date',$cabecerasLinea);
					
					
					$posicion_temporada		= array_search('tipo',$cabecerasLinea);
					$posicion_qty		= array_search('qty',$cabecerasLinea);
					
					if(!is_numeric($posicion_sku)):
						exit('no tenemos posicion sku '.$posicion_sku);
						alertOnError("LECTURA FICHERO","NO TENEMOS SKU EN LAS CABECERAS");
					endif;
					
					//eliminamos la columna website que nos mandan ya que tenemos que leer de visibleweb
					$borrar_key_1 = array_search('website', $cabecerasLinea);
					unset($cabecerasLinea[$borrar_key_1]);
					
					//eliminamos la columna website que nos mandan ya que tenemos que leer de visibleweb
					$borrar_key_2 = array_search('store', $cabecerasLinea);
					unset($cabecerasLinea[$borrar_key_2]);

				else:
					$contaCampos = 0;
					
					foreach($data as $valor){
						if($contaCampos == $posicion_sku):
							echo "</br>".$contaLineas.".-";
							echo "sku ->".$valor."</br>";
						endif;
						
						//$item[$cabecerasLinea[$contaCampos]] = $valor; //asigno los valores a sus cabeceras por linea	
						
						$contaCampos++; //increment position of line
					}
						//reasignamos o recalculamos valores
						$item['sku'] 				= $data[$posicion_sku];
						$item['qty']=$data[$posicion_qty];
						$stock = $data[$posicion_qty];
						echo "stock=".$stock."</br>";
						if($stock!='.00'):
							$item['is_in_stock'] = "1";
						else:
							$item['is_in_stock'] = "0";
						endif;
						echo "is_in_stock:".$item['is_in_stock']."</br>";
						//$item['news_from_date']		= getFechaNovedad($sku); //@jesus esto viene en el fichero porque se recalcula????
						//$item['news_to_date']		= getFechaFinNovedad($sku); //@jesus esto viene en el fichero porque se recalcula????
						$dto 		= $data[$posicion_departamento];
						$seccion 	= $data[$posicion_seccion];
						$familia 	= $data[$posicion_familia];
						$item['weight']				= getPeso($dto,$seccion,$familia);


						$item['status'] 			= getEstado($data[$posicion_status]);
						$item['visibility'] 		= getVisibilidad($data[$posicion_visibility]); 
						//$item['qty'] 		= getVisibilidad($data[$posicion_qty]); 
						//$item['price'] 				= $data[$posicion_price];
						//$item['special_price'] 		= getPrecioEspecial($data[$posicion_price],$data[$posicion_special_price]);
						//$item['special_from_date'] 	= getFechaReal($data[$posicion_special_from]);
						//$item['special_to_date']   	= getFechaReal($data[$posicion_special_to]);
						
						try{
							print_r($item);
							echo "<hr>";
							$dp->ingest($item); //insert item
							unset($item); 		//clean item
							
						} catch (Exception $ex){ 
							alertOnError("CREAR ITEM",$ex->getMessage());
						}
						
				endif;
			
				 
				
				$contaLineas++;		//increment line
			} //end while bucle for line
			fclose($handle);		//close file
		}

		$finalfichero 	= microtime(true);
		$segundosfichero= round($finalfichero- $iniciofichero,4);
		$minutosfichero = round(($finalfichero- $iniciofichero) / 60,4);
		
		Mage::log("Final de importacion fichero ".$name.", duración: ".$minutosfichero." minutos  / ".$segundosfichero." segundos", null, 'direct-sql.log');
		
		
		unset($id_producto);
		unset($skus);
		unset($sku);
		
		
			
	// End import Session
	$dp->endImportSession();

	return "1";

}

function importar_fotos($dir,$file){
	$file = $dir.$file; //contact folder + file
	
	$dp = Magmi_DataPumpFactory::getDataPumpInstance("productimport"); // create a Product import Datapump using Magmi_DatapumpFactory
	$dp->beginImportSession("default","create"); // Available modes: "create" creates and updates items, "update" updates only, "xcreate creates only. || Important: for values other than "default" profile has to be an existing magmi profile 
	
	$handle = fopen($file,"r")  or die("no puedo leer el fichero ->".$file);
	$procesados = array();
	$almacen	= array();

	$item['sku'] = ""; //empty for first element
		if($handle){
			$contaLineas = 0;
			$cabecerasLinea = array();

			while (($data = fgetcsv($handle, 6000, ';')) !== FALSE) {
				
				if($contaLineas==0):
					$cabecerasLinea = $data;
					array_walk($cabecerasLinea, 'quitar_dobles_comillas');
					$posicion_sku = array_search('sku',$cabecerasLinea);
					$posicion_imagen		= array_search('rutaimagen',$cabecerasLinea);
					$posicion_imagen2		= array_search('campo_cuatro',$cabecerasLinea);
					$posicion_imagen3		= array_search('campo_cinco',$cabecerasLinea);
				else:
					//https://www.blinkdata.com/magmi-image-attributes-processor/
					$item['sku'] = $data[$posicion_sku];
					
					
					
					$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$data[$posicion_sku]);
					
					$mediaApi = Mage::getModel("catalog/product_attribute_media_api");
		    		
					if($product):
						$imagenes = $mediaApi->items($product->getId(),1);
						
						if(count($imagenes)==0){ //procesamos imagenes si el producto no las tiene asociadas
							
							if(!in_array($item['sku'],$procesados)):
								$procesados[] = $item['sku'];
						
								$almacen[$item['sku']][] 	= str_replace("/media/import","",$data[$posicion_imagen]);
								$almacen[$item['sku']][] 	= str_replace("/media/import","",$data[$posicion_imagen]); //"+/prueba.jpg";
								$almacen[$item['sku']][] 	= str_replace("/media/import","",$data[$posicion_imagen]);
						
								if($data[$posicion_imagen2]!=''):
									$almacen[$item['sku']][] = str_replace("/media/import","",$data[$posicion_imagen2]);
								endif;
						
								if($data[$posicion_imagen3]!=''):
									$almacen[$item['sku']][] = str_replace("/media/import","",$data[$posicion_imagen3]);
								endif;
							else:
								$almacen[$item['sku']][] =str_replace("/media/import","",$data[$posicion_imagen]);
							endif;
						} else {
							echo "<br>No proceso imagenes para ->".$item['sku']; 
						}
					else:
						echo "<br>Producto no existe";
					endif; //si producto exist
						
				endif;
				
				$contaLineas++;		//increment line
			} //end while bucle for line
			
			
				$cadena_fotos = "";
				
				//$almacen =  array_reverse($almacen);  //ultima modificacion para la ordenacion de las fotos de los configurables
				
				foreach ($almacen as $clave => $valor){
   					$item['sku'] = $clave; 
   					$valor = array_unique($valor);
   					
   					$ultima_foto = count($valor)-1;
   					
   					$contafotos = 0;
   					$contagalery = 0;
   					foreach($valor as $fotos){
   						if($contafotos==$ultima_foto): //ordenamos las fotos DESC
   							$item['image']		= "+".$fotos;
							$item['small_image']= "+".$fotos;
							$item['thumbnail']	= "+".$fotos;
   						else:
   							if($contagalery==0):
	   							$item['media_gallery']= $fotos.";";
	   						elseif($contagalery==1):
								$item['media_gallery'].= $fotos."::back;";	   						
	   						endif;
   							$contagalery++;
   						endif; 
   						
   						
   						//limpiamos sino viene nada
   						if($item['media_gallery']=='::back;'){
   							unset($item['media_gallery']);
   						}
   						
   						$item['media_gallery_reset'] = "0"; 
   						$contafotos++;
   					}

					//reordeno media galery
					$cadena = explode(";",$item['media_gallery']);
					$cadena = array_reverse($cadena);
					//echo $cadena; 
					
					$item['media_gallery'] = implode(";",$cadena);

					print_r($item);
					
   					$dp->ingest($item);
   					
					unset($item);
										
				}
				

			
			fclose($handle);		//close file
		}
		
		
	// End import Session
	$dp->endImportSession();
	echo "fin de importar fotos";
	return "1";
	
}

function importar_extra($dir,$file,$store_id){

	$file = $dir.$file; //contact folder + file
	$iniciofichero = microtime(true);
	

	$read  = Mage::getSingleton('core/resource')->getConnection('core_read');
	
	
	$dp = Magmi_DataPumpFactory::getDataPumpInstance("productimport"); // create a Product import Datapump using Magmi_DatapumpFactory
	$dp->beginImportSession("default","create"); // Available modes: "create" creates and updates items, "update" updates only, "xcreate creates only. || Important: for values other than "default" profile has to be an existing magmi profile 

	$handle = fopen($file,"r")  or die("no puedo leer el fichero ->".$file);
	$valoresLinea = array(); //donde guardaremos los datos
	
	
		if($handle){
			$contaLineas = 0;
			$cabecerasLinea = array();

			while (($data = fgetcsv($handle, 6000, ';')) !== FALSE) {
				if($contaLineas==0):
					$cabecerasLinea = $data;
					array_walk($cabecerasLinea, 'quitar_dobles_comillas');

					//procesamos la posicion de los elementos
					$posicion_sku = array_search('sku',$cabecerasLinea);
					$posicion_campo_uno		=  array_search('campo_uno',$cabecerasLinea);
					$posicion_campo_seis	=  array_search('campo_seis',$cabecerasLinea);
					$posicion_status 		= array_search('status',$cabecerasLinea);
					$posicion_stock_price	=  array_search('stock_price',$cabecerasLinea);
					$posicion_price			=  array_search('price',$cabecerasLinea);
					$posicion_special_stockprice	=  array_search('special_stockprice',$cabecerasLinea);
					$posicion_visibleweb	= array_search('visibleweb',$cabecerasLinea);
					
					if(!is_numeric($posicion_sku)):
						exit('no tenemos posicion sku '.$posicion_sku);
						alertOnError("LECTURA FICHERO","NO TENEMOS SKU EN LAS CABECERAS");
					endif;
				
				else:
					//Añadimos los datos especificos para ingles
					$item['sku']				= $data[$posicion_sku];
					$item['status'] 			= getEstado($data[$posicion_status]);
					$store_asociados = getTiendas($item['sku'],$data[$posicion_visibleweb]);
					
					if($data[$posicion_visibleweb]=='F' and ($store_id=='e_stock_es' or $store_id=='e_stock_en')):
						continue;
					endif;
					
					//echo "<br>queremos (".$data[$posicion_visibleweb].") ".$store_asociados;
					//echo "<br>Paso tienda->".	
					$item['store']		  		= $store_id; //aqui el codigo de la vista de tienda
					
				
					//solo tiendas en ingles
					if(strstr($store_id,'en')){
					
						$item['name'] 				= $data[$posicion_campo_uno];
						$item['description']		= $data[$posicion_campo_uno];
						$item['url_key'] 			= getUrl($data[$posicion_campo_uno]);
					
						$item['meta_title']			= $data[$posicion_campo_uno];
						$item['meta_keyword']		= str_replace(' ',',',strtolower($data[$posicion_campo_uno]));
						$item['meta_description']	= $data[$posicion_campo_seis];
						$item['short_description']	= $data[$posicion_campo_seis];
					}
					
					if($store_id=='e_stock_es'){
						$item['special_price'] 		= getPrecioEspecial($data[$posicion_price],$data[$posicion_special_stockprice]);
						
						$item['news_from_date']		= getFechaNovedad($sku); //@jesus esto viene en el fichero porque se recalcula????
						$item['news_to_date']		= getFechaFinNovedad($sku); //@jesus esto viene en el fichero porque se recalcula????
					}
						
					try{
						print_r($item);
						echo "<hr>";
						$dp->ingest($item); //insert item
						unset($item); 		//clean item
					} catch (Exception $ex){ 
						alertOnError("CREAR ITEM INGLES",$ex->getMessage());
					} 
					
				endif;
				
				$contaLineas++;		//increment line
			} //end while bucle for line
			fclose($handle);		//close file
		}

		$finalfichero 	= microtime(true);
		$segundosfichero= round($finalfichero- $iniciofichero,4);
		$minutosfichero = round(($finalfichero- $iniciofichero) / 60,4);
		
		Mage::log("Final de importacion store_id.".$store_id." ->fichero ".$name.", duración: ".$minutosfichero." minutos  / ".$segundosfichero." segundos", null, 'direct-sql-'.date("Ymd").'.log');
		
		
		unset($id_producto);
		unset($skus);
		unset($sku);
		
		
			
	// End import Session
	$dp->endImportSession();

	return 1;

}

function importar_stock($dir,$file){
	$file = $dir.$file; //contact folder + file
	$iniciofichero = microtime(true);
	$read  = Mage::getSingleton('core/resource')->getConnection('core_read');
	

	$dp = Magmi_DataPumpFactory::getDataPumpInstance("productimport"); // create a Product import Datapump using Magmi_DatapumpFactory
	$dp->beginImportSession("default","create"); // Available modes: "create" creates and updates items, "update" updates only, "xcreate creates only. || Important: for values other than "default" profile has to be an existing magmi profile 
	
	$handle = fopen($file,"r")  or die("no puedo leer el fichero ->".$file);
	$valoresLinea = array(); //donde guardaremos los datos
	
	$acumulado_skus = array(); //repiten skus asi que solo procesamos 1 vez por item para no marear el proceso
		
		if($handle){
			$contaLineas = 0;
			$cabecerasLinea = array();

			while (($data = fgetcsv($handle, 6000, ';')) !== FALSE) {

				if($contaLineas==0):
					$cabecerasLinea = $data;
					
					array_walk($cabecerasLinea, 'quitar_dobles_comillas');
					
					//procesamos la posicion de los elementos
					$posicion_sku 		= array_search('sku',$cabecerasLinea);
					$posicion_type		= array_search('type',$cabecerasLinea);
					$posicion_associated= array_search('associated',$cabecerasLinea);

					if(!is_numeric($posicion_sku)):
						exit('<br>no tenemos posicion sku ->'.$posicion_sku.print_r($data));
						alertOnError("LECTURA FICHERO","NO TENEMOS SKU EN LAS CABECERAS");
					endif;
					
				else:
						if($data[$posicion_type]=='configurable'):
							$item['sku'] = $data[$posicion_sku];
							
							if(!in_array($item['sku'],$acumulado_skus)):
								$acumulado_skus[]=$item['sku'];							
								
								$almacen=0; //ponemos a 0 el stock
								$cadena_skus = getAssociated($item['sku'], $data[$posicion_associated]);
								$skus	     = explode(",",$cadena_skus);
								
								echo "tengo ".count($skus)." hijos <br>";
								
								if(count($skus)>0):
									foreach($skus as $sku){
										try{
											$_product 	= Mage::getModel('catalog/product')->loadByAttribute('sku',$sku);
											if($_product):
												$stock 		= Mage::getModel('cataloginventory/stock_item')->loadByProduct($_product);
												$almacen 	= $stock->getQty() + $almacen;
											endif; 
										} catch (Exception $ex){}
									}
								else:
									$almacen = 0;
								endif;
							
								if($almacen >0):
									$item['is_in_stock'] = "1";
								else:
									$item['is_in_stock'] = "0";
								endif;
								
								
								try{
									print_r($item);
									echo "<hr>";
									$dp->ingest($item); //insert item 
									unset($item); 		//clean item
								} catch (Exception $ex){ 
									alertOnError("CREAR ITEM",$ex->getMessage());
								}
							
							endif;
							
						endif;
						
				endif;
			
				$contaLineas++;		//increment line
			
			} //end while bucle for line
			fclose($handle);		//close file
		
		}

	// End import Session
	$dp->endImportSession();

	return true;
}

#
# FUNCTION SPECIFIC FROM ELITESTORE
#
//return mapping from categories
function getCategorias($sku,$departamento,$seccion,$familia){
			$catalogo[1][1][1]=array(3,23,24,25);
			$catalogo[1][1][2]=array(3,23,24,26);
			$catalogo[1][1][3]=array(3,23,24,27);
			$catalogo[1][1][4]=array(3,23,24,36);
			$catalogo[1][1][5]=array(3,23,24,30);
			$catalogo[1][1][6]=array(3,23,24,35);
			$catalogo[1][1][7]=array(3,23,24,31);
			$catalogo[1][1][8]=array(3,23,24,29);
			$catalogo[1][1][9]=array(3,23,24,37);
			$catalogo[1][1][10]=array(3,23,24,38);
			$catalogo[1][1][11]=array(3,23,24,155);
			$catalogo[1][1][12]=array(3,23,24,32);	
			$catalogo[1][1][13]=array(3,23,24,28);
			$catalogo[1][1][14]=array(3,23,24,40);
			$catalogo[1][1][15]=array(3,23,24,33);
			$catalogo[1][1][16]=array(3,23,24,42);
			$catalogo[1][1][17]=array(3,23,24,44);
			$catalogo[1][2][1]=array(3,23,45,49);
			$catalogo[1][2][2]=array(3,23,45,47);
			$catalogo[1][2][3]=array(3,23,45,46);
			$catalogo[1][2][4]=array(3,23,45,54);
			$catalogo[1][2][5]=array(3,23,45,53);
			$catalogo[1][2][6]=array(3,23,45,48);
			$catalogo[1][2][7]=array(3,23,45,156);
			$catalogo[1][2][8]=array(3,23,45,57);
			$catalogo[1][3][1]=array(3,23,59,60);
			$catalogo[1][3][2]=array(3,23,59,176);
			$catalogo[1][3][3]=array(3,23,59,177);
			$catalogo[1][3][4]=array(3,23,59,178);
			$catalogo[1][3][5]=array(3,23,59,179);
			$catalogo[1][3][6]=array(3,23,59,180);
			$catalogo[1][3][7]=array(3,23,59,181);
			$catalogo[1][3][8]=array(3,23,59,182);
			$catalogo[1][4][1]=array(3,23,61,152);
			$catalogo[1][4][2]=array(3,23,61,183);
			$catalogo[1][4][3]=array(3,23,61,184);
			$catalogo[1][4][4]=array(3,23,61,185);
			$catalogo[1][4][5]=array(3,23,61,186);
			$catalogo[1][4][6]=array(3,23,61,187);
			$catalogo[1][4][7]=array(3,23,61,188);
			$catalogo[1][4][8]=array(3,23,61,189);
			$catalogo[1][4][9]=array(3,23,61,190);
			$catalogo[1][5][1]=array(3,23,206,207,158,159);
			$catalogo[1][5][2]=array(3,23,206,208,158,159);
			$catalogo[1][5][3]=array(3,23,206,209,158,159);
			$catalogo[1][5][4]=array(3,23,206,210,158,159);
			$catalogo[1][5][5]=array(3,23,206,211,158,159);
			$catalogo[1][5][6]=array(3,23,206,212,158,159);
			$catalogo[1][5][7]=array(3,23,206,213,158,159);
			$catalogo[1][5][8]=array(3,23,206,214,158,159);
			$catalogo[2][1][1]=array(3,62,63,64);
			$catalogo[2][1][2]=array(3,62,63,65);
			$catalogo[2][1][3]=array(3,62,63,66);
			$catalogo[2][1][4]=array(3,62,63,67);
			$catalogo[2][1][5]=array(3,62,63,68);
			$catalogo[2][1][6]=array(3,62,63,70);
			$catalogo[2][1][7]=array(3,62,63,71);
			$catalogo[2][1][8]=array(3,62,63,69);
			$catalogo[2][1][9]=array(3,62,63,72);
			$catalogo[2][1][10]=array(3,62,63,73);
			$catalogo[2][1][11]=array(3,62,63,77);
			$catalogo[2][2][1]=array(3,62,78,84);
			$catalogo[2][2][2]=array(3,62,78,81);
			$catalogo[2][2][3]=array(3,62,78,80);
			$catalogo[2][2][4]=array(3,62,78,87);
			$catalogo[2][2][5]=array(3,62,78,82);
			$catalogo[2][2][6]=array(3,62,78,83);
			$catalogo[2][2][7]=array(3,62,78,153);
			$catalogo[2][2][8]=array(3,62,78,90);
			$catalogo[2][2][9]=array(3,62,78,79);
			$catalogo[2][3][1]=array(3,62,92,93);
			$catalogo[2][3][2]=array(3,62,92,191);
			$catalogo[2][3][3]=array(3,62,92,192);
			$catalogo[2][3][4]=array(3,62,92,193);
			$catalogo[2][3][5]=array(3,62,92,194);
			$catalogo[2][4][1]=array(3,62,94,95);
			$catalogo[2][4][2]=array(3,62,94,195);
			$catalogo[2][4][3]=array(3,62,94,196);
			$catalogo[2][4][4]=array(3,62,94,197);
			$catalogo[2][4][5]=array(3,62,94,198);
			$catalogo[2][4][6]=array(3,62,94,199);
			$catalogo[2][4][7]=array(3,62,94,200);
			$catalogo[2][4][8]=array(3,62,94,201);
			$catalogo[2][5][1]=array(3,62,215,216,158,160);
			$catalogo[2][5][2]=array(3,62,215,217,158,160);
			$catalogo[2][5][3]=array(3,62,215,218,158,160);
			$catalogo[2][5][4]=array(3,62,215,219,158,160);
			$catalogo[2][5][5]=array(3,62,215,220,158,160);
			$catalogo[2][5][6]=array(3,62,215,221,158,160);
			$catalogo[2][5][7]=array(3,62,215,222,158,160);
			$catalogo[2][5][8]=array(3,62,215,223,158,160);
			$catalogo[4][1][1]=array(3,101,171,102);
			$catalogo[4][1][4]=array(3,101,171,120);
			$catalogo[4][1][5]=array(3,101,171,121);
			$catalogo[4][1][6]=array(3,101,171,116);
			$catalogo[4][1][7]=array(3,101,171,115);
			$catalogo[4][1][8]=array(3,101,171,119);
			$catalogo[4][1][9]=array(3,101,171,112);
			$catalogo[4][1][10]=array(3,101,171,167);
			$catalogo[4][1][11]=array(3,101,171,168);
			$catalogo[4][1][12]=array(3,101,171,111);
			$catalogo[4][1][13]=array(3,101,171,110);
			$catalogo[4][1][14]=array(3,101,171,117);
			$catalogo[4][1][15]=array(3,101,171,113);
			$catalogo[4][1][16]=array(3,101,171,118);
			$catalogo[4][2][1]=array(3,101,224,226,171,105);
			$catalogo[4][2][2]=array(3,101,224,227,171,105);
			$catalogo[4][3][1]=array(3,101,225,228,171,104);
			$catalogo[4][3][2]=array(3,101,225,229,171,104);
			$catalogo[4][4][1]=array(3,101,106,169);
			$catalogo[4][4][2]=array(3,101,106,170);
			$catalogo[4][4][3]=array(3,101,106,108);
			$catalogo[4][4][4]=array(3,101,106,109);
			$catalogo[4][4][5]=array(3,101,106,205);
			$catalogo[4][5][1]=array(3,101,96,164);
			$catalogo[4][5][2]=array(3,101,96,97);
			$catalogo[4][5][3]=array(3,101,96,166);
			$catalogo[4][5][4]=array(3,101,96,165);
			$catalogo[4][5][5]=array(3,101,96,99);
			$catalogo[7][1][1]=array(3,122,123,124);
			$catalogo[7][1][2]=array(3,122,123,125);
			$catalogo[7][1][3]=array(3,122,123,126);
			$catalogo[7][1][4]=array(3,122,123,127);
			$catalogo[7][1][5]=array(3,122,123,128);
			$catalogo[7][1][6]=array(3,122,123,129);
			$catalogo[7][1][7]=array(3,122,123,130);
			$catalogo[7][1][8]=array(3,122,123,132);
			$catalogo[7][1][9]=array(3,122,123,133);
			$catalogo[7][1][10]=array(3,122,123,134);
			$catalogo[7][1][11]=array(3,122,123,135);
			$catalogo[7][1][12]=array(3,122,123,136);
			$catalogo[7][1][13]=array(3,122,123,137);
			$catalogo[7][1][14]=array(3,122,123,138);
			$catalogo[7][1][15]=array(3,122,123,140);
			$catalogo[7][2][1]=array(3,122,142,144);
			$catalogo[7][2][2]=array(3,122,142,145);
			$catalogo[7][3][1]=array(3,122,148,149);
			$catalogo[7][4][1]=array(3,122,150,151);


    if ($departamento && $seccion && $familia){	
			$categorias=$catalogo[$departamento][$seccion][$familia];

		}
		$catExist = CategoriesFromSku($sku);
		echo 'cats_exits=';
		print_r ($catExist);
		if (!empty($catExist)){
			$categorias = array_merge($categorias, $catExist);
			$categorias = array_unique($categorias);
		}
		 foreach($categorias as $categor):
			$cats .= $categor.",";
		 endforeach;
		 
		 $cats = substr($cats,0,-1);
		 echo 'cats='.$cats;
		return $cats;

		
}
function CategoriesFromSku($sku){
	try{
		$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku);
		if($product){
			$categories = $product->getCategoryIds();
			return $categories;
		} else {
			return;
		}
	} catch (Exception $ex){
		alertOnError('sacando categorias de producto '.$sku,$ex->getMessage());
	}
}
// Devuelve la fecha de hoy si es la primera vez que se importa dicho artículo @TODO REVISAR CON JESUS
function getFechaNovedad($sku){
	$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku);
	if(!$product){  //Si no existia antes
		$fecha_final = Mage::getModel('core/date')->timestamp(time()); //revisar esta fecha esta dado algo raro @JESUS
	}else{
		$fecha_final = $product->getData('news_from_date');
	}
	$fecha_final = date('Y-m-d', $fecha_final); //formato magento
	return $fecha_final;
}
// Devuelve la fecha de dentro de 7 dias si es la primera vez que se importa dicho artículo
function getFechaFinNovedad($sku){
	$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku);	
	if(!$product){  //Si no existia antes
		$fecha_final = Mage::getModel('core/date')->timestamp(time() + (2 * 24 * 60 * 60));;
	}else{
		$fecha_final = $product->getData('news_to_date');
	}
	$fecha_final = date('Y-m-d', $fecha_final); //formato magento
	return $fecha_final;
}
function getEstado($status){
	if($status == 'Deshabilitado'):
		return 2;
	else:
		return 1;
	endif;
}
function getVisibilidad($visibility){
	if($visibility == 'Not Visible Individually'):
		return 1;
	else:
		return 4;
	endif;
}
// Devuelve el valor "campo_tres" o "name" si éste no existe
function getDescripcionCorta($descripcion,$nombre){
	if($descripcion!=''){
		return $descripcion;
	}else{
		return $nombre;
	}
}
// Devuelve el precio especial si es distinto de 0 e inferior al precio
function getPrecioEspecial($precio,$precioespecial){
	if (($precioespecial!='.00') && ((int)$precioespecial<(int)$precio)):
		return $precioespecial;
	else:
		return '';	
	endif;
}
// Devuelve el peso correspondiente en función de la categoría.
function getPeso($departamento,$seccion,$familia){
	$catalogo[1][2][5]='0,48';
	$catalogo[1][5][3]='0,48';
	$catalogo[1][5][4]='0,48';
	$catalogo[1][5][7]='0,48';
	$catalogo[4][1][4]='0,48';
	$catalogo[4][3][1]='0,48';
	$catalogo[4][3][2]='0,48';
	$catalogo[4][4][1]='0,48';
	$catalogo[4][4][2]='0,48';
	$catalogo[1][1][1]='0,68';
	$catalogo[1][1][2]='0,68';
	$catalogo[1][1][3]='0,68';
	$catalogo[1][1][5]='0,68';
	$catalogo[1][1][10]='0,68';
	$catalogo[1][1][11]='0,68';
	$catalogo[1][1][13]='0,68';
	$catalogo[1][1][14]='0,68';
	$catalogo[1][2][1]='0,68';
	$catalogo[1][2][3]='0,68';
	$catalogo[1][2][4]='0,68';
	$catalogo[1][2][6]='0,68';
	$catalogo[1][2][7]='0,68';
	$catalogo[1][2][8]='0,68';
	$catalogo[1][5][1]='0,68';
	$catalogo[1][5][2]='0,68';
	$catalogo[1][5][5]='0,68';
	$catalogo[1][5][6]='0,68';
	$catalogo[1][5][8]='0,68';
	$catalogo[2][1][1]='0,68';
	$catalogo[2][1][2]='0,68';
	$catalogo[2][1][3]='0,68';
	$catalogo[2][1][5]='0,68';
	$catalogo[2][1][9]='0,68';
	$catalogo[2][1][10]='0,68';
	$catalogo[2][2][1]='0,68';
	$catalogo[2][2][3]='0,68';
	$catalogo[2][2][4]='0,68';
	$catalogo[2][2][5]='0,68';
	$catalogo[2][2][6]='0,68';
	$catalogo[2][2][7]='0,68';
	$catalogo[2][2][8]='0,68';
	$catalogo[2][2][9]='0,68';
	$catalogo[2][5][1]='0,68';
	$catalogo[2][5][2]='0,68';
	$catalogo[2][5][3]='0,68';
	$catalogo[2][5][4]='0,68';
	$catalogo[2][5][5]='0,68';
	$catalogo[2][5][6]='0,68';
	$catalogo[2][5][7]='0,68';
	$catalogo[2][5][8]='0,68';
	$catalogo[4][1][1]='0,68';
	$catalogo[4][1][5]='0,68';
	$catalogo[4][1][6]='0,68';
	$catalogo[4][1][7]='0,68';
	$catalogo[4][1][10]='0,68';
	$catalogo[7][1][1]='0,68';
	$catalogo[7][1][2]='0,68';
	$catalogo[7][1][3]='0,68';
	$catalogo[7][1][4]='0,68';
	$catalogo[7][1][5]='0,68';
	$catalogo[7][1][6]='0,68';
	$catalogo[7][1][7]='0,68';
	$catalogo[7][1][8]='0,68';
	$catalogo[7][1][12]='0,68';
	$catalogo[7][1][13]='0,68';
	$catalogo[1][1][8]='2';
	$catalogo[1][2][2]='2';
	$catalogo[2][1][4]='2';
	$catalogo[2][2][2]='2';
	$catalogo[4][1][11]='2';
	$catalogo[4][1][15]='2';
	$catalogo[4][1][16]='2';
	$catalogo[4][2][1]='2';
	$catalogo[4][2][2]='2';
	$catalogo[4][4][3]='2';
	$catalogo[7][1][14]='2';
	$catalogo[7][1][15]='2';
	$catalogo[7][2][1]='2';
	$catalogo[7][2][2]='2';
	$catalogo[7][4][1]='2';
	$catalogo[1][1][15]='2,6';
	$catalogo[1][3][5]='2,6';
	$catalogo[1][3][6]='2,6';
	$catalogo[1][4][3]='2,6';
	$catalogo[1][4][4]='2,6';
	$catalogo[1][4][5]='2,6';
	$catalogo[1][4][6]='2,6';
	$catalogo[1][4][7]='2,6';
	$catalogo[1][4][8]='2,6';
	$catalogo[2][3][3]='2,6';
	$catalogo[2][3][4]='2,6';
	$catalogo[2][4][4]='2,6';
	$catalogo[2][4][5]='2,6';
	$catalogo[2][4][6]='2,6';
	$catalogo[2][4][7]='2,6';
	$catalogo[2][4][8]='2,6';
	$catalogo[4][1][8]='2,6';
	$catalogo[4][1][9]='2,6';
	$catalogo[4][4][5]='2,6';
	$catalogo[7][3][1]='2,6';
	$catalogo[1][1][16]='3,6';
	$catalogo[1][3][1]='3,6';
	$catalogo[1][3][7]='3,6';
	$catalogo[1][3][8]='3,6';
	$catalogo[2][3][1]='3,6';
	$catalogo[4][1][12]='2';
	$catalogo[4][1][13]='3,6';
	$catalogo[4][1][14]='3,6';
	$catalogo[1][1][7]='5,4';
	$catalogo[1][1][17]='5,4';
	$catalogo[1][3][2]='5,4';
	$catalogo[1][3][3]='5,4';
	$catalogo[1][3][4]='5,4';
	$catalogo[1][4][1]='5,4';
	$catalogo[1][4][2]='5,4';
	$catalogo[1][4][9]='5,4';
	$catalogo[2][1][11]='5,4';
	$catalogo[2][3][2]='5,4';
	$catalogo[2][3][5]='5,4';
	$catalogo[2][4][1]='5,4';
	$catalogo[2][4][2]='5,4';
	$catalogo[2][4][3]='5,4';
	$catalogo[4][4][4]='5,4';
	$catalogo[7][1][9]='5,4';
	$catalogo[1][1][4]='6';
	$catalogo[1][1][6]='6';
	$catalogo[2][1][6]='6';
	$catalogo[7][1][10]='6';
	$catalogo[1][1][9]='9,6';
	$catalogo[1][1][12]='9,6';
	$catalogo[2][1][7]='9,6';
	$catalogo[2][1][8]='9,6';
	$catalogo[4][5][1]='9,6';
	$catalogo[4][5][2]='9,6';
	$catalogo[4][5][3]='9,6';
	$catalogo[4][5][4]='9,6';
	$catalogo[4][5][5]='9,6';
	$catalogo[7][1][11]='9,6';
	$weight='5';
	if ($departamento && $seccion && $familia){	
		$weight=$catalogo[$departamento][$seccion][$familia];		
	}
	return $weight;
}
// Devuelve los ids de las tiendas a las que debe ir dicho artículo en función del campo "Visibleweb"
function getTiendas($sku,$visibleweb){
	$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku);
	/*if($product){
		$tiendas = $product->getStoreIds();
		if(count($tiendas)>1):
			$write  = Mage::getSingleton('core/resource')->getConnection('core_write');
			$delete_stores = "delete from `catalog_product_website`  where product_id=".$product->getId();
			$write->query($delete_stores);
		endif;
	} 
	*/
	if ($visibleweb=='T'){
		$websites = "e_stock_es";
	}elseif ($visibleweb=='F'){
		$websites = "es";
		/*	if($product){
				$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku);
				$tiendas = $product->getStoreIds();
				if($sku=='PRUEBA44BIANCOTTIC'):
					echo "--- tengo -->".count($tiendas)."<<<-<br>";
				endif;
				if (count($tiendas)>2){ //En el caso en que se hubiese puesto en las dos tiendas desde magento.
					$websites = "es,e_stock_es";
				}else{
					$websites= "es";
				}
			} else {
				$websites= "es"; //añadido nuevo no tengo producto es nuevo y no estaba asignado @todo: preguntar como funcionaba esto 
			}					
		*/
	//}else{
		//$websites=array(1,2); //e_stock
		//$websites = "es,e_stock_es";
	}
	
	return $websites;
}
// @todo revisar con fotos reale CON JESUS // Devuelve la ruta correspondiente a la imagen si procede subirla
function getImagenes($product, $importData){
	$foto1=$importData['rutaimagen'];
	$foto2=$importData['campo_cuatro'];
	$foto3=$importData['campo_cinco'];
	$ruta = Mage :: getBaseDir( 'media' ) ."/". 'import' . str_replace("/media/import","",$foto1);
	$ruta = strtolower($ruta);
	$finrutaimagen = substr($importData['rutaimagen'],strlen(strtolower($foto1))-4);
	$is_file = ((file_exists($ruta)) && $finrutaimagen=='.jpg');

	$ruta2 = Mage :: getBaseDir( 'media' ) ."/". 'import' . str_replace("/media/import","",$foto2);
	$ruta2 = strtolower($ruta2);			
	$finrutaimagen2 = substr($importData['campo_cuatro'],strlen(strtolower($foto2))-4);
	$is_file2 = ((file_exists($ruta2)) && $finrutaimagen2=='.jpg'); 			

	$ruta3 = Mage :: getBaseDir( 'media' ) ."/". 'import' . str_replace("/media/import","",$foto3);
	$ruta3 = strtolower($ruta3);			
	$finrutaimagen3 = substr($importData['campo_cinco'],strlen(strtolower($foto3))-4);
	$is_file3 = ((file_exists($ruta3)) && $finrutaimagen3=='.jpg');
			
	if ($is_file){
		$subir= false;
		if ($is_file2 && $is_file3){
			if (!($product->getMediaGalleryImages()) || ($product -> getMediaGalleryImages() -> getSize()<3)){
				$subir=true;
			}
		}else if ($is_file2 && (!$is_file3)){
			if (!($product->getMediaGalleryImages()) || ($product -> getMediaGalleryImages() -> getSize()<2)){
				$subir=true;
			}
		}else if ((!$is_file2) && (!$is_file3)){
			if (!($product->getMediaGalleryImages()) || ($product -> getMediaGalleryImages() -> getSize()<1)){
				$subir=true;
			}
		}
	}
	if ($subir){
		return $ruta;	
	}else{
		return '';
	}
}
//create URL key correct
function getUrl($url,$type = NULL,$visibility = NULL,$sku = NULL){
	if($type=='simple' and $visibility==1):
		$url = $url."-".$sku;
	endif;
	$find = array('á', 'é', 'í', 'ó', 'ú', 'ñ');
	$repl = array('a', 'e', 'i', 'o', 'u', 'n');
	$url = str_replace ($find, $repl, trim(strtolower($url)));
	$find = array(' ', '&', '\r\n', '\n', '+'); 
	$url = str_replace ($find, '-', $url);
	$find = array('/[^a-z0-9\-<>]/', '/[\-]+/', '/<[^>]*>/');
	$repl = array('', '-', '');
	$url = preg_replace ($find, $repl, $url);
	return $url;
}
//transform correct format date 31/12/50  => 2050-13-31
function getFechaReal($date){
	$dia 	= substr($date,0,2);
	$mes 	= substr($date,3,2);
	$anno 	= "20".substr($date,6,2);
	return $anno."-".$mes."-".$dia;
}
//RETRIEVE INFO ATT
function att($att,$type){
	try{
		$read = Mage::getSingleton('core/resource')->getConnection('core_read');		
		$query ="SELECT attribute_id from eav_attribute as eav WHERE eav.entity_type_id = ".$type." AND eav.attribute_code = '".$att."'";
		$attributo = $read->fetchOne($query);
		return $attributo;
	}catch(Exception $ex){
		alertOnError("ATRIBUTO ID",$ex->getMessage());
	}
}
//REINDEX PROCESS
function reindex(){
	$inicioReindex = microtime(true); 
	
	if(date("i")>0 and date("i")<16):
		$reindexar[] = 'catalog_product_price';
		$reindexar[] = 'catalog_product_attribute';
		$reindexar[] = 'cataloginventory_stock';
		$reindexar[] = 'catalog_category_product';
		$reindexar[] = 'catalog_category_flat';
		$reindexar[] = 'catalog_product_flat';
	else:
		$reindexar[] = 'catalog_product_price';
		$reindexar[] = 'cataloginventory_stock';
	endif;
	//$reindexar[] = 'catalog_url';

	for($i=0;$i<sizeof($reindexar);$i++):
		try{
			exec('php ../shell/indexer.php -reindex '.$reindexar[$i]);
		} catch (Exception $ex){
			Mage::log("No Puedo reindexar: ".$ex->getMessage(), null, 'direct-sql-'.date("Ymd").'.log');
		}
	endfor;
	
	$finalReindex = microtime(true);
	$segundosReindex= round($finalReindex- $inicioReindex,4);
	$minutosReindex= round(($finalReindex- $inicioReindex) / 60,4);
	Mage::log("Reindexado duración: ".$minutosReindex." minutos  / ".$segundosReindex." segundos", null, 'direct-sql-'.date("Ymd").'.log');

}
//SEND ERROR TO IT
function alertOnError($process,$error){
	exit($error);
		try{
			$mail = new Zend_Mail();
			$mail->setFrom('gcaceres@magento-spain.com','MG SPAIN')
			->addTo('gcaceres@magentos-spain.com','MG SPAIN')
			->setSubject($_SERVER['HOSTNAME'].'- Error proceso de importacion '.date("Y-m-d H:i:s"))
			->setBodyText('Sección: '.$process.'<br>Detalle: <br>'.$error)
			->send();	
		} catch (Exception $ex){
				Mage::log("No Puedo enviar email: ".$ex->getMessage(), null, 'direct-sql-'.date("Ymd").'.log');
		}
}
//QUITAR QUOTES
function quitar_dobles_comillas(&$value){
	$value = str_replace('"','',trim($value,$character_mask = " \t\n\r\0\x0B"));
	$value = trim($value,chr(0xC2).chr(0xA0));
	$value = trim($value,chr(173));
}

//productos asociados
function getAssociated($sku,$ids_csv){
	Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
		
	$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku);
	//echo "cargo ".$sku."--".$product->getName()."<br>";
	
    if($product){
		$childProducts  = $product->getTypeInstance()->getUsedProductIds();
		
		//echo "tengo ".count($childProducts);
		
		foreach($childProducts as $child_id){
			$product_child = Mage::getModel('catalog/product')->load($child_id);
			if($product_child->getSku()!=''):
				$ids_csv.= ",".$product_child->getSku();
			endif;
			unset($product_child);
		}
		
		//pasamos a array
		$items 		= explode(",",$ids_csv);
		$items 		= array_unique($items);
		$ids_csv 	= implode(",",$items);
    }
    
   	return $ids_csv; //contact news and olds products ids

}

//reordenamos la posicion de los atributos configurables
function reorderConfigAttributes($cadena){
	$items 	= explode(",",$cadena);
	$items 	= array_reverse($items);
	$items 	= implode(",",$items);
	return $items;
}