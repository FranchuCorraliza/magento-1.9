<?php
//require_once("./variables.php");

ob_end_clean();
require_once("../../classes/ConexionDb.php");
	
$update = new ConexionDb;
	
$actualzar = $update->borrarImagenCodarticulo($_POST['id'],'ES_IMAGE'.$_POST['foto']);
$update->actualizarImagenes($_POST['id']);
echo '{"error":"0","image":"#'.$_POST['id'].'-image'.$_POST['foto'].'","boton":"#'.$_POST['id'].'eliminarfoto'.$_POST['foto'].'"}';