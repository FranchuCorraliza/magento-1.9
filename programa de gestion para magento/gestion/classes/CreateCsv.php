<?php 

class CreateCsv{
	
	
	public function insertarStockColorYPesoEnCsv($data,$file,$filedir){
		$header = array("sku","qty","is_in_stock","color","weight");
		$fp = fopen($filedir.'/'.$file, "w");
		
	    fputcsv ($fp, $header, ";","\"");
	    foreach($data as $row){
			$item=array($row['sku'],$row['qty'],$row['is_in_stock'],$row['color'],$this->getPeso($row['DPTO'],$row['SECCION'],$row['FAMILIA']));
			fputcsv($fp, $item, ";","\"");
	    }
	    fclose($fp);
	
	
	}
	
	function getPeso($departamento,$seccion,$familia){
	$catalogo[1][1][1]='0,68';
	$catalogo[1][1][2]='0,68';
	$catalogo[1][1][3]='0,68';
	$catalogo[1][1][4]='6';
	$catalogo[1][1][5]='0,68';
	$catalogo[1][1][6]='6';
	$catalogo[1][1][7]='5,4';
	$catalogo[1][1][8]='2';
	$catalogo[1][1][9]='9,6';
	$catalogo[1][1][10]='0,68';
	$catalogo[1][1][11]='0,68';
	$catalogo[1][1][12]='9,6';
	$catalogo[1][1][13]='0,68';
	$catalogo[1][1][14]='0,68';
	$catalogo[1][1][15]='2,6';
	$catalogo[1][1][16]='3,6';
	$catalogo[1][1][17]='5,4';
	$catalogo[1][2][1]='0,68';
	$catalogo[1][2][2]='2';
	$catalogo[1][2][3]='0,68';
	$catalogo[1][2][4]='0,68';
	$catalogo[1][2][5]='0,48';
	$catalogo[1][2][6]='0,68';
	$catalogo[1][2][7]='0,68';
	$catalogo[1][2][8]='0,68';
	$catalogo[1][3][1]='3,6';
	$catalogo[1][3][2]='5,4';
	$catalogo[1][3][3]='5,4';
	$catalogo[1][3][4]='5,4';
	$catalogo[1][3][5]='2,6';
	$catalogo[1][3][6]='2,6';
	$catalogo[1][3][7]='3,6';
	$catalogo[1][3][8]='3,6';
	$catalogo[1][4][1]='5,4';
	$catalogo[1][4][2]='5,4';
	$catalogo[1][4][3]='2,6';
	$catalogo[1][4][4]='2,6';
	$catalogo[1][4][5]='2,6';
	$catalogo[1][4][6]='2,6';
	$catalogo[1][4][7]='2,6';
	$catalogo[1][4][8]='2,6';
	$catalogo[1][4][9]='5,4';
	$catalogo[1][5][1]='0,68';
	$catalogo[1][5][2]='0,68';
	$catalogo[1][5][3]='0,48';
	$catalogo[1][5][4]='0,48';
	$catalogo[1][5][5]='0,68';
	$catalogo[1][5][6]='0,68';
	$catalogo[1][5][7]='0,48';
	$catalogo[1][5][8]='0,68';
	$catalogo[2][1][1]='0,68';
	$catalogo[2][1][2]='0,68';
	$catalogo[2][1][3]='0,68';
	$catalogo[2][1][4]='2';
	$catalogo[2][1][5]='0,68';
	$catalogo[2][1][6]='6';
	$catalogo[2][1][7]='9,6';
	$catalogo[2][1][8]='9,6';
	$catalogo[2][1][9]='0,68';
	$catalogo[2][1][10]='0,68';
	$catalogo[2][1][11]='5,4';
	$catalogo[2][2][1]='0,68';
	$catalogo[2][2][2]='2';
	$catalogo[2][2][3]='0,68';
	$catalogo[2][2][4]='0,68';
	$catalogo[2][2][5]='0,68';
	$catalogo[2][2][6]='0,68';
	$catalogo[2][2][7]='0,68';
	$catalogo[2][2][8]='0,68';
	$catalogo[2][2][9]='0,68';
	$catalogo[2][3][1]='3,6';
	$catalogo[2][3][2]='5,4';
	$catalogo[2][3][3]='2,6';
	$catalogo[2][3][4]='2,6';
	$catalogo[2][3][5]='5,4';
	$catalogo[2][4][1]='5,4';
	$catalogo[2][4][2]='5,4';
	$catalogo[2][4][3]='5,4';
	$catalogo[2][4][4]='2,6';
	$catalogo[2][4][5]='2,6';
	$catalogo[2][4][6]='2,6';
	$catalogo[2][4][7]='2,6';
	$catalogo[2][4][8]='2,6';
	$catalogo[2][5][1]='0,68';
	$catalogo[2][5][2]='0,68';
	$catalogo[2][5][3]='0,68';
	$catalogo[2][5][4]='0,68';
	$catalogo[2][5][5]='0,68';
	$catalogo[2][5][6]='0,68';
	$catalogo[2][5][7]='0,68';
	$catalogo[2][5][8]='0,68';
	$catalogo[4][1][1]='0,68';
	$catalogo[4][1][4]='0,48';
	$catalogo[4][1][5]='0,68';
	$catalogo[4][1][6]='0,68';
	$catalogo[4][1][7]='0,68';
	$catalogo[4][1][8]='2,6';
	$catalogo[4][1][9]='2,6';
	$catalogo[4][1][10]='0,68';
	$catalogo[4][1][11]='2';
	$catalogo[4][1][12]='1';
	$catalogo[4][1][13]='3,6';
	$catalogo[4][1][14]='3,6';
	$catalogo[4][1][15]='2';
	$catalogo[4][1][16]='2';
	$catalogo[4][2][1]='2';
	$catalogo[4][2][2]='2';
	$catalogo[4][3][1]='0,48';
	$catalogo[4][3][2]='0,48';
	$catalogo[4][4][1]='0,48';
	$catalogo[4][4][2]='0,48';
	$catalogo[4][4][3]='2';
	$catalogo[4][4][4]='5,4';
	$catalogo[4][4][5]='2,6';
	$catalogo[4][5][1]='9,6';
	$catalogo[4][5][2]='9,6';
	$catalogo[4][5][3]='9,6';
	$catalogo[4][5][4]='9,6';
	$catalogo[4][5][5]='9,6';
	$catalogo[4][6][1]='0,68';
	$catalogo[4][6][2]='0,68';
	$catalogo[4][6][3]='0,68';
	$catalogo[6][1][1]='0,68';
	$catalogo[6][1][2]='0,68';
	$catalogo[6][1][3]='0,68';
	$catalogo[6][1][4]='0,68';
	$catalogo[6][1][5]='0,68';
	$catalogo[6][1][6]='0,68';
	$catalogo[6][1][7]='0,68';
	$catalogo[6][1][8]='0,68';
	$catalogo[6][1][9]='5,4';
	$catalogo[6][1][10]='6';
	$catalogo[6][1][11]='9,6';
	$catalogo[6][1][12]='0,68';
	$catalogo[6][1][13]='0,68';
	$catalogo[6][1][14]='2';
	$catalogo[6][1][15]='2';
	$catalogo[6][2][1]='2';
	$catalogo[6][2][2]='2';
	$catalogo[6][3][1]='2,6';
	$catalogo[6][4][1]='2';
	$catalogo[7][1][1]='0,68';
	$catalogo[7][1][2]='0,68';
	$catalogo[7][1][3]='0,68';
	$catalogo[7][1][4]='0,68';
	$catalogo[7][1][5]='0,68';
	$catalogo[7][1][6]='0,68';
	$catalogo[7][1][7]='0,68';
	$catalogo[7][1][8]='0,68';
	$catalogo[7][1][9]='5,4';
	$catalogo[7][1][10]='6';
	$catalogo[7][1][11]='9,6';
	$catalogo[7][1][12]='0,68';
	$catalogo[7][1][13]='0,68';
	$catalogo[7][1][14]='2';
	$catalogo[7][1][15]='2';
	$catalogo[7][2][1]='2';
	$catalogo[7][2][2]='2';
	$catalogo[7][3][1]='2,6';
	$catalogo[7][4][1]='2';	
	$weight='5';
	
	if ($departamento && $seccion && $familia && isset($catalogo[$departamento][$seccion][$familia])){	
		$weight=$catalogo[$departamento][$seccion][$familia];		
	}
	return $weight;
}
	public function insertarStocksEnCsv($data,$file,$filedir){
		$header = array("sku","qty","is_in_stock");
		$fp = fopen($filedir.'/'.$file, "w");
	    fputcsv ($fp, $header, ";","\"");
	    foreach($data as $row){
			$item=array($row['sku'],$row['qty'],$row['is_in_stock']);
			fputcsv($fp, $item, ";","\"");
	    }
	    fclose($fp);
	
	
	}
	public function insertarStocksConfEnCsv($data,$file,$filedir){
		$header = array("sku","is_in_stock");
		$fp = fopen($filedir.'/'.$file, "w");
	    fputcsv ($fp, $header, ";","\"");
	    foreach($data as $row){
			$item=array($row['sku'],$row['is_in_stock']);
			fputcsv($fp, $item, ";","\"");
	    }
	    fclose($fp);
	
	
	}
	public function insertarStocksYColorConfEnCsv($data,$file,$filedir){
		$header = array("sku","color_simple","is_in_stock");
		$fp = fopen($filedir.'/'.$file, "w");
	    fputcsv ($fp, $header, ";","\"");
	    foreach($data as $row){
			$item=array($row['sku'],$row['color_simple'],$row['is_in_stock']);
			fputcsv($fp, $item, ";","\"");
	    }
	    fclose($fp);
	
	
	}
	
