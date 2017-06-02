<?php
ob_end_clean();
require_once "../app/Mage.php";
Mage::app();
umask(0);

Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$storesview = array(2,4,6,8,10,12);
$rutaArchivo="../magmi/files/temporal/";
$nombreArchivo = "categorias-prueba.csv";
$fp = fopen ( $rutaArchivo.$nombreArchivo , "r" );
$i = 0;


while(($data=fgetcsv($fp,1000,";")) !== false){
    if($i>0){

       

        if($data[0]==""){
            try{
                $category = Mage::getModel('catalog/category');
                $category->setName($data[3]);
                $category->setMetaTitle('');
                $category->setIncludeInMenu(0);
                $category->setUrlKey($data[1]);
                $category->setDescription(strip_tags($data[5]));
                $category->setMetaDescription('');
                $category->setMetaKeywords('');
                $category->setIsActive(1);
                $category->setDisplayMode('PRODUCTS');
                $category->setIsAnchor(1); //for active anchor
                $category->setStoreId(0);
                $category->setPath($data[2]);
                $category->setCustomUseParentSettings(true);

                
                $category->save();

                $data[0] = $category->getId();

            } catch(Exception $e) {
                print_r($e);
            }
        }

        echo ' importamos '. $data[0];
        flush();
        echo ' name '. $data[3];
        flush();
        echo ' description '. $data[5];
        flush();

        $categoriaactual = Mage::getModel('catalog/category')->setStoreId(0)->load($data[0]);
        $categoriaactual->setName($data[3]);
        $categoriaactual->setDescription($data[5]);
        $categoriaactual->save();

        foreach ($storesview as $view) {
            $categoriaactual = Mage::getModel('catalog/category')->setStoreId($view)->load($data[0]);
            echo ' tienda '. $view;
            flush();
            $categoriaactual->setName($data[4]);
            echo ' name '. $data[4];
            flush();
            $categoriaactual->setDescription($data[6]);
            echo ' dscription '. $data[6];
            flush();
            $categoriaactual->save();
        }

        echo "<hr>";
        flush();
    }
    $i++;
    
}
$log=fopen($$rutaArchivo."result-categorias.log", "w");
$txt = "1";
fwrite($log, $txt);
fclose($log);
?>