<?php
session_start();
include "../classes/ConexionGestion.php";

//$conexion = new ConexionGestion();


if(isset($_POST['email']))
{
    $usuario=$_POST['email'];
    $password=$_POST['password'];
	//$resultados = $conexion->obtenerusuario('email',$usuario,$password);
}

if(isset($_SESSION['email'])){
    if(isset($_POST['emailOut']))
    {
        session_unset('usuario');
        session_unset('email');
		setcookie("login", "francisco.it@elitespain.es", time() - 36000, "/", "192.168.1.200");
        $_SESSION['mensajes']="Has Cerrado sesion";
        echo '<script type="text/javascript" language="JavaScript">location.href = "../index.php";</script>';
    }
    else{
        $_SESSION['mensajes']="Has iniciado sesión anteriormente";
        echo '<script type="text/javascript" language="JavaScript">location.href = "./panel.php";</script>';
    }

}
else{
	//if(isset($resultados)){
		$_SESSION['usuario']="francisco.it@elitespain.es";//$resultados[0]['NOMBRE'];
		$_SESSION['email']="francisco.it@elitespain.es";//$resultados[0]['EMAIL'];
		setcookie("login", "francisco.it@elitespain.es",/*$resultados[0]['EMAIL']*/time() + 36000, "/", "192.168.1.200");
		setcookie("permisos", "1"/*$resultados[0]['ISADMIN']*/, time() + 36000, "/", "192.168.1.200");
		setcookie("puesto", "1"/*$resultados[0]['PUESTO']*/, time() + 36000, "/", "192.168.1.200");
        echo '<script type="text/javascript" language="JavaScript">location.href = "./panel.php";</script>';
	//}
	//else{
    //    $_SESSION['mensajes']="Datos incorrectos";
     //   echo '<script type="text/javascript" language="JavaScript">location.href = "../index.php";</script>';
    //}
	
}
