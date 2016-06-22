<?php 
require_once "../app/Mage.php";
Mage::app();
umask(0);

ini_set("memory_limit", "-1");
set_time_limit(0);
ini_set('display_errors', 1);
#Varien_Profiler::enable();

Mage::setIsDeveloperMode(true);

/*
phpinfo();
*/

Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

        try {
            Mage::getModel('catalogrule/rule')->applyAll();
            Mage::app()->removeCache('catalog_rules_dirty');
            echo Mage::helper('catalogrule')->__('The rules have been applied.');
        } catch (Exception $e) {
            echo Mage::helper('catalogrule')->__('Unable to apply rules.');
            print_r($e);
        }
