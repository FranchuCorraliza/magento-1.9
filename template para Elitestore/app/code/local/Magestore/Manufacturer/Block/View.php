<?php
class Magestore_Manufacturer_Block_View extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getManufacturer()     
     { 
        if (!$this->hasData('manufacturer')) {
			$manufacturer = Mage::getModel("manufacturer/manufacturer");
			$adminManufacturerID = $this->getRequest()->getParam("id");
			$manufacturerID = $manufacturer->getManufacturerID($adminManufacturerID);
			$manufacturer->load($manufacturerID,"manufacturer_id");
			$manufacturer = Mage::getModel('manufacturer/manufacturer')->loadDataManufacturer($manufacturer);
            $manufacturer = $manufacturer->loadDataManufacturer($manufacturer);
			$this->setData('manufacturer',$manufacturer);
        }
		if($manufacturer->getData('status'))
		
			return $this->getData('manufacturer');
		else
		
			return null;
    }
	

	
	public function getManufacturerDetailUrl($manufacturer)
	{
		$url = $this->getUrl($manufacturer->getUrlKey(), array());
	
		return $url;	
	}
	
	public function getManufacturerImage($manufacturer)
	{
		if($manufacturer->getImage())
		{
			$url = Mage::helper('manufacturer')->getUrlImagePath($manufacturer->getName()) .'/'. $manufacturer->getImage();
		
			$img = "<img  src='". $url . "' title='". $manufacturer->getName()."' />";
		
			return $img;
		} else{
		
			return null;
		}
	}
	public function getManufacturerImageUrl($manufacturer)
	{
		if($manufacturer->getImage())
		{
			$url = Mage::helper('manufacturer')->getUrlImagePath($manufacturer->getName()) .'/'. $manufacturer->getImage();
		
			return $url;
		} else{
		
			return null;
		}
	}
	

	 public function getProductListHtml()
    {
        return $this->getChildHtml('search_result_list');
    }
	
    public function generateCategoryTree($manufacturer)
	{
		$categories = Mage::getModel('manufacturer/manufacturer')->getManufacturerCategories($manufacturer);
		//var_dump($categories);die();
		$cat_id=$this->getRequest()->getParam("catid");
		$this->setLevelList($categories);
		$this->setCategories($categories);
		$category_level = $this->getLevelList();
		$html = "";
		
//		for($i = 0; $i<count($category_level ); $i++)
		$i=2;
		if ($cat_id){
			$i= Mage::getModel('catalog/category')->load($cat_id)->getLevel()+1;
			

		}
		$html .= $this->buildTree($manufacturer,  $i,0);
		return $html;
		
	}
	
	public function setLevelList($categories)
	{
		$category_level = array();
		foreach($categories as $category)
		{
			
			$level = $category->getLevel();
			if(!in_array($level ,$category_level))
			{
				$category_level[] = $level;
			}
			
		}
		// añadimos el nivel 1 si no está. De este modo solucionamos el error que tenemos devido a que algunos artículos de nuestro catálogo no pertenecen a la categoría principal Elite
		if(!in_array('1',$category_level)){
			$category_level[] = '1';
 		}
		
		sort($category_level,SORT_NUMERIC);
		
		
		$this->setData('category_level',$category_level);
	}
	
	
	public function getLevelList()
	{
		return $this->getData('category_level');
	}
	
	public function setCategories($categories)
	{
		$this->setData('categories',$categories);
	}
	
	public function getCategories()
	{
		return $this->getData('categories');
	}
	
	
	public function buildTree($manufacturer, $level, $parrent_id)
	{
		$current_catid = $this->getRequest()->getParam("catid");
		$parrent_id=$current_catid;		
		$categories = $this->getCategories();
		$category_level = $this->getLevelList();
		if($level == count($category_level))
		{
			return "";
		}
		
		$html ="";
		for($i = 0; $i < count($categories); $i++){
			if($categories[$i]->getListed()){
				continue;
			}
			$category_path = $categories[$i]->getPath();
			$category_path = explode("/",$category_path );
			if($parrent_id){
				if(is_array($category_path) && in_array($parrent_id, $category_path)){
					$is_child = true;

				}else{
					$is_child = false;
				}
			}else{
				$is_child = true;
			}
			//if( $categories[$i]->getLevel() == $category_level[$level] && $is_child && $level==2 && ($categories[$i]->getId()==23 || $categories[$i]->getId()==62 || $categories[$i]->getId()==122 || $categories[$i]->getId()==101)){								
			if( $categories[$i]->getLevel() == $category_level[$level] && $is_child){		
				$categories[$i]->listed = 1;
				$this->setCategories($categories);
				//var_dump($categories[$i]->getPath());die();
				if($current_catid == $categories[$i]->getId()){
					$html .= "<li><a href='". $this->getManufacturerDetailUrl($manufacturer)."?catid=".$categories[$i]->getId()."' class='active'>".mb_strtolower($categories[$i]->getName(),'UTF-8')."</a>" ;
				}else{
					$html .= "<li><a href='". $this->getManufacturerDetailUrl($manufacturer)."?catid=".$categories[$i]->getId()."'>".mb_strtolower($categories[$i]->getName(),'UTF-8')."</a>" ;
				}
				//$html .= $this->buildTree($manufacturer,$level+1, $categories[$i]->getId());
				$html .= "</li>";
			}	
            			
		}
		if($html)
		{
			$html = "<ul>". $html . "</ul>";
		}
		
		return $html;
	}
	
	public function getResultCount()
    {    
        $size = Mage::getModel('manufacturer/manufacturer')->getProductCollection()->getSize();
              
        return $size;
    }
	
	
	public function setListCollection() {
      
		$this->getChild('search_result_list')
           ->setCollection($this->_getProductCollection());
    }

    protected function _getProductCollection(){
        return $this->getSearchModel()->getProductCollection();
    }
	
	public function getSearchModel()
    {
        return Mage::getSingleton('manufacturer/manufacturer');
    }
	
	public function getEditorPics($manufacturer){
		$productCollection = Mage::getResourceModel('catalog/product_collection')
                        ->addAttributeToSelect("*")
						->addAttributeToFilter('manufacturer', array ('eq' => $manufacturer->getData('option_id')))
						->addAttributeToFilter('designer_pic', array('eq' => 1))
						->addAttributeToFilter('type_id', 'configurable')
                        ->addAttributeToFilter('status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED));

		Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($productCollection);
		$productos=array();
		
		$categoriasHijasMujer= array(1003,1004,1005,1005,1006,1006,1007,1007,1008,1008,1009,1009,1010,1010,1011,1011,1012,1012,1013,1014,1014,1015,1015,1016,1016,1017,1017,1018,1018,1019,1019,1020,1020,1021,1021,1022,1023,1023,1024,1024,1025,1025,1026,1026,1027,1028,1028,1029,1030,1030,1031,1031,1032,1032,1033,1033,1034,1034,1035,1036,1036,1037,1037,1038,1038,1039,1040,1040,1041,1041,1042,1042,1043,1043,1044,1044,1045,1045,1046,1047,1048,1048,1049,1049,1050,1050,1051,1052,1052,1053,1053,1054,1054,1055,1055,1056,1056,1057,1057,1058,1058,1059,1059,1060,1060,1061,1062,1062,1063,1063,1064,1064,1065,1065,1066,1066,1067,1067,1068,1069,1069,1070,1070,1071,1071);
		$categoriasHijasHombre=array(1073,1074,1075,1075,1076,1076,1077,1077,1078,1078,1079,1080,1080,1081,1081,1082,1082,1083,1083,1084,1084,1085,1085,1086,1087,1087,1088,1088,1089,1089,1090,1090,1091,1091,1092,1093,1093,1094,1094,1095,1095,1096,1096,1097,1098,1098,1099,1099,1100,1100,1101,1102,1102,1103,1103,1104,1104,1105,1105,1106,1106,1107,1107,1108,1108,1109,1110,1111,1111,1112,1112,1113,1113,1114,1115,1115,1116,1116,1117,1117,1118,1118,1119,1119,1120,1120,1121,1121,1122,1122,1123,1123,1124,1124,1125,1125,1126,1126,1127,1128,1128,1129,1129,1130,1130,1131,1131,1132,1132,1133,1133,1134,1134,1135,1136,1136,1137,1137,1138,1138);
		$categoriasHijasKids=array(1140,1141,1141,1142,1143,1143,1144,1144,1145,1145,1146,1146,1147,1147,1148,1148,1149,1149);
		
		
		foreach ($productCollection as $product):
			$_product= Mage::getModel('catalog/product')->loadByAttribute('sku',$product->getSku());
			if (count(array_intersect($_product->getCategoryIds(),$categoriasHijasMujer))>0){
				$productos['women'][$product->getSku()]= array ('name'=> $_product->getName(), 'price' => $_product->getPrice(), 'image' => (string)Mage::helper('catalog/image')->init($product, 'small_image')->resize(200, 300), 'url' => $_product->getProductUrl(), 'pic_editor_product' => $_product->getAttributeText('designer_pic'), 'designer_line' => $_product->getAttributeText('designer_line'), 'runway' => $_product->getAttributeText('runway'), 'categoryIds' => $_product->getCategoryIds());
			}
			if (count(array_intersect($_product->getCategoryIds(),$categoriasHijasHombre))>0){
				$productos['men'][$product->getSku()]= array ('name'=> $_product->getName(), 'price' => $_product->getPrice(), 'image' => (string)Mage::helper('catalog/image')->init($product, 'small_image')->resize(200, 300), 'url' => $_product->getProductUrl(), 'pic_editor_product' => $_product->getAttributeText('designer_pic'), 'designer_line' => $_product->getAttributeText('designer_line'), 'runway' => $_product->getAttributeText('runway'), 'categoryIds' => $_product->getCategoryIds());
			}
			if (count(array_intersect($_product->getCategoryIds(),$categoriasHijasKids))>0){
				$productos['kids'][$product->getSku()]= array ('name'=> $_product->getName(), 'price' => $_product->getPrice(), 'image' => (string)Mage::helper('catalog/image')->init($product, 'small_image')->resize(200, 300), 'url' => $_product->getProductUrl(), 'pic_editor_product' => $_product->getAttributeText('designer_pic'), 'designer_line' => $_product->getAttributeText('designer_line'), 'runway' => $_product->getAttributeText('runway'), 'categoryIds' => $_product->getCategoryIds());
			}
		endforeach;
		
		return $productos;

	}
	
	public function isDesignerLineActive($manufacturer,$i){
		$designerLine=$manufacturer->getData('idlinea'.$i);
		if ($designerLine!=''):
			$productCollection = Mage::getResourceModel('catalog/product_collection')
							->addAttributeToSelect('*')
							->addAttributeToFilter('manufacturer', array ('eq' => $manufacturer->getData('option_id')))
							->addAttributeToFilter('designer_line', array ('eq' => $designerLine))
							->addAttributeToFilter('type_id', 'configurable')
							->addAttributeToFilter('status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED));
			Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($productCollection);
			return (count($productCollection)>0);
		else:
			return false;
		endif;
		
	}
	
	public function isRunwayActive($manufacturer){
		$productCollection = Mage::getResourceModel('catalog/product_collection')
                        ->addAttributeToSelect('*')
						->addAttributeToFilter('manufacturer', array ('eq' => $manufacturer->getData('option_id')))
						->addAttributeToFilter('runway', array ('eq' => 525))
						->addAttributeToFilter('type_id', 'configurable')
                        ->addAttributeToFilter('status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED));
		Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($productCollection);
		
		return (count($productCollection)>0);	
	}
	
	public function getDesignerLineImage($manufacturer, $i)
	{	
		if($manufacturer->getData('imagelinea'.$i))
		{
			$url = Mage::helper('manufacturer')->getUrlImagePath($manufacturer->getName()) .'/'. $manufacturer->getData('imagelinea'.$i);
		
			$img = "<img  src='". $url . "' title='". $manufacturer->getName()."'/>";
		
			return $img;
		} else{
		
			return null;
		}
	}
	public function getImagemanufacturer2Image($manufacturer)
	{	
		if($manufacturer->getData('imagemanufacturer2'))
		{
			$url = Mage::helper('manufacturer')->getUrlImagePath($manufacturer->getName()) .'/'. $manufacturer->getData('imagemanufacturer2');
		
			$img = "<img  src='". $url . "' title='". $manufacturer->getName()."' class='image-logo' style='max-width: 100%;'/>";
		
			return $img;
		} else{
		
			return null;
		}
	}
	public function getImageManufacturerBanner1($manufacturer)
	{	
		if($manufacturer->getData('imagebanner1'))
		{
			$url = Mage::helper('manufacturer')->getUrlImagePath($manufacturer->getName()) .'/'. $manufacturer->getData('imagebanner1');
		
			return $url;
		} else{
		
			return null;
		}
	}
	public function getImageManufacturerBanner2($manufacturer)
	{	
		if($manufacturer->getData('imagebanner2'))
		{
			$url = Mage::helper('manufacturer')->getUrlImagePath($manufacturer->getName()) .'/'. $manufacturer->getData('imagebanner2');
		
			return $url;
		} else{
		
			return null;
		}
	}
	public function getImageManufacturerBanner3($manufacturer)
	{	
		if($manufacturer->getData('imagebanner3'))
		{
			$url = Mage::helper('manufacturer')->getUrlImagePath($manufacturer->getName()) .'/'. $manufacturer->getData('imagebanner3');
		
			return $url;
		} else{
		
			return null;
		}
	}
	public function getDesignerLineTitle($manufacturer, $i)
	{
		if($manufacturer->getData('idlinea'.$i))
		{
			$attributeDetails = Mage::getSingleton("eav/config")->getAttribute("catalog_product", 'designer_line');
			$optionValue = $attributeDetails->getSource()->getOptionText($manufacturer->getData('idlinea'.$i));
			return $optionValue;
		}
		
	}
	
	public function getDesignerLineUrl($manufacturer, $i)
	{
		return Mage::helper('core/url')->getCurrentUrl().Mage::getModel('catalog/product_url')->formatUrlKey($this->getDesignerLineTitle($manufacturer,$i));
	}
	
	public function getRunwayImage($manufacturer)
	{	
		if($manufacturer->getData('imagerunway'))
		{
			
			$url = Mage::helper('manufacturer')->getUrlImagePath($manufacturer->getName()) .'/'. $manufacturer->getData('imagerunway');
		
			$img = "<img  src='". $url . "' title='". $manufacturer->getName()."'/>";
		
			return $img;
		} else{
		
			return null;
		}
	}
	public function getRunwayUrl($manufacturer, $i)
	{
		return Mage::helper('core/url')->getCurrentUrl().'runway';
	}
	
	public function getCategoriasDestacadas($manufacturer)
	{
		$categoriasIds = explode(',',$manufacturer->getData('idsubcat'));
		$categorias=array();
		foreach ($categoriasIds as $id):
			$cat=Mage::getModel('catalog/category')->load($id);
			$url=Mage::helper('core/url')->getCurrentUrl().$cat->getUrlPath();
			$titulo=$manufacturer->getManufacturerMagentoName().' '.$cat->getName();
			$categorias[$url]=$titulo;
		endforeach;
		return $categorias;
	}
	
	public function isNewDesigner($manufacturer)
	{
		return $manufacturer->getData('newdesigner');
		
	}
	
	private function _getCategories($categories,$categoriasAptas,$manufacturerId,$manufacturerUrlKey){
		$array= '<ul>';
		foreach($categories as $category) {
			if(is_numeric($category)){
				$category=Mage::getModel("catalog/category")->load($category);
			}
			if (in_array($category->getId(),$categoriasAptas) && $category->getLevel()<4){
				$cat = Mage::getModel('catalog/category')->load($category->getId());
				$manufacturerAttr = Mage::getModel('catalog/resource_eav_attribute')->load(187); //187 es el id del attributo manufacturer
				$manufacturerName= $manufacturerAttr->getSource()->getOptionText($manufacturerId);
				$category=Mage::getModel("catalog/category")->load($category->getId());
				$categoryUrlPath=$category->getUrlPath();
				$array .= '<li>'.
				'<a href="'.Mage::getBaseUrl().$manufacturerUrlKey.'/'.$categoryUrlPath.'" >' .
					  $category->getName() ."</a>\n";
				if($category->hasChildren()) {
					$children=explode(',',$category->getChildren());
					$array .= $this->_getCategories($children,$categoriasAptas,$manufacturerId,$manufacturerUrlKey);
				}
			
			}
		}
		return  $array . '</ul>';
	}
	
	public function getCategoryArbol($manufacturer){
		$manufacturerId = $manufacturer->getOptionId();
		$manufacturerUrlKey=$manufacturer->getUrlKey();
		$attributeCode = 'manufacturer';
		$products = Mage::getModel('catalog/product')
			->getCollection()
			->addAttributeToSelect('*') 
			->addAttributeToFilter('status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED))
			->addAttributeToFilter($attributeCode, $manufacturerId);
		Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($products);
		$_category=array();
		foreach($products as $product):
		   $productId = $product->getId();
		   $_category = array_merge(Mage::getModel('catalog/product')->load($productId)->getCategoryIds(), $_category);
		endforeach;
		$_category = array_unique($_category);
		$categoriasFull=array();
		foreach ($_category as $categoriaId):
			$pathCategoria = Mage::getModel('catalog/category')->load($categoriaId)->getPath();
			$categoriasFull = array_merge(explode('/', $pathCategoria), $categoriasFull);
		endforeach;
		$categoriasFull = array_unique($categoriasFull);
		$rootcatId= Mage::app()->getStore()->getRootCategoryId();
		$categories=Mage::getModel('catalog/category')->getCategories($rootcatId);
		$html="";
		$html= $this->_getCategories($categories,$categoriasFull,$manufacturerId,$manufacturerUrlKey);
		return $html;
	}
}