	public function insertarBaseEnCsv($data,$file,$filedir){
		$header = array("sku","name","description","composicion","tallaje","modelo","outlet","sale","unisex","home_pic","designer_pic","designer_line","estilismo","tags","runway","category_ids","manufacturer","order_by_request","color","temporada","tipo","codarticulo");
		$fp = fopen($filedir.'/'.$file, "w");
	    fputcsv ($fp, $header, ";","\"");
	    foreach($data as $row){
			$item=array($row['sku'],$row['name'],$row['description'],$row['composicion'],$row['tallaje'],$row['modelo'],$row['outlet'],$row['sale'],$row['unisex'],$row['home_pic'],$row['designer_pic'],$row['designer_line'],$row['estilismo'],$row['tags'],$row['runway'],$row['category_ids'],$row['manufacturer'],$row['order_by_request'],$row['color'],$row['temporada'],$row['tipo'],$row['codarticulo']);
			fputcsv($fp, $item, ";","\"");
	    }
	    fclose($fp);
	}
	
	public function insertarPrecioEnCsv($data,$file,$filedir){
		$header = array("sku","price","special_to_date","special_from_date","special_price");
		$fp = fopen($filedir.'/'.$file, "w");
	    fputcsv ($fp, $header, ";","\"");
	    foreach($data as $row){
			$item=array($row['sku'],$row['price'],$row['special_to_date'],$row['special_from_date'],$row['special_price']);
			fputcsv($fp, $item, ";","\"");
	    }
	    fclose($fp);
	}
	
