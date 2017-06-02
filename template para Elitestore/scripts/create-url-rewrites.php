<?php
require_once "../app/Mage.php";
Mage::app();
umask(0);
ob_end_clean();
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$fila = 1;
$nombreArchivo = "../magmi/files/processing/nuevos.csv";
//abrimos el csv y generamos un multiarray con todos los campos de cada dise�ador
try {
	echo "<hr>Iniciando Creación de Url Rewrites<hr>";
    if(file_exists($nombreArchivo))
    {
		$handle = fopen($nombreArchivo,"r")  or die("no puedo leer el fichero ->".$nombreArchivo);
		if($handle){
			$contaLineas = 0;
			$cabecerasLinea = array();
			while ((($data = fgetcsv($handle, 6000, ';')) !== FALSE)) {
				if($contaLineas==0):
					$cabecerasLinea = $data;
				else:
					$contaCampos = 0;
					foreach($data as $valor){
						$data[$cabecerasLinea[$contaCampos]] = $valor; //asigno los valores a sus cabeceras por linea	
						$contaCampos++;
					}
					try{
						if ($data['type']=='configurable'){
							echo "Creamos urls para sku ".$data['sku']."<br/>";
							$product=Mage::getModel('catalog/product')->loadByAttribute('sku', $data['sku']);
							if ($product){
								createBrandsCategoryPages($product);
								//createBrandsDesignerLinePages($product);
								//createBrandsRunwayPage($product);
							}
						}
					} catch (Exception $ex){ 
						echo "<br>Error: ".$ex->getMessage();
					}
				endif;
				$contaLineas++;
			} //end while bucle for line
				fclose($handle);		//close file
		}
		
			
			
			
			
    }
    else{
            echo "<br/> No Tenemos Ningun CSV para importar";
    }
	echo "<hr>Creación de Url Rewrites Finalizada<hr>";
} catch (Exception $e) {
    echo 'Excepción capturada: ',  $e->getMessage(), "\n";
}

function createBrandsCategoryPages($product){
		$attr = $product->getResource()->getAttribute("manufacturer");
		if ($attr->usesSource()) {
			$manufacturerId = $attr->getSource()->getOptionId($product->getAttributeText('manufacturer'));
		}
		$categories =  $product->getCategoryIds();
		if ($categories){
			foreach ($categories as $categoryId):
				createUrlPathEn($categoryId, $manufacturerId,$product->getAttributeText('manufacturer'));
				createUrlPathEs($categoryId, $manufacturerId,$product->getAttributeText('manufacturer'));
			endforeach;
		}else{
			echo "NO TIENE CATEGORIAS<br/>";
		}
}

function getCategoriasAptas($product){
		$categories = $product->getCategoryIds();
		//En Categorias Aptas se encuentran todas las categorias que cuelgan de Women, Men y Kids, ellas inclusive.
		$categoriasAptas=array(1124,1125,1126,1127,1128,1129,1130,1131,1132,1133,18436,1134,1135,1136,1137,1138,1139,1140,1141,1142,1143,1144,1145,1146,1147,18437,18438,18439,18440,1148,1149,1150,1221,1222,1223,1224,1225,1226,1227,1228,1229,1230,1231,1232,1233,1234,1235,18441,18442,18443,18444,1240,1241,1242,1243,1244,1245,1246,1247,1248,1249,1250,1251,1252,1253,1254,1255,1256,1257,1258,1259,1260,1261,1262,1263,1264,1265,1266,1267,1268,1269,1270,1271,1272,1273,1274,1275,1276,1277,1278,1279,1280,1281,1282,18445,1283,1284,1285,1286,1287,1288,1289,1290,1291,1292,1293,1294,1295,1296,18446,18447,18448,18449,1297,18450,1447,1448,1454,1492,1493,1494,1495,1496,1497,1498,1455,1456,1457,1480,1481,1482,1483,18301,18302,18303,18304,18305,18306,18307,18308,18451,18452,18453,1449,1452,18454,18455,18456,1453,1458,1459,1470,1490,1491,1499,1450,1462,1463,1464,1465,1466,1467,1451,1460,1471,1472,1473,1474,1475,18462,1461,1479,1484,1485,1486,1487,1488,1500,1501,18457,18458,18459,1489,18309,18310,18460,18461,1468,1469,1476,1477,1478,18463,1298,1299,1300,1301,1302,1303,1304,1305,1306,1307,1308,1309,1310,1311,1312,1313,1314,1315,1316,1317,1318,1319,1320,1321,1322,1323,18464,18465,18466,1324,1325,1326,1327,1328,1329,1330,1331,1332,1333,1334,1335,1336,1337,1338,1339,1340,1341,1342,1343,1344,1345,1346,1347,1348,1349,1350,1351,1352,1353,18467,1354,1355,1356,1357,18468,18469,1358,1359,1360,1361,1362,1363,18470,1364,1365,1366,1367,1368,18471,1369,1370,1371,1372,1373,1374,1375,1376,1377,1378,1379,1380,1381,1382,1383,1384,1385,1386,1387,1388,1389,1390,1391,1392,1393,1394,1395,1396,1397,1398,1399,1400,1401,1402,1403,1404,1405,1406,1407,1408,1409,1410,1411,1412,1413,1414,1415,1416,1417,1418,1419,1420,1421,1422,1423,1424,1425,1426,1427,1428,1429,1430,18472,1431,1432,1433,1434,1435,1436,1437,1438,1439,1440,1441,1442,1443,1444,18473,18474,18475,18476,1445,1446);
		$categories = array_intersect($categories,$categoriasAptas);
		return $categories;
}

