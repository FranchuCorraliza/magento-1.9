<?php 
class Elite_Sendto_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    public function setStatesAction()
    {
    	Mage::getSingleton('core/session')->setBandera($_POST['bandera']);

    	$_SESSION['nombre'] = $_POST['nombre'];
    	$_SESSION['bandera'] = $_POST['bandera'];
    	$_SESSION['idioma'] = $_POST['idioma'];
    	$_SESSION['moneda'] = $_POST['moneda'];
        $len="en";
        
        if($_POST['idioma']=="es_ES")
            $leng="es";

    	$ruta = "http://192.168.1.201:8080/elitestore192/" . $_POST['zona'] . "/" . $len . "/index.php";
        $ruta =  Mage::app()->getStore($_POST['zona'])->getBaseUrl();
            
    	
    	echo $ruta;

    }
    public function getStatesAction()
    {
        session_start();        
        
        echo $_SESSION['bandera'];

    }
}
 ?>