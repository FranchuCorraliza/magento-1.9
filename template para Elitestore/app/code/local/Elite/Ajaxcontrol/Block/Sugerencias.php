<?php

/**
 * @author Marcelo Jacobus <marcelo.jacobus@gmail.com>
 *
 *
 * This block name is helloworld/helloWorld, as per the module config.xml file
 * under session global.blocks.<i>helloworld</i>.class
 *
 * In order to override the rendering of this class, the protected method
 * _toHtml() should be overriden.
 */
class Elite_Ajaxcontrol_Block_Sugerencias extends Mage_Core_Block_Template
{
	public function getProductosRecientes()
	{
		return Mage::getBlockSingleton('reports/product_viewed')->getItemsCollection();
	}
	
    public function getProductosSugeridos($sku)
    {
		// OJO Cambiar listado de categorias cuando se actualicen las categorías correctas
		$categoriasAdmitidas=array(1003,1004,1005,1005,1006,1006,1007,1007,1008,1008,1009,1009,1010,1010,1011,1011,1012,1012,1013,1014,1014,1015,1015,1016,1016,1017,1017,1018,1018,1019,1019,1020,1020,1021,1021,1022,1023,1023,1024,1024,1025,1025,1026,1026,1027,1028,1028,1029,1030,1030,1031,1031,1032,1032,1033,1033,1034,1034,1035,1036,1036,1037,1037,1038,1038,1039,1040,1040,1041,1041,1042,1042,1043,1043,1044,1044,1045,1045,1046,1047,1048,1048,1049,1049,1050,1050,1051,1052,1052,1053,1053,1054,1054,1055,1055,1056,1056,1057,1057,1058,1058,1059,1059,1060,1060,1061,1062,1062,1063,1063,1064,1064,1065,1065,1066,1066,1067,1067,1068,1069,1069,1070,1070,1071,1071,1073,1074,1075,1075,1076,1076,1077,1077,1078,1078,1079,1080,1080,1081,1081,1082,1082,1083,1083,1084,1084,1085,1085,1086,1087,1087,1088,1088,1089,1089,1090,1090,1091,1091,1092,1093,1093,1094,1094,1095,1095,1096,1096,1097,1098,1098,1099,1099,1100,1100,1101,1102,1102,1103,1103,1104,1104,1105,1105,1106,1106,1107,1107,1108,1108,1109,1110,1111,1111,1112,1112,1113,1113,1114,1115,1115,1116,1116,1117,1117,1118,1118,1119,1119,1120,1120,1121,1121,1122,1122,1123,1123,1124,1124,1125,1125,1126,1126,1127,1128,1128,1129,1129,1130,1130,1131,1131,1132,1132,1133,1133,1134,1134,1135,1136,1136,1137,1137,1138,1138,1140,1141,1141,1142,1143,1143,1144,1144,1145,1145,1146,1146,1147,1147,1148,1148,1149,1149);
		
		
		$storeId = Mage::app()->getStore()->getStoreId();
		$retorno = "";
		$_product = Mage::getModel('catalog/product')->setStoreId($storeId)->loadByAttribute('sku', $sku);
		$season = $_product->getResource()->getAttribute("season");
		if ($season->usesSource()) {
			$productSeason = $season->getSource()->getOptionId($_product->getAttributeText('season'));
			
		}
		
		$categorias = $_product->getCategoryIds();
		$categoriasAptas = array_intersect($categorias,$categoriasAdmitidas);
		$categoriasBuscadas=array();
		foreach ($categoriasAptas as $categoriaId):
			$categoria= Mage::getModel('catalog/category')->load($categoriaId);
			$padre=$categoria->getParentCategory();
			$categoriasBuscadas=array_merge(explode(',',$padre->getChildren()),$categoriasBuscadas);
		endforeach;
		$filtrocategorias = array();
		foreach($categoriasBuscadas as $id):
			$filtrocategorias[] = array('attribute'=>'category_id', "finset" => $id);
		endforeach;
		$productCollection = Mage::getResourceModel('catalog/product_collection')
								->addAttributeToSelect("*")
								->addAttributeToFilter('type_id', 'configurable');
		if ($productSeason!=''){
								$productCollection ->addAttributeToFilter('season', array ('eq' => $productSeason));
		}
		$productCollection ->addAttributeToFilter('status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED))
								->addAttributeToFilter('entity_id', array ('neq' => $_product->getId()));
								

		
		if (count($filtrocategorias)>0){
			$productCollection->joinField('category_id','catalog/category_product','category_id','product_id=entity_id',null,'left')->addAttributeToFilter($filtrocategorias);
			
		}
		
		$productCollection->getSelect()->group('entity_id')->order(new Zend_Db_Expr('RAND()'))->limit(5);
		Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($productCollection);
		Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($productCollection);
		$productosHermanos= array();
		$productosPrimos=array();
		foreach ($productCollection as $product):
			$producto= Mage::getModel('catalog/product')->load($product->getId());
			if (count(array_intersect($categoriasAptas,$producto->getCategoryIds()))):
				$productosHermanos[]=$producto;
			else:
				$productosPrimos[]=$producto;
			endif;
		endforeach;
		
		$productosSugeridos=$productosHermanos;
		$i=0;
		while(isset($productosPrimos[$i]) && count($productosSugeridos)<5):
			$productosSugeridos[]=$productosPrimos[$i];
			$i++;
		endwhile;
		
        return $productosSugeridos;
    }

	public function getProductosSugeridosCheckuout()
    {
		// OJO Cambiar listado de categorias cuando se actualicen las categorías correctas
		
		$productCollection = Mage::getResourceModel('catalog/product_collection')
                        ->addAttributeToSelect("*")
						->addAttributeToFilter('checkout_pic', true)
						->addAttributeToFilter('type_id', 'configurable')
                        ->addAttributeToFilter('status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED));

		Mage::getSingleton('cataloginventory/stock')
    ->addInStockFilterToCollection($productCollection);
	$products->getSelect()->order(new Zend_Db_Expr('RAND()'));
		
		return $productCollection;
    }



}