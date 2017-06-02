<?php
session_start();
# definimos la carpeta destino
# si hay algun archivo que subir

$rutaimagen ='fotosweb/';

require_once("./ConexionDb.php");
require_once("./GestionFtp.php");

$subir = new GestionFtp;
$conector = new ConexionDb;

if($_FILES["archivo"]["name"][0])
{
    //creamos la carpeta que tendra el backup de las fotos
    $rutabakup = $subir->getCarpetaBackup();
    # recorremos todos los arhivos que se han subido
    for($i=0;$i<count($_FILES["archivo"]["name"]);$i++) {
		$filename = explode('.',$_FILES["archivo"]["name"][$i])[0];
		$idProduct = explode('-',$filename)[0];
        $numimagen = explode('-',$filename)[1];
        $ruta = $subir->subirimageftp($_FILES["archivo"]["name"][$i], $_FILES["archivo"]["tmp_name"][$i]);
		$codbarras[$idProduct][$numimagen]=$_FILES["archivo"]["name"][$i];
		
        //hacemos el backup para el nas
        $ruta2 = $subir->subirimagenbackup($_FILES["archivo"]["name"][$i], $_FILES["archivo"]["tmp_name"][$i],$rutabakup);
    }

	
	foreach ($codbarras as $key =>$images){
        //var_dump($images);
        if (array_key_exists(0,$images)){
            $i=0;
            $fin=count($images)-1;
        }elseif(array_key_exists(1,$images)){
            $i=1;
            $fin=count($images);
        }elseif(array_key_exists(2,$images)){
            $i=2;
            $fin=count($images)+1;
        }elseif(array_key_exists(3,$images)){
            $i=3;
            $fin=count($images)+2;
        }elseif(array_key_exists(4,$images)){
            $i=4;
            $fin=count($images)+3;
        }elseif(array_key_exists(5,$images)){
            $i=5;
            $fin=count($images)+4;
        }elseif(array_key_exists(6,$images)){
            $i=6;
            $fin=count($images)+5;
        }elseif(array_key_exists(7,$images)){
            $i=7;
            $fin=count($images)+6;
        }elseif(array_key_exists(8,$images)){
            $i=8;
            $fin=count($images)+7;
        }elseif(array_key_exists(9,$images)){
            $i=9;
            $fin=count($images)+8;
        }else{
            $i=0;
            $fin=0;
        }
        $a=1;
        while($i<=$fin){
			$conector->setRutaImagen($key, "ES_IMAGE" . $a, $rutaimagen . $images[$i]);

            $i++;
            $a++;
        }
        $conector->publicar($key);
		
    }
    $_SESSION['mensajes']="<div class='alert alert-success'>Fotografías subidas correctamente</div>";
    echo '<script type="text/javascript" language="JavaScript">location.href = "../pages/subir.php";</script>';

}else{
    $_SESSION['mensajes']="<div class='alert alert-success'>No se han podido subir las fotografías</div>";
    echo '<script type="text/javascript" language="JavaScript">location.href = "../pages/subir.php";</script>';
}
?>