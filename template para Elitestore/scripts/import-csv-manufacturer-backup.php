<?php
require_once "../app/Mage.php";
Mage::app();
umask(0);

Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$fila = 1;
$nombreArchivo = "manufacturer.csv";
//abrimos el csv y generamos un multiarray con todos los campos de cada diseñador
try {
    if(file_exists($nombreArchivo))
    {
        $gestor = fopen($nombreArchivo, "r") or die('no tenemos permisos para abrir el archivo');
        
            $r = array_map('str_getcsv', file($nombreArchivo));
            foreach( $r as $k => $d ) { $r[$k] = array_combine($r[0], $r[$k]); }
            $micsv = array_values(array_slice($r,1));
            //var_dump($micsv);        
    }
    else{
            echo "<br/> No Tenemos Ningun CSV para importar";
    }
} catch (Exception $e) {
    echo 'Excepción capturada: ',  $e->getMessage(), "\n";
}
//ya tengo el multiarray lo recorremos para ir actualizando cada registrodel diseñador
foreach ($micsv as $data) {
	$manufacturer = Mage::getModel('manufacturer/manufacturer')->getManufacturerByName($data['name']);
	$data['image'] = Mage::helper('manufacturer')->uploadManufacturerImageFromCsv($data['name'],$data['image'], 'image');
	$data['imagemanufacturer2'] = Mage::helper('manufacturer')->uploadManufacturerImageFromCsv($data['name'],$data['imagemanufacturer2'], 'imagemanufacturer2');
	$data['imagebanner1'] = Mage::helper('manufacturer')->uploadManufacturerImageFromCsv($data['name'],$data['imagebanner1'], 'imagebanner1');
	$data['imagebanner2'] = Mage::helper('manufacturer')->uploadManufacturerImageFromCsv($data['name'],$data['imagebanner2'], 'imagebanner2');
	$data['imagebanner3'] = Mage::helper('manufacturer')->uploadManufacturerImageFromCsv($data['name'],$data['imagebanner3'], 'imagebanner3');
	$data['imagelinea1'] = Mage::helper('manufacturer')->uploadManufacturerImageFromCsv($data['name'],$data['imagelinea1'], 'imagelinea1');
	$data['imagelinea2'] = Mage::helper('manufacturer')->uploadManufacturerImageFromCsv($data['name'],$data['imagelinea2'], 'imagelinea2');
	$data['imagelinea3'] = Mage::helper('manufacturer')->uploadManufacturerImageFromCsv($data['name'],$data['imagelinea3'], 'imagelinea3');
	$data['imagelinea4'] = Mage::helper('manufacturer')->uploadManufacturerImageFromCsv($data['name'],$data['imagelinea4'], 'imagelinea4');
	$data['imagerunway'] = Mage::helper('manufacturer')->uploadManufacturerImageFromCsv($data['name'],$data['imagerunway'], 'imagerunway');/**/
	$model = Mage::getModel('manufacturer/manufacturer');
    $model->setData($data)
           ->setId($manufacturer->getId())
           ->setUpdateTime(now());
    $model->save(); 

	echo "finito";
/*		
	//echo $manufacturer->getId() . " " .  $manufacturer->getName();
     $rutainicial = $data['imagemanufacturer2'];
     $data['imagemanufacturer2']=array();
     $arrayTrasicional = explode("\\", $rutainicial);

     $data['imagemanufacturer2']['name'] = $arrayTrasicional[count($arrayTrasicional)-1];
      
     $data['imagemanufacturer2']['type'] = 'image/jpeg';
     $data['imagemanufacturer2']['error'] = 0;
     $data['imagemanufacturer2']['size'] = 23710;
     $data['imagemanufacturer2']['tmp_name'] = $rutainicial;

    $data['imagemanufacturer2'] = Mage::helper('manufacturer')->uploadManufacturerImage(,$data['imagemanufacturer2'], 'imagemanufacturer2');
    $data['imagemanufacturer2'] = $arrayTrasicional[count($arrayTrasicional)-1];
    //$data['imagemanufacturer2']="paco";

     $model = Mage::getModel('manufacturer/manufacturer');
     $model->setData($data)
            ->setId($manufacturer->getId())
            ->setUpdateTime(now());
     $model->save(); */

     //var_dump($registro);
}
?>