	public function insertarInglesEnCsv($data,$file,$filedir){
		$storesIngles=array("en_as","en_zh","en_eu","en","en_oc","en_us_ca");
		$header = array("store","sku","name","description");
		$fp = fopen($filedir.'/'.$file, "w");
	    fputcsv ($fp, $header, ";","\"");
		foreach ($storesIngles as $store){
			foreach($data as $row){
				$item=array($store,$row['sku'],$row['name'],$row['description']);
				fputcsv($fp, $item, ";","\"");
			}
		}
	    fclose($fp);
	}
	
	public function insertarNuevosEnCsv($codarticulos,$file,$fileDescripciones,$filedir,$conn){
		$header = array("store","website","has_options","type","attribute_set","status","visibility","sku","name","description","talla","composicion","tallaje","modelo","outlet","sale","unisex","home_pic","designer_pic","designer_line","estilismo","tags","runway","category_ids","manufacturer","order_by_request","color","temporada","tipo","codarticulo","configurable_attributes","qty","tax_class_id","price","special_from_date","special_to_date","special_price","image","small_image","thumbnail","media_gallery","video_url","is_in_stock","fecha_subida","codbarras","weight","color_simple");
		$fp = fopen($filedir.'/'.$file, "w");
	    fputcsv ($fp, $header, ";","\"");
		
		$codigos=array();
		foreach($codarticulos as $codarticulo){
			if (!in_array($codarticulo['codarticulo'],$codigos)){
				$codigos[]=$codarticulo['codarticulo'];
			}
		}
		$codigos=implode(',',$codigos);
		$articulos=$conn->getArticulos($codigos);
		$this->insertarArticulos($articulos,$fp);
		fclose($fp);
		//Crear fichero actualizaciones
		$data=$conn->getDescripcionesIngles($codigos);
		$this->insertarInglesEnCsv($data,$fileDescripciones,$filedir);
	}
	
