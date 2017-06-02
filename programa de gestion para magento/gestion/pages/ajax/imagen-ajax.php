<?php
//require_once("./variables.php");
	
	if($_FILES){
        ob_end_clean();
        require_once("../../classes/ConexionDb.php");
        require_once("../../classes/GestionFtp.php");
        $idProduct = $_POST['codigo'];
        $subir = new GestionFtp;
        $update = new ConexionDb;
		$web= 'https://www.elitestores.com/media/import/';

        if($_FILES['foto'.$_POST['foto']]['name']){
            $ruta = $subir->subirimageftp($_FILES['foto'.$_POST['foto']]['name'], $_FILES['foto'.$_POST['foto']]['tmp_name']);
            $actualzar = $update->setRutaImagenByCodarticulo($idProduct,'ES_IMAGE'.$_POST['foto'],$ruta);
			$update->actualizarImagenes($idProduct);
			echo '{"error":"0","id":"#formImage'.$_POST['foto'].$idProduct.' button","imageId":"#formImage'.$_POST['foto'].$idProduct.' img","image":"'.$web.$ruta.'"}';
        }
	}
	else
	{
		echo '{"error":"1","id":"#formImage'.$_POST['foto'].$idProduct.'"}';
	}
	