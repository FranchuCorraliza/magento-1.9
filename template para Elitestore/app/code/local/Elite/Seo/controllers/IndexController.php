<?php
class Elite_Seo_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {
      
	  $this->loadLayout();   
	  $this->getLayout()->getBlock("head")->setTitle($this->__("Metatitlte"));
	$breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
      $breadcrumbs->addCrumb("home", array(
                "label" => $this->__("Home Page"),
                "title" => $this->__("Home Page"),
                "link"  => Mage::getBaseUrl()
		   ));

      $breadcrumbs->addCrumb("metatitlte", array(
                "label" => $this->__("Metatitlte"),
                "title" => $this->__("Metatitlte")
		   ));

      $this->renderLayout(); 
	  
    }
}