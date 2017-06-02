<?php
require_once("../../classes/ConexionDb.php");
require_once("../../classes/GestionFtp.php");

$update = new ConexionDb;
		
if(isset($_POST['codigo'])){
	$cod = $_POST['codigo'];
	$nombre = "cambiarimage";
	$query = "";
	$i=1;
	
	for($i=1;$i<=7;$i++){
		if(isset($_POST[$cod.$nombre.$i])){
			if(is_numeric($_POST[$cod.$nombre.$i])&&$_POST[$cod.$nombre.$i]!=""){
				$query = $query . ', ES_IMAGE' . $i . "="."ES_IMAGE".$_POST[$cod.$nombre.$i];
			}
		}
	}
	if($query!=""){
		$cadena=$update->ordenImagenes($cod, $query);
	}
	$contador=1;
	$html = '{"error":"0","codigo":"'.$cod.'","id":"#guardar'.$cod.'"';
	$allImages = $update->getAllFotos($cod);
	foreach($allImages[0] as $key=>$rutaImagen ){
		if(strlen($key)>2){
			$html = $html .',"image'.$contador.'":"';
			if($rutaImagen!=""){
				$html = $html . $rutaImagen.'"';
			}
			else{
				$html = $html . 'desaparecido.jpg"';
			}
			$contador++;
		}
	}
	$html = $html.' }';
	echo $html;
}
else{
	echo '{"error":"1","id":"#formImage'.$_POST['foto'].$idProduct.'"}';
}