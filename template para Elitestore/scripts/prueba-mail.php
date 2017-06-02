<?php
$mensaje = "Mensaje de Prueba";
$destinatario      = 'jesus.it@elitespain.es';
$titulo    = 'Prueba';
try{
	mail($destinatario, $titulo, $mensaje);
}catch(Exception $e){
	echo $e;
}
echo "<br/>Se ha enviado correctamente";