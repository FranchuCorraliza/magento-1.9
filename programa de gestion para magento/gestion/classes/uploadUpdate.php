<?php

session_start();

$rutaimagen ='fotosweb/';

require_once("./ConexionDb.php");
require_once("./GestionFtp.php");

$subir = new GestionFtp;
$conector = new ConexionDb;

if($_FILES["archivo"]["name"][0])
{

    # recorremos todos los arhivos que se han subido
    for($i=0;$i<count($_FILES["archivo"]["name"]);$i++) {
        $idProduct = substr($_FILES["archivo"]["name"][$i], 0, -6);
        $numimagen = substr($_FILES["archivo"]["name"][$i], -5, -4);
        $ruta = $subir->updateimageftp($_FILES["archivo"]["name"][$i], $_FILES["archivo"]["tmp_name"][$i]);
		$conector->setRutaImagen($idProduct, "ES_IMAGE" . $numimagen, $ruta);
        $conector->actualizarImagenes($idProduct);
    }

    $_SESSION['mensajes']="<div class='alert alert-success'>Fotografías actualizadas correctamente</div>";
    echo '<script type="text/javascript" language="JavaScript">location.href = "../pages/actualizarfoto.php";</script>';
}else{
    $_SESSION['mensajes']="<div class='alert alert-success'>No se han podido actualizar las fotografías</div>";
    echo '<script type="text/javascript" language="JavaScript">location.href = "../pages/actualizarfoto.php";</script>';
}
?>