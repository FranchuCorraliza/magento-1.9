<?php
require_once "../app/Mage.php";
Mage::app();
umask(0);
ob_end_clean();
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$fila = 1;
$nombreArchivo = "../media/manufacturers/import/manufacturer.csv";
//abrimos el csv y generamos un multiarray con todos los campos de cada diseñador
try {
    if(file_exists($nombreArchivo))
    {
		$handle = fopen($nombreArchivo,"r")  or die("no puedo leer el fichero ->".$nombreArchivo);
		if($handle){
			$contaLineas = 0;
			$cabecerasLinea = array();
			while (($data = fgetcsv($handle, 6000, ';')) !== FALSE) {
				if($contaLineas==0):
					$cabecerasLinea = $data;
				else:
					$contaCampos = 0;
					foreach($data as $valor){
						$data[$cabecerasLinea[$contaCampos]] = $valor; //asigno los valores a sus cabeceras por linea	
						$contaCampos++;
					}
					try{
						echo "<hr>";
						$manufacturer = Mage::getModel('manufacturer/manufacturer')->getManufacturerByName($data['name']);
						if ($manufacturer){
							echo "Importando ".$manufacturer->getName();
							$data['image'] = Mage::helper('manufacturer')->uploadManufacturerImageFromCsv($data['name'],$data['image'], 'image');
							$data['imagemanufacturer2'] = Mage::helper('manufacturer')->uploadManufacturerImageFromCsv($data['name'],$data['imagemanufacturer2'], 'imagemanufacturer2');
							$data['imagebanner1'] = Mage::helper('manufacturer')->uploadManufacturerImageFromCsv($data['name'],$data['imagebanner1'], 'imagebanner1');
							$data['imagebanner2'] = Mage::helper('manufacturer')->uploadManufacturerImageFromCsv($data['name'],$data['imagebanner2'], 'imagebanner2');
							$data['imagebanner3'] = Mage::helper('manufacturer')->uploadManufacturerImageFromCsv($data['name'],$data['imagebanner3'], 'imagebanner3');
							$data['imagelinea1'] = Mage::helper('manufacturer')->uploadManufacturerImageFromCsv($data['name'],$data['imagelinea1'], 'imagelinea1');
							$data['imagelinea2'] = Mage::helper('manufacturer')->uploadManufacturerImageFromCsv($data['name'],$data['imagelinea2'], 'imagelinea2');
							$data['imagelinea3'] = Mage::helper('manufacturer')->uploadManufacturerImageFromCsv($data['name'],$data['imagelinea3'], 'imagelinea3');
							$data['imagelinea4'] = Mage::helper('manufacturer')->uploadManufacturerImageFromCsv($data['name'],$data['imagelinea4'], 'imagelinea4');
							$data['imagerunway'] = Mage::helper('manufacturer')->uploadManufacturerImageFromCsv($data['name'],$data['imagerunway'], 'imagerunway');
							$data['url_key'] = $manufacturer->getUrlKey();
							$model = Mage::getModel('manufacturer/manufacturer');
							$model->setData($data)
								   ->setId($manufacturer->getId())
								   ->setUpdateTime(now());
							$model->save();
							$model->updateUrlKey();
							echo "....OK<hr>";
							echo "<hr>";
							var_dump($data);
							echo "<hr>";
							flush();
						}else{
							echo "El diseñador ".$data['name']." no existe.<hr>";
						}
					} catch (Exception $ex){ 
						echo "<br>Error: $ex->getMessage()";
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
	echo "<hr>Importación Finalizadda</hr>";
} catch (Exception $e) {
    echo 'Excepción capturada: ',  $e->getMessage(), "\n";
}
//ya tengo el multiarray lo recorremos para ir actualizando cada registrodel diseñador
/*
foreach ($micsv as $data) {
	$manufacturer = Mage::getModel('manufacturer/manufacturer')->getManufacturerByName($data['name']);
	
	echo "<hr>Subiendo el diseñador ".$data['name']."<hr>";
	if ($manufacturer){
		echo "<hr>id=".$manufacturer->getId()."<hr>";
		
		$data['image'] = Mage::helper('manufacturer')->uploadManufacturerImageFromCsv($data['name'],$data['image'], 'image');
		$data['imagemanufacturer2'] = Mage::helper('manufacturer')->uploadManufacturerImageFromCsv($data['name'],$data['imagemanufacturer2'], 'imagemanufacturer2');
		$data['imagebanner1'] = Mage::helper('manufacturer')->uploadManufacturerImageFromCsv($data['name'],$data['imagebanner1'], 'imagebanner1');
		$data['imagebanner2'] = Mage::helper('manufacturer')->uploadManufacturerImageFromCsv($data['name'],$data['imagebanner2'], 'imagebanner2');
		$data['imagebanner3'] = Mage::helper('manufacturer')->uploadManufacturerImageFromCsv($data['name'],$data['imagebanner3'], 'imagebanner3');
		$data['imagelinea1'] = Mage::helper('manufacturer')->uploadManufacturerImageFromCsv($data['name'],$data['imagelinea1'], 'imagelinea1');
		$data['imagelinea2'] = Mage::helper('manufacturer')->uploadManufacturerImageFromCsv($data['name'],$data['imagelinea2'], 'imagelinea2');
		$data['imagelinea3'] = Mage::helper('manufacturer')->uploadManufacturerImageFromCsv($data['name'],$data['imagelinea3'], 'imagelinea3');
		$data['imagelinea4'] = Mage::helper('manufacturer')->uploadManufacturerImageFromCsv($data['name'],$data['imagelinea4'], 'imagelinea4');
		$data['imagerunway'] = Mage::helper('manufacturer')->uploadManufacturerImageFromCsv($data['name'],$data['imagerunway'], 'imagerunway');
		$model = Mage::getModel('manufacturer/manufacturer');
		$model->setData($data)
			   ->setId($manufacturer->getId())
			   ->setUpdateTime(now());
		$model->save(); 
	}else{
		echo "<hr>El diseñador ".$data['name']." no existe<hr>";
	}
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
     $model->save(); 

     //var_dump($registro);
}
*/
?>