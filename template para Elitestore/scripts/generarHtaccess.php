<?php

require_once "../app/Mage.php";
Mage::app();
umask(0);

$rutaArchivo="../magmi/files/temporal/";
$nombreArchivoProduccion = "urlProductosProduccion.csv";
$nombreArchivoDesarrollo = "urlProductosDesarrollo.csv";
$fp = fopen ($nombreArchivoProduccion , "r" );
$fp2 = fopen ($nombreArchivoDesarrollo , "r" );
$file = fopen(".htaccess", "w");
$i = 0;
$arrayProduccion = "";
$arrayDesarrollo = "";
while(($data=fgetcsv($fp,1000,";")) !== false){
        $arrayProduccion[$data[0]] = $data[1];
}
while(($data2=fgetcsv($fp2,1000,";")) !== false){
        $arrayDesarrollo[$data2[0]] = $data2[1];
}
foreach ($arrayProduccion as $sku => $url) {
    if(array_key_exists ($sku , $arrayDesarrollo))
    {
        fwrite($file, "Redirect 301 " . $url . " " . $arrayDesarrollo[$sku] . PHP_EOL);
    }
    else
    {
        //fwrite($file, "Esto es una nueva linea de texto" . PHP_EOL);
    }
}
?>