	public function insertarArticulos($articulos,$fp){
		$is_in_stockConfigurable=0;
		foreach ($articulos as $key=>$articulo){
			if ($articulo['image']!=''){
				if($articulo['qty']>0){
						$is_in_stock=1;
						$is_in_stockConfigurable=1;
					}else{
						$is_in_stock=0;
					}
					$item=array('admin','base','0','simple','Default','1','1',$articulo['sku'],$articulo['name'],'__MAGMI_IGNORE__',$articulo['talla'],'__MAGMI_IGNORE__','__MAGMI_IGNORE__','__MAGMI_IGNORE__','__MAGMI_IGNORE__','__MAGMI_IGNORE__','__MAGMI_IGNORE__','__MAGMI_IGNORE__','__MAGMI_IGNORE__','__MAGMI_IGNORE__','__MAGMI_IGNORE__','__MAGMI_IGNORE__','__MAGMI_IGNORE__','__MAGMI_IGNORE__','__MAGMI_IGNORE__','__MAGMI_IGNORE__','__MAGMI_IGNORE__','__MAGMI_IGNORE__','__MAGMI_IGNORE__','__MAGMI_IGNORE__','__MAGMI_IGNORE__',$articulo['qty'],'__MAGMI_IGNORE__','__MAGMI_IGNORE__','__MAGMI_IGNORE__','__MAGMI_IGNORE__','__MAGMI_IGNORE__','__MAGMI_IGNORE__','__MAGMI_IGNORE__','__MAGMI_IGNORE__','__MAGMI_IGNORE__','__MAGMI_IGNORE__',$is_in_stock,$articulo['fecha_subida'],$articulo['codbarras'],$this->getPeso($articulo['dpto'],$articulo['seccion'],$articulo['familia']),$articulo['color_simple']);
					fputcsv($fp, $item, ";","\"");
				if ($articulo['codarticulo']!=$articulos[$key+1]['codarticulo']){ // Si el siguiente artículo tiene un código de articulo distinto del actual
					//Controlar si fecha especial es correcta y precio espcial es correcto, si no no rellenamos nada
					$specialFromDate=$articulo['special_from_date'];
					$specialToDate=$articulo['special_to_date'];
					$specialPrice=$articulo['special_price'];
					if ($specialPrice=='0.00' || $specialPrice=='0' || $specialPrice==0){
						$specialPrice='';
					}
					$item=array('admin','base','1','configurable','Default','1','4',$articulo['sku_configurable'],$articulo['name'],$articulo['description'],'__MAGMI_IGNORE__',$articulo['composicion'],$articulo['tallaje'],$articulo['modelo'],$articulo['outlet'],$articulo['sale'],$articulo['unisex'],$articulo['home_pic'],$articulo['designer_pic'],$articulo['designer_line'],$articulo['estilismo'],$articulo['tags'],$articulo['runway'],$articulo['category_ids'],$articulo['manufacturer'],$articulo['order_by_request'],$articulo['color'],$articulo['temporada'],$articulo['tipo'],$articulo['codarticulo'],'talla','__MAGMI_IGNORE__','6',$articulo['price'],$specialFromDate,$specialToDate,$specialPrice,'+'.$articulo['image'],'+'.$articulo['small_image'],'+'.$articulo['thumbnail'],$articulo['media_gallery'],$articulo['video_url'],$is_in_stockConfigurable,$articulo['fecha_subida'],$articulo['codbarras'],'__MAGMI_IGNORE__',$articulo['color_simple']);
					$is_in_stockConfigurable=0;
					fputcsv($fp, $item, ";","\"");
				}
			}
		}
	}
	public function insertarImagenesEnCsv($data,$file,$filedir,$conn){
		$header = array("sku","image","small_image","thumbnail","media_gallery");
		$fp = fopen($filedir.'/'.$file, "w");
	    fputcsv ($fp, $header, ";","\"");
	    foreach($data as $row){
			$item=$conn->getImagenes($sku);
			$item=array($item['sku'],$row['image'],$row['small_image'],$row['thumbnail'],$row['media_gallery']);
			fputcsv($fp, $item, ";","\"");
	    }
	    fclose($fp);
	}
	
	public function getImagenes($sku){
		
	}
	public function insertarCategorias($tabla){
		$header = array("DPTO","SEC","FAM","SALE","OUTLET","CATEGORY","ID");
		$fp = fopen('temp/categorias.csv', "w");
	    fputcsv ($fp, $header, ";","\"");
	    foreach($tabla as $row){
			$item=array($row['DPTO'],$row['SEC'],$row['FAM'],$row['SALE'],$row['OUTLET'],$row['CATEGORY'],$row['ID']);
			fputcsv($fp, $item, ";","\"");
	    }
	    fclose($fp);
	}
	
	public function insertarObr($tabla){
		$header = array("MANUFACTURER","OBR","ID");
		$fp = fopen('temp/obr.csv', "w");
	    fputcsv ($fp, $header, ";","\"");
	    foreach($tabla as $row){
			$item=array($row['MANUFACTURER'],$row['OBR'],$row['ID']);
			fputcsv($fp, $item, ";","\"");
	    }
	    fclose($fp);
	}
}