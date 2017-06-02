<?php
ob_end_clean();
require_once "../app/Mage.php";
Mage::app();
umask(0);

Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$storesview = array(2,4,6,8,10,12);
$nombreArchivo = "transltecategories.csv";
$fp = fopen ( $nombreArchivo , "r" );
$i = 0;


while(($data=fgetcsv($fp,1000,";")) !== false){
    if($i>0){

        echo ' importamos '. $data[0];
        flush();
        echo ' name '. $data[2];
        flush();
        echo ' description '. $data[4];
        flush();

        if($data[0]==""){
            try{
                $category = Mage::getModel('catalog/category');
                $category->setName($data[2]);
                $category->setUrlKey($data[2]);
                $category->setIsActive(1);
                $category->setDisplayMode('PRODUCTS');
                $category->setIsAnchor(1); //for active anchor
                $category->setStoreId(0);
                
                $path = explode("/", $data[1]);
                unset($path[count($path)-1]);
                $path = implode("/",$path);

                $category->setPath($path);
                
                $category->save();
            } catch(Exception $e) {
                print_r($e);
            }
        }

        $categoriaactual = Mage::getModel('catalog/category')->setStoreId(0)->load($data[0]);
        $categoriaactual->setName($data[2]);
        $categoriaactual->setDescription($data[4]);
        $categoriaactual->save();

        foreach ($storesview as $view) {
            $categoriaactual = Mage::getModel('catalog/category')->setStoreId($view)->load($data[0]);
            echo ' tienda '. $view;
            flush();
            $categoriaactual->setName($data[3]);
            echo ' name '. $data[3];
            flush();
            $categoriaactual->setDescription($data[5]);
            echo ' dscription '. $data[5];
            flush();
            $categoriaactual->save();
        }

        echo "<hr>";
        flush();
    }
    $i++;
    
}
?>