function createUrlPathEn($categoryId, $manufacturerId,$manufacturerName){
		if($manufacturerId!=""){
			$category=Mage::getModel("catalog/category")->setStoreId(3)->load($categoryId);
			//echo "-->Categoría ".$category->getName()."<br/>-->Path ".$category->getUrlPath(). "<br/>";
			if ($category->getParentCategory()->getLevel()>1){
				createUrlPathEn($category->getParentCategory()->getId(),$manufacturerId,$manufacturerName);
			}
			$resource = Mage::getSingleton('core/resource');
			$manufacturerUrlKey = Mage::helper('manufacturer')->refineUrlKey($manufacturerName);
			$categoryUrlPath=$category->getUrlPath();
			$id_path="manufacturer/".$manufacturerId.'/'.$categoryId;
			$targetPath='catalog/category/view/id/'.$categoryId.'?manufacturer='.$manufacturerId.'&sc=1';
			$requestPath=$manufacturerUrlKey.'/'.$categoryUrlPath;
			echo "-->Url:$requestPath.....";
			if (!(Mage::getModel('core/url_rewrite')->loadByIdPath($id_path)->getId()) && !(Mage::getModel('core/url_rewrite')->loadByRequestPath($requestPath)->getId())):
					Mage::getModel('core/url_rewrite')
						->setIsSystem(0)
						->setOptions()
						->setIdPath($id_path)
						->setTargetPath($targetPath)
						->setRequestPath($requestPath)
						->save();
					echo "Ok<hr>";
			else:
					echo "Ya estaba<hr>";
			endif;
		}
}

function createUrlPathEs($categoryId, $manufacturerId,$manufacturerName){
		if($manufacturerId!=""){
			$category=Mage::getModel("catalog/category")->setStoreId(4)->load($categoryId);
			//echo "-->Categoría ".$category->getName()."<br/>-->Path ".$category->getUrlPath(). "<br/>";
			if ($category->getParentCategory()->getLevel()>1){
				createUrlPathEs($category->getParentCategory()->getId(),$manufacturerId,$manufacturerName);
			}
			$resource = Mage::getSingleton('core/resource');
			$manufacturerUrlKey = Mage::helper('manufacturer')->refineUrlKey($manufacturerName);
			$categoryUrlPath=$category->getUrlPath();
			$id_path="manufacturer/".$manufacturerId.'/'.$categoryId.'/es';
			$targetPath='catalog/category/view/id/'.$categoryId.'?manufacturer='.$manufacturerId.'&sc=1';
			$requestPath=$manufacturerUrlKey.'/'.$categoryUrlPath;
			echo "-->Url:$requestPath.....";
			if (!(Mage::getModel('core/url_rewrite')->loadByIdPath($id_path)->getId()) && !(Mage::getModel('core/url_rewrite')->loadByRequestPath($requestPath)->getId())):
					Mage::getModel('core/url_rewrite')
						->setIsSystem(0)
						->setOptions()
						->setIdPath($id_path)
						->setTargetPath($targetPath)
						->setRequestPath($requestPath)
						->save();
					echo "Ok<hr>";
			else:
					echo "Ya estaba<hr>";
			endif;
		}
		
}

