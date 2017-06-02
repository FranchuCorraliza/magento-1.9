<?php
session_start();
# definimos la carpeta destino
# si hay algun archivo que subir

require_once("./ConexionDb.php");

$update = new ConexionDb;

if($_POST["itemcambiar"])
{

    $_SESSION['mensajes']="<div class='alert alert-success'>Fotografías subidas correctamente</div>";
    //echo '<script type="text/javascript" language="JavaScript">location.href = "../pages/subir.php";</script>';
}else{
    $_SESSION['mensajes']="<div class='alert alert-success'>No se han podido subir las fotografías</div>";
    //echo '<script type="text/javascript" language="JavaScript">location.href = "../pages/subir.php";</script>';
}
?>