<?php 
echo "Hola Mundo";
require_once '../Mage.php'; ini_set('display_errors', 1);
#Varien_Profiler::enable();
echo "Iniciando actualizacion<br/>";
Mage::setIsDeveloperMode(true);

umask(0); Mage::app('default');

Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

        try {
            Mage::getModel('catalogrule/rule')->applyAll();
            Mage::app()->removeCache('catalog_rules_dirty');
            echo Mage::helper('catalogrule')->__('The rules have been applied.');
        } catch (Exception $e) {
            echo Mage::helper('catalogrule')->__('Unable to apply rules.');
            print_r($e);
        }
		
?>		