<?php
class Elite_LayerNavigation_Block_Catalog_Layer_Filter_Category extends Mage_Catalog_Block_Layer_Filter_Category
{
	public function getFiltrosActuales(){
		$parametros=$this->getRequest()->getParams();
		//Mage::log($parametros,null,"EliteLayerNavigation.log");
		return array();
	}
}
			