function createBrandsDesignerLinePages($product){
		$designerlineName = $product->getAttributeText('designer_line');
		if ($designerlineName!=''):
			Mage::log('Este producto pertenece a la linea de diseño '.$designerlineName,null,'import.log');
			$attr = $product->getResource()->getAttribute("manufacturer");
			if ($attr->usesSource()):
				$manufacturerId = $attr->getSource()->getOptionId($product->getAttributeText('manufacturer'));
			endif;
			$categories = getCategoriasAptas($product);
			foreach ($categories as $categoryId):
				$category= Mage::getModel('catalog/category')->load($categoryId);
				$parentCategoryId = explode('/',$category->getPath())[2];
				$parentCategoryName = strtolower(Mage::getModel('catalog/category')->load($parentCategoryId)->getName());
				
				$attr2 = $product->getResource()->getAttribute("designer_line");
				if ($attr2->usesSource()):
					$designerlineId = $attr2->getSource()->getOptionId($designerlineName);
				endif;
				$designerlineName=Mage::getModel('catalog/product_url')->formatUrlKey($designerlineName);
				$manufacturerUrlKey = Mage::helper('manufacturer')->refineUrlKey($product->getAttributeText('manufacturer'));
				echo 'Creando URL para la linea de diseño '. $designerlineName .',la categoria '.$category->getName().' y diseñador '.$manufacturerUrlKey.'<br/>';
				$id_path="designerline/".$designerlineId.'/'.$manufacturerId.'/'.$parentCategoryId;
				$targetPath='catalog/category/view/id/'.$parentCategoryId.'?manufacturer='.$manufacturerId.'&designer_line='.$designerlineId.'&sc=1';
				$requestPath=$manufacturerUrlKey.'/'.$designerlineName.'/'.$parentCategoryName;
				echo 'request Path:'.$requestPath."<br/>";
				echo 'id_path:'.$id_path."<br/>";
				echo 'targetPath:'.$targetPath."<br/>";
				if (!(Mage::getModel('core/url_rewrite')->loadByIdPath($id_path)->getId())): 
						Mage::getModel('core/url_rewrite')
							->setIsSystem(0)
							->setOptions()
							->setIdPath($id_path)
							->setTargetPath($targetPath)
							->setRequestPath($requestPath)
							->save();
						
				endif;
			endforeach;
		endif;
}

function createBrandsRunwayPage($product){
		if ($product->getData('runway')!=670): //Valor 0 de runway corresponde al id 670
			//Mage::log('Este producto pertenece a runway',null,'import.log');
			$attr = $product->getResource()->getAttribute("manufacturer");
			if ($attr->usesSource()):
				$manufacturerId = $attr->getSource()->getOptionId($product->getAttributeText('manufacturer'));
			endif;
			
			$categories = getCategoriasAptas($product);
			foreach ($categories as $categoryId):
				$category= Mage::getModel('catalog/category')->load($categoryId);
				$parentCategoryId = explode('/',$category->getPath())[2];
				$parentCategoryName = strtolower(Mage::getModel('catalog/category')->load($parentCategoryId)->getName());
			
				$manufacturerUrlKey = Mage::helper('manufacturer')->refineUrlKey($product->getAttributeText('manufacturer'));
				echo 'Creando URL para runway del diseñador '.$manufacturerUrlKey."<br/>";
				
				$id_path="runway/".$manufacturerId.'/'.$parentCategoryId;
				$targetPath='catalog/category/view/id/'.$parentCategoryId.'?manufacturer='.$manufacturerId.'&runway=525&sc=1';
				$requestPath=$manufacturerUrlKey.'/runway/'.$parentCategoryName;
				echo 'request Path:'.$requestPath."<br/>";
				echo 'id_path:'.$id_path."<br/>";
				echo 'targetPath:'.$targetPath."<br/>";
				if (!(Mage::getModel('core/url_rewrite')->loadByIdPath($id_path)->getId())): 
						Mage::getModel('core/url_rewrite')
							->setIsSystem(0)
							->setOptions()
							->setIdPath($id_path)
							->setTargetPath($targetPath)
							->setRequestPath($requestPath)
							->save();
						
				endif;
			endforeach;
		endif;
	}