<?php
class Elite_Contactus_Block_Contactus extends Mage_Core_Block_Template
{
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    public function getDepartaments(){
        $storeId = Mage::app()->getStore()->getStoreId(); // ID of the store you want to fetch the value of
        $configValue = Mage::getStoreConfig('contactus/opciones/departamentos', $storeId);
        $departamentos = explode(PHP_EOL, $configValue);
        $depfinal = array();

            foreach ($departamentos as $departamento) {
                $dep = explode(",", $departamento);
                $depfinal[] = $dep;
            }

        return $depfinal;
    }  
    public function getEmailFromName($nombreDep){
        $storeId = Mage::app()->getStore()->getStoreId(); // ID of the store you want to fetch the value of
        $configValue = Mage::getStoreConfig('contactus/opciones/departamentos', $storeId);
        $departamentos = explode(PHP_EOL, $configValue);
        $depfinal = array();
        
            foreach ($departamentos as $departamento) {
                $dep = explode(",", $departamento);
                $depfinal[] = $dep;
            }

            $dep1 = "";

            foreach ($depfinal as $departamento) {
                if($nombreDep==$departamento[0]){
                   $dep1 = $departamento[1];
               }
            }

        return $dep1;
    } 
}

?>