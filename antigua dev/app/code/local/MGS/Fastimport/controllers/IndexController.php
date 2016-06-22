<?php
set_time_limit(0);

class MGS_Fastimport_IndexController extends Mage_Core_Controller_Front_Action{
	
	public function indexAction(){
		$mio = Mage::getModel('fastimport/cron');
		$mio->importar();
	}
	
	public function stockAction(){
		$mio = Mage::getModel('fastimport/cron');
		$mio->actualizarstock();
	}
	

}
	