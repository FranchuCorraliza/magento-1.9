<?php

class Magestore_Manufacturer_Model_Manufacturer extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('manufacturer/manufacturer');
    }
	
	public function getFeaturedManufacturer()
	{
		$listManufacturer = array();
		$activeStoreID =  Mage::app()->getStore()->getId();
		
		$this->_featured_manufacturer_collection = Mage::getResourceModel('manufacturer/manufacturer_collection')
		->addFieldToFilter("store_id",array("=" => $activeStoreID));
		
		foreach($this->_featured_manufacturer_collection as $manufacturer)
		{
			$manufacturer = $manufacturer->loadDataManufacturer($manufacturer);
			if(($manufacturer->getFeatured() == 1) AND ($manufacturer->getStatus() == 1) )
				$listManufacturer[] = $manufacturer;
		}
		//var_dump($listManufacturer);
		return $listManufacturer;
	}
	private function getCategoriasAptas($category,$gender){
		if ($category=="clothing"){
			if ($gender=="women"){
				$categories=array(1004, 1005, 1006, 1007, 1008, 1009, 1010, 1011, 1012, 1013, 1014, 1015, 1016, 1017, 1018, 1019, 1020, 1021, 1022, 1023, 1024, 1025, 1026, 18428, 18429, 18430);
			} else if ($gender=="men"){
				$categories=array(1074, 1075, 1076, 1077, 1078, 1079, 1080, 1081, 1082, 1083, 1084, 1085, 1086, 1087, 1088, 1089, 1090, 1091);
			} else if ($gender=="kids"){	
				$categories=array(1141, 1142, 1143, 1144, 1145, 1146, 1147, 18437, 18438, 18439, 18440);
			}else{
				$categories=array(1004, 1005, 1006, 1007, 1008, 1009, 1010, 1011, 1012, 1013, 1014, 1015, 1016, 1017, 1018, 1019, 1020, 1021, 1022, 1023, 1024, 1025, 1026, 18428, 18429, 18430,1074, 1075, 1076, 1077, 1078, 1079, 1080, 1081, 1082, 1083, 1084, 1085, 1086, 1087, 1088, 1089, 1090, 1091,1141, 1142, 1143, 1144, 1145, 1146, 1147, 18437, 18438, 18439, 18440);
			}
		}else if ($category=="shoes"){
			if ($gender=="women"){
				$categories=array(1028, 1029, 1030, 1031, 1032, 1033, 1034, 1035, 1036, 1037, 1038);
			} else if ($gender=="men"){
				$categories=array(1093, 1094, 1095, 1096, 1097, 1098, 1099, 1100);
			} else if ($gender=="kids"){	
				$categories=array(1148);
			}else{
				$categories=array(1028, 1029, 1030, 1031, 1032, 1033, 1034, 1035, 1036, 1037, 1038,1093, 1094, 1095, 1096, 1097, 1098, 1099, 1100,1148);
			}
		}else if ($category=="accessories"){	
			if ($gender=="women"){
				$categories=array(1047, 1048, 1049, 1050, 1051, 1052, 1053, 1054, 1055, 1056, 18431, 1057, 1058, 1059, 1060, 18432, 18433, 1061, 1062, 1063, 1064, 1065, 1066, 18434, 1067, 1068, 1069, 1070, 1071, 18435);
			} else if ($gender=="men"){
				$categories=array(1110, 1111, 1112, 1113, 1114, 1115, 1116, 1117, 1118, 1119, 1120, 1121, 1122, 1123, 1124, 1125, 1126, 1127, 1128, 1129, 1130, 1131, 1132, 1133, 18436, 1134, 1135, 1136, 1137, 1138);
			} else if ($gender=="kids"){	
				$categories=array(1149);
			}else{
				$categories=array(1047, 1048, 1049, 1050, 1051, 1052, 1053, 1054, 1055, 1056, 18431, 1057, 1058, 1059, 1060, 18432, 18433, 1061, 1062, 1063, 1064, 1065, 1066, 18434, 1067, 1068, 1069, 1070, 1071, 18435,1110, 1111, 1112, 1113, 1114, 1115, 1116, 1117, 1118, 1119, 1120, 1121, 1122, 1123, 1124, 1125, 1126, 1127, 1128, 1129, 1130, 1131, 1132, 1133, 18436, 1134, 1135, 1136, 1137, 1138,1149);
			}
		}else if ($category=="bag"){
			if ($gender=="women"){
				$categories=array(1040, 1041, 1042, 1043, 1044, 1045);
			} else if ($gender=="men"){
				$categories=array(1102, 1103, 1104, 1105, 1106, 1107, 1108);
			} else if ($gender=="kids"){	
				$categories=array();
			}else{
				$categories=array(1040, 1041, 1042, 1043, 1044, 1045,1102, 1103, 1104, 1105, 1106, 1107, 1108);
			}
		}else{
			if ($gender=="women"){
				$categories=array(1004, 1005, 1006, 1007, 1008, 1009, 1010, 1011, 1012, 1013, 1014, 1015, 1016, 1017, 1018, 1019, 1020, 1021, 1022, 1023, 1024, 1025, 1026, 18428, 18429, 18430,1028, 1029, 1030, 1031, 1032, 1033, 1034, 1035, 1036, 1037, 1038,1047, 1048, 1049, 1050, 1051, 1052, 1053, 1054, 1055, 1056, 18431, 1057, 1058, 1059, 1060, 18432, 18433, 1061, 1062, 1063, 1064, 1065, 1066, 18434, 1067, 1068, 1069, 1070, 1071, 18435,1040, 1041, 1042, 1043, 1044, 1045);
			} else if ($gender=="men"){
				$categories=array(1074, 1075, 1076, 1077, 1078, 1079, 1080, 1081, 1082, 1083, 1084, 1085, 1086, 1087, 1088, 1089, 1090, 1091,1093, 1094, 1095, 1096, 1097, 1098, 1099, 1100,1110, 1111, 1112, 1113, 1114, 1115, 1116, 1117, 1118, 1119, 1120, 1121, 1122, 1123, 1124, 1125, 1126, 1127, 1128, 1129, 1130, 1131, 1132, 1133, 18436, 1134, 1135, 1136, 1137, 1138,1102, 1103, 1104, 1105, 1106, 1107, 1108);
			} else if ($gender=="kids"){	
				$categories=array(1141, 1142, 1143, 1144, 1145, 1146, 1147, 18437, 18438, 18439, 18440,1148,1149);
			}else{
				$categories=array(1004, 1005, 1006, 1007, 1008, 1009, 1010, 1011, 1012, 1013, 1014, 1015, 1016, 1017, 1018, 1019, 1020, 1021, 1022, 1023, 1024, 1025, 1026, 18428, 18429, 18430, 1028, 1029, 1030, 1031, 1032, 1033, 1034, 1035, 1036, 1037, 1038, 1040, 1041, 1042, 1043, 1044, 1045, 1047, 1048, 1049, 1050, 1051, 1052, 1053, 1054, 1055, 1056, 18431, 1057, 1058, 1059, 1060, 18432, 18433, 1061, 1062, 1063, 1064, 1065, 1066, 18434, 1067, 1068, 1069, 1070, 1071, 18435, 1074, 1075, 1076, 1077, 1078, 1079, 1080, 1081, 1082, 1083, 1084, 1085, 1086, 1087, 1088, 1089, 1090, 1091, 1093, 1094, 1095, 1096, 1097, 1098, 1099, 1100, 1102, 1103, 1104, 1105, 1106, 1107, 1108, 1110, 1111, 1112, 1113, 1114, 1115, 1116, 1117, 1118, 1119, 1120, 1121, 1122, 1123, 1124, 1125, 1126, 1127, 1128, 1129, 1130, 1131, 1132, 1133, 18436, 1134, 1135, 1136, 1137, 1138, 1141, 1142, 1143, 1144, 1145, 1146, 1147, 18437, 18438, 18439, 18440);
			}
		}
		return $categories;
	}	
	/*
	public function _getManufacturersInStock($category,$gender)
	{
		Mage::log("Category:$category",null,"manufacturer.log");
		Mage::log("Genero:$gender",null,"manufacturer.log");
		
		$inStockProducts = Mage::getResourceModel('catalog/product_collection')
		->joinField('category_id','catalog/category_product','category_id','product_id=entity_id',null,'left')
		->addAttributeToFilter('category_id', array('in' => $this->getCategoriasAptas($category,$gender)))
		->addAttributeToFilter('type_id', Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) //Tipo Simple
		->addStoreFilter(Mage::app()->getStore()->getId())
		->addAttributeToSelect('*');
		$inStockProducts->load();
		Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($inStockProducts);
		
		$inStockProducts2 = Mage::getResourceModel('catalog/product_collection')
		->addAttributeToFilter('type_id', Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) //Tipo Simple
		->addStoreFilter(Mage::app()->getStore()->getId())
		->addAttributeToSelect('*');
		$inStockProducts->load();
		Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($inStockProducts2);
		$manufacturers_in_stock = array();
		Mage::log("Con Filtros",null,"manufacturer.log");
		foreach($inStockProducts as $product){
			if (!in_array($product->getData('manufacturer'),$manufacturers_in_stock)){ //si no esta en el listado de marcas
				$manufacturers_in_stock[]=$product->getData('manufacturer'); //insertamos marca del articulo con stock
				Mage::log($product->getAttributeText('manufacturer'),null,"manufacturer.log");
			}
		}
		Mage::log("Sin Filtros",null,"manufacturer.log");
		$manufacturers_in_stock2 = array();
		foreach($inStockProducts2 as $product){
			if (!in_array($product->getData('manufacturer'),$manufacturers_in_stock2)){ //si no esta en el listado de marcas
				$manufacturers_in_stock2[]=$product->getData('manufacturer'); //insertamos marca del articulo con stock
				Mage::log($product->getAttributeText('manufacturer'),null,"manufacturer.log");
			}
		}
		return $manufacturers_in_stock;
	}
	*/
	public function getManufacturers($category,$gender,$letter){
		
		$categories=implode(',',$this->getCategoriasAptas($category,$gender));
		$storeId=Mage::app()->getStore()->getId();
		
		$query="SELECT DISTINCT IFNULL(manufacturer_label.value, manufacturer_default.name) as name, 
					manufacturer_default.url_key as url_key, 
					manufacturer_default.newdesigner as newdesigner,
					(CASE
						WHEN
							(SUM(`at_inventory_in_stock`.is_in_stock)>0)
						THEN
							1
						ELSE
							0
						END) as in_stock
					
				FROM `catalog_product_entity` AS `e` 
						LEFT JOIN `catalog_category_product` AS `at_category_id` 
							ON (at_category_id.`product_id`=e.entity_id) 
						INNER JOIN `catalog_product_entity_int` AS `at_manufacturer` 
							ON (`at_manufacturer`.`entity_id` = `e`.`entity_id`) 
								AND (`at_manufacturer`.`attribute_id` = '187') 
								AND (`at_manufacturer`.`store_id` = 0) 
						LEFT JOIN manufacturer AS manufacturer_default
							ON (manufacturer_default.store_id = 0 
							AND manufacturer_default.option_id = at_manufacturer.value) 
						LEFT JOIN eav_attribute_option_value AS manufacturer_label
							ON (manufacturer_label.store_id = 1
							AND manufacturer_label.option_id = at_manufacturer.value)
						INNER JOIN `cataloginventory_stock_item` AS `at_inventory_in_stock` 
							ON (at_inventory_in_stock.`product_id`=e.entity_id) 
					WHERE (at_category_id.category_id IN (".$categories.")) 
						AND (`e`.`type_id` = 'configurable') 
						AND (manufacturer_default.status = 1)
					GROUP BY manufacturer_label.value,manufacturer_default.name,manufacturer_default.url_key,manufacturer_default.newdesigner";
		
		
		
		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');
		
		/**
		 * Execute the query and store the results in $results
		 */
		$results = $readConnection->fetchAll($query);
		
		foreach($results as $manufacturer)
		{
			$capitalLetter=strtoupper($manufacturer['name'])[0];
			if (is_numeric($capitalLetter)){
				$capitalLetter='#';
			}
			if($manufacturer['in_stock']==1){
				$listManufacturer[$capitalLetter][$manufacturer['name']]= array($manufacturer,'1');
				
			}else{
				$listManufacturer[$capitalLetter][$manufacturer['name']]= array($manufacturer,'0');
			}
		}
		ksort($listManufacturer);
		return $listManufacturer;
	
	}
	
	
	/*
	public function getManufacturers($category, $gender, $letter)
	{
		
		$listManufacturer = array();
		
		$activeStoreID =  Mage::app()->getStore()->getId();
		
		$this->_featured_manufacturer_collection = Mage::getResourceModel('manufacturer/manufacturer_collection')
		->addFieldToFilter("name_store",array("like"=>$letter."%"))
		->addFieldToFilter("store_id",array("=" => $activeStoreID))->setOrder('name', 'ASC');
		
		$manufacturers_in_stock=$this->_getManufacturersInStock($category,$gender);
		
		foreach($this->_featured_manufacturer_collection as $manufacturer)
		{
			$manufacturer = $this->loadDataManufacturer($manufacturer);
			if($manufacturer->getStatus() ==1){
				//if($this->_getManufacturersAptos($category, $gender, $manufacturer)>0){
					$capitalLetter=strtoupper($manufacturer->getManufacturerMagentoName())[0];
					if (is_numeric($capitalLetter)){
						$capitalLetter='#';
					}
					
					if (in_array($manufacturer->getData('option_id'),$manufacturers_in_stock)){
						$listManufacturer[$capitalLetter][$manufacturer->getManufacturerMagentoName()]= array($manufacturer,'1');
						
					}else{
						$listManufacturer[$capitalLetter][$manufacturer->getManufacturerMagentoName()]= array($manufacturer,'0');
					
					}
				//}
			}
		}
		ksort($listManufacturer);
		$this->_featured_manufacturer_collection = $listManufacturer;
		
		
		return $listManufacturer;
	}
	*/
	
	public function getChildrenCategoryHtml($node,  $result = "") {
        if (is_numeric($node)) {
            $node = $this->getNodeById($node);
        }
        if (!$node)
            return $result;

		$result .="<ul>";	
        foreach ($node->getChildren() as $child) {
            
			$result .= "<li>" . $child->getId();
			if ($child->getChildren()) {
				$result .= $this->getChildren($child, $result);
			}
           
		   $result .= "</li>";
            
        }
		$result.="</ul>";
		echo($result);
        return $result;
    }
	
	public function getManufacturerCategories($manufacturer)
	{
		$manufacturer_resource = Mage::getResourceModel('manufacturer/manufacturer');
		$category_ids = $manufacturer_resource->getCategoriesByManufacturer($manufacturer);
		
		$category_model = Mage::getModel('catalog/category');
		
		$categories = array();	
		$listCatID = array();	
		for($i = 0; $i < count($category_ids); $i++)
		{
			$category_ids[$i]['category_id'] = intval($category_ids[$i]['category_id']);
			$categories[$i] = Mage::getModel('catalog/category')->load($category_ids[$i]['category_id']);
			$listCatID[] =  $category_ids[$i]['category_id'];
		}
		
		for($i=0;$i<count($category_ids);$i++)
		{
			$categoryParentID = $categories[$i]->getParentId();
	
			if($categoryParentID > 3)
			{
				$categoryParent = Mage::getModel('catalog/category')->load($categoryParentID);
				
				if(!in_array($categoryParentID,$listCatID))
				{
					$listCatID[] = $categoryParentID;
					$categories[] = $categoryParent;
				}
			}
		}
				
		return $categories;
	}

	
	public function getCategoryProductCount($manufacturer,$category_id)
	{
		$product_count = Mage::getResourceModel('manufacturer/manufacturer')->getCategoryProductCount($manufacturer,$category_id);
		return $product_count;
	}
	
	public function getProductCollection(){
        
		if (is_null($this->_productCollection)) {
			
            $this->_productCollection = Mage::getResourceModel('catalog/product_collection')
                ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                ->addMinimalPrice()
                ->addTaxPercents()
                ->addStoreFilter();
								
                Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($this->_productCollection);
                Mage::getSingleton('catalog/product_visibility')->addVisibleInSearchFilterToCollection($this->_productCollection);						
        }
		
		//$this->_productCollection->load();
		
        return $this->_productCollection;
    }
	
	public function addFilters($catid=null,$id=null)
	{	
		$reference_attribute_code = Mage::helper('manufacturer')->getAttributeCode();
		if($id)
		{			
			$manufactuerer = Mage::getSingleton("manufacturer/manufacturer")->load($id);
			$option_id = Mage::getResourceModel("manufacturer/manufacturer")->getManufacturerOptionIdByName($manufactuerer->getName());
			$this->getProductCollection()->addFieldToFilter($reference_attribute_code,$option_id);
		}
		
		if(is_numeric($catid))
		{						
			$category = Mage::getModel("catalog/category")->load($catid);
			if($category)
				$this->getProductCollection()->addCategoryFilter($category);
		}
		
	}
	
	public function updateUrlKey()
	{	
		$id = $this->getId();
		$url_key = $this->getData('url_key');	
		$urlrewrite = Mage::getModel("manufacturer/urlrewrite")->load("manufacturer/".$id,"id_path");
		/*
		$urlrewrite = Mage::getModel("manufacturer/urlrewrite")->load("manufacturer/".$id,"id_path");
		$oldUrlrewrite =  Mage::getModel("manufacturer/urlrewrite")->load($url_key,"request_path");
		if($oldUrlrewrite->getId()){
			if(!$urlrewrite->getId())
				$oldUrlrewrite->delete();				
			elseif($oldUrlrewrite->getId() != $urlrewrite->getId())
				$oldUrlrewrite->delete();
		}	
		*/
		//set urlKey 
		//
		//{
			//get a random product
		if($this->getData('store_id')==0)
		{
			$product_id = Mage::getResourceModel("manufacturer/manufacturer")->getFirstProductId();
			$urlrewrite->setData("id_path","manufacturer/".$id);
			$urlrewrite->setData("request_path",$this->getData('url_key'));
		
			$urlrewrite->setData("target_path",'manufacturer/index/view/id/'. $id );
			$urlrewrite->setData("product_id",$product_id);
			//var_dump($urlrewrite);
			//die();		
			try{
			
				$urlrewrite->save();				
			} catch (Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());	
			}
		}
	}
	
	function deleteStore()
	{
		Mage::getResourceModel("manufacturer/manufacturer")->deleteStore($this);
	}
	
	function loadDataManufacturer($manufacturer)
	{
		if($manufacturer->getData('store_id') != 0)
		{
			$adminManufacturer = Mage::getResourceModel('manufacturer/manufacturer')->getStoreManufacturer($manufacturer->getId(),0);
			if($manufacturer->getData('default_name_store'))
				$manufacturer->setData('name_store',$adminManufacturer->getData('name'));
			if($manufacturer->getData('default_page_title'))
				$manufacturer->setData('page_title',$adminManufacturer->getData('page_title'));
			if($manufacturer->getData('default_description'))
				$manufacturer->setData('description',$adminManufacturer->getData('description'));
			if($manufacturer->getData('default_meta_keywords'))
				$manufacturer->setData('meta_keywords',$adminManufacturer->getData('meta_keywords'));
			if($manufacturer->getData('default_meta_description'))
				$manufacturer->setData('meta_description',$adminManufacturer->getData('meta_description'));				
			if($manufacturer->getData('default_status'))		
				$manufacturer->setData('status',$adminManufacturer->getData('status'));		
			if($manufacturer->getData('default_featured'))
				$manufacturer->setData('featured',$adminManufacturer->getData('featured'));
			if($manufacturer->getData('default_image'))
				$manufacturer->setData('image',$adminManufacturer->getData('image'));	
			if($manufacturer->getData('default_description_short'))
				$manufacturer->setData('description_short',$adminManufacturer->getData('description_short'));					
			if($manufacturer->getData('default_titulodesc1'))
				$manufacturer->setData('titulodesc1',$adminManufacturer->getData('titulodesc1'));
			if($manufacturer->getData('default_descripcion1'))
				$manufacturer->setData('descripcion1',$adminManufacturer->getData('descripcion1'));
			if($manufacturer->getData('default_titulodesc2'))
				$manufacturer->setData('titulodesc2',$adminManufacturer->getData('titulodesc2'));
			if($manufacturer->getData('default_descripcion2'))
				$manufacturer->setData('descripcion2',$adminManufacturer->getData('descripcion2'));
			if($manufacturer->getData('default_genero'))
				$manufacturer->setData('genero',$adminManufacturer->getData('genero'));
			if($manufacturer->getData('default_idlinea1'))
				$manufacturer->setData('idlinea1',$adminManufacturer->getData('idlinea1'));
			if($manufacturer->getData('default_idlinea2'))
				$manufacturer->setData('idlinea2',$adminManufacturer->getData('idlinea2'));
			if($manufacturer->getData('default_idlinea3'))
				$manufacturer->setData('idlinea3',$adminManufacturer->getData('idlinea3'));
			if($manufacturer->getData('default_idlinea4'))
				$manufacturer->setData('idlinea4',$adminManufacturer->getData('idlinea4'));
			if($manufacturer->getData('default_theicons'))
				$manufacturer->setData('theicons',$adminManufacturer->getData('theicons'));
			if($manufacturer->getData('default_imagemanufacturer2'))
				$manufacturer->setData('imagemanufacturer2',$adminManufacturer->getData('imagemanufacturer2'));
			if($manufacturer->getData('default_imagelinea1'))
				$manufacturer->setData('imagelinea1',$adminManufacturer->getData('imagelinea1'));
			if($manufacturer->getData('default_imagelinea2'))
				$manufacturer->setData('imagelinea2',$adminManufacturer->getData('imagelinea2'));
			if($manufacturer->getData('default_imagelinea3'))
				$manufacturer->setData('imagelinea3',$adminManufacturer->getData('imagelinea3'));
			if($manufacturer->getData('default_imagelinea4'))
				$manufacturer->setData('imagelinea4',$adminManufacturer->getData('imagelinea4'));
			if($manufacturer->getData('default_imagerunway'))
				$manufacturer->setData('imagerunway',$adminManufacturer->getData('imagerunway'));
			if($manufacturer->getData('default_idsubcat'))
				$manufacturer->setData('idsubcat',$adminManufacturer->getData('idsubcat'));
			if($manufacturer->getData('default_newdesigner'))
				$manufacturer->setData('newdesigner',$adminManufacturer->getData('newdesigner'));
			if($manufacturer->getData('default_genero'))
				$manufacturer->setData('genero',$adminManufacturer->getData('genero'));
			if($manufacturer->getData('default_tipologia'))
				$manufacturer->setData('tipologia',$adminManufacturer->getData('tipologia'));
			if($manufacturer->getData('default_linkbanner1'))
				$manufacturer->setData('linkbanner1',$adminManufacturer->getData('linkbanner1'));
			if($manufacturer->getData('default_linkbanner2'))
				$manufacturer->setData('linkbanner2',$adminManufacturer->getData('linkbanner2'));
			if($manufacturer->getData('default_linkbanner3'))
				$manufacturer->setData('linkbanner3',$adminManufacturer->getData('linkbanner3'));
			if($manufacturer->getData('default_morefor1'))
				$manufacturer->setData('morefor1',$adminManufacturer->getData('morefor1'));
			if($manufacturer->getData('default_morefor2'))
				$manufacturer->setData('morefor2',$adminManufacturer->getData('morefor2'));
			if($manufacturer->getData('default_morefor3'))
				$manufacturer->setData('morefor3',$adminManufacturer->getData('morefor3'));
			if($manufacturer->getData('default_morefor4'))
				$manufacturer->setData('morefor4',$adminManufacturer->getData('morefor4'));
			if($manufacturer->getData('default_morefor5'))
				$manufacturer->setData('morefor5',$adminManufacturer->getData('morefor5'));
			if($manufacturer->getData('default_morefor6'))
				$manufacturer->setData('morefor6',$adminManufacturer->getData('morefor6'));
			if($manufacturer->getData('default_morefor7'))
				$manufacturer->setData('morefor7',$adminManufacturer->getData('morefor7'));
			if($manufacturer->getData('default_morefor8'))
				$manufacturer->setData('morefor8',$adminManufacturer->getData('morefor8'));
			if($manufacturer->getData('default_linkmorefor1'))
				$manufacturer->setData('linkmorefor1',$adminManufacturer->getData('linkmorefor1'));
			if($manufacturer->getData('default_linkmorefor2'))
				$manufacturer->setData('linkmorefor2',$adminManufacturer->getData('linkmorefor2'));
			if($manufacturer->getData('default_linkmorefor3'))
				$manufacturer->setData('linkmorefor3',$adminManufacturer->getData('linkmorefor3'));
			if($manufacturer->getData('default_linkmorefor4'))
				$manufacturer->setData('linkmorefor4',$adminManufacturer->getData('linkmorefor4'));
			if($manufacturer->getData('default_linkmorefor5'))
				$manufacturer->setData('linkmorefor5',$adminManufacturer->getData('linkmorefor5'));
			if($manufacturer->getData('default_linkmorefor6'))
				$manufacturer->setData('linkmorefor6',$adminManufacturer->getData('linkmorefor6'));
			if($manufacturer->getData('default_linkmorefor7'))
				$manufacturer->setData('linkmorefor7',$adminManufacturer->getData('linkmorefor7'));
			if($manufacturer->getData('default_linkmorefor8'))
				$manufacturer->setData('linkmorefor8',$adminManufacturer->getData('linkmorefor8'));
			if($manufacturer->getData('default_textobanner1'))
				$manufacturer->setData('textobanner1',$adminManufacturer->getData('textobanner1'));
			if($manufacturer->getData('default_textobanner2'))
				$manufacturer->setData('textobanner2',$adminManufacturer->getData('textobanner2'));
			if($manufacturer->getData('default_textobanner3'))
				$manufacturer->setData('textobanner3',$adminManufacturer->getData('textobanner3'));	
			if($manufacturer->getData('default_imagebanner1'))
				$manufacturer->setData('imagebanner1',$adminManufacturer->getData('imagebanner1'));	
			if($manufacturer->getData('default_imagebanner2'))
				$manufacturer->setData('imagebanner2',$adminManufacturer->getData('imagebanner2'));	
			if($manufacturer->getData('default_imagebanner3'))
				$manufacturer->setData('imagebanner3',$adminManufacturer->getData('imagebanner3'));	
			if($manufacturer->getData('default_urlblog'))
				$manufacturer->setData('urlblog',$adminManufacturer->getData('urlblog'));
		}
		
		return $manufacturer;
	}
	
	public function getUrlKey()
	{
		//$activeStoreID =  Mage::app()->getStore()->getId();
		$storeManu = Mage::getResourceModel('manufacturer/manufacturer')->getStoreManufacturer($this->getId(),0);
		
		return $storeManu->getData('url_key');
	}
	
	public function getManufacturerID($adminManufacturerID)
	{
		$activeStoreID =  Mage::app()->getStore()->getId();
		$manufacturer = Mage::getResourceModel('manufacturer/manufacturer')->getStoreManufacturer($adminManufacturerID,$activeStoreID);
		
		return $manufacturer->getId();	
	}
	
	public function getManufacturerMagentoName()
	{
		$resource = Mage::getSingleton('core/resource');
     	$readConnection = $resource->getConnection('core_read');
		$query = 'SELECT Option_id FROM ' . $resource->getTableName('manufacturer/manufacturer').' WHERE name_store LIKE "'.$this->getData('name_store').'"';
		$manufacturerId = $readConnection->fetchOne($query);
		return Mage::getModel('catalog/product')->getResource()->getAttribute("manufacturer")->getSource()->getOptionText($manufacturerId);
	}
    public function getManufacturerByName($manufacturerName)
    {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $query = 'SELECT manufacturer_id FROM ' . $resource->getTableName('manufacturer/manufacturer').' WHERE name LIKE "'.$manufacturerName.'" and store_id=0';
        $manufacturerId = $readConnection->fetchOne($query);
        return Mage::getResourceModel('manufacturer/manufacturer')->getStoreManufacturer($manufacturerId,0);
    }
	public function getManufacturerByOptionId($optionId,$storeId)
    {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $query = 'SELECT manufacturer_id FROM ' . $resource->getTableName('manufacturer/manufacturer').' WHERE option_id = "'.$optionId.'" and store_id='.$storeId;
		$manufacturerId = $readConnection->fetchOne($query);
        return Mage::getResourceModel('manufacturer/manufacturer')->getStoreManufacturer($manufacturerId,$storeId);
    }
	
	public function getUrlKeyByOptionId($optionId){
		$resource = Mage::getSingleton('core/resource');
     	$readConnection = $resource->getConnection('core_read');
		$query = 'SELECT url_key FROM ' . $resource->getTableName('manufacturer/manufacturer').' WHERE option_id='.$optionId;
		$url= $readConnection->fetchOne($query);
		return $url;
	}
	/*
	public function getFeatured()
	{
		$manufacturer = Mage::getResourceModel('manufacturer/manufacturer')->getStoreManufacturer($this->getId(),0);
		
		return $manufacturer->getData('featured');	
	}	

	public function getStatus()
	{
		$manufacturer = Mage::getResourceModel('manufacturer/manufacturer')->getStoreManufacturer($this->getId(),0);
		
		return $manufacturer->getData('status');	
	}		
	
	public function getImage()
	{
		$storeManu = Mage::getResourceModel('manufacturer/manufacturer')->getStoreManufacturer($this->getId(),0);
		
		return $storeManu->getData('image');		
	}
	*/
}