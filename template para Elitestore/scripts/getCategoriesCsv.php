<?php
ob_end_clean();
//obtenemos todas las categorias de la web con su traduccion en el view 2 
require_once 'variables.php';
require_once '../app/Mage.php';
Mage::app();
try{
$storesview = 2;

$categoryItems = Mage::getModel('catalog/category')
        ->getCollection()
        ->addAttributeToSelect('*')
        ->setStoreId(3)
        ->addFieldToFilter('is_active', 1)
        ->load();

$write = fopen('categorias.csv', 'w');
$headers = array('category_id', 'url_path', 'url_path_es', 'id_path', 'nombre_ingles', 'nombre_espanol', 'descripcion_ingles', 'descripcion_espanol','sortby');
fputcsv ($write, $headers, ";","\"");
foreach ($categoryItems as $item) {
    $category = Mage::getModel('catalog/category')->setStoreId($storesview)->load($item->getId());
    $categoryes = Mage::getModel('catalog/category')->setStoreId(3)->load($item->getId());

    $data['category_id'] = $categoryes->getId();
    $data['url_path'] = $categoryes->getUrlPath();
	$data['url_path_es'] = $category->getUrlPath();
    $data['id_path'] = $categoryes->getPath();
    $data['nombre_ingles'] = $categoryes->getName();
    $data['nombre_espanol'] = $category->getName();
    $data['descripcion_ingles'] = $categoryes->getDescription();
    $data['descripcion_espanol'] = $category->getDescription();
    $data['sortby'] = implode(',',$categoryes->getAvailableSortBy());

	fputcsv ($write, $data, ";","\"");
}

fclose($write);
echo $servidorUrl;
echo "<div class='alert alert-success'>El fichero de descargas es <a href='".$servidorUrl."scripts/categorias.csv'>categorias.csv</a></div>";
}catch(Exception $e){
	echo "<div class='alert alert-danger'>Error: $e</div>";
}
?>