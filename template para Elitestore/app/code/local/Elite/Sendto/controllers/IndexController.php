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
		//metodo para el geoip
		$cookie = Mage::getSingleton('core/cookie');
		
        //if ($cookie->get('geoip_processed') != 1) {
			$cookie->set('geoip_nombre', $_POST['nombre'], time() + 86400, '/');
            $cookie->set('geoip_bandera', $_POST['bandera'], time() + 86400, '/');
			//$cookie->set('geoip_idioma', $_POST['idioma'], time() + 86400, '/');
			$cookie->set('geoip_moneda', $_POST['moneda'], time() + 86400, '/');
            $cookie->set('geoip_tienda', $_POST['zona'], time() + 86400, '/');
		//fin del metodo para el geoip

		$store=Mage::app()->getStore($_POST['zona']);
		
    	$ruta =  $store->getBaseUrl()."?___store=".$store->getCode();
            
    	
    	echo $ruta;

    }
    public function getStatesAction()
    {
        session_start();        
    }
}
 ?>