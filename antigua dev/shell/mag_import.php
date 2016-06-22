<?php
 date_default_timezone_set('Europe/Berlin');
/**
* Path to the root of your magento installation
*/
 $root = '/var/www/vhosts/elitestore.es/httpdocs/';
/**
* Url to your magento installation.
*/
//$url = 'http://clientes.magento-spain.com/elite/';
$url = 'http://www.elitestore.es/'; 
 
 
//getting Magento
 
require_once $root.'app/Mage.php';
ob_implicit_flush();

//Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
Mage::app()->setCurrentStore(Mage::getModel('core/store')->load(Mage_Core_Model_App::ADMIN_STORE_ID));
 
/**
 
* relative path from the magento root to the login file.
 
*/
 
$login = 'shell/mag_login.php';
 
 //añadido log de ejecucion  
Mage::log('Comienzo: '.date("Ymd H:i s"),null,"log_cron_articulos.csv");
 
/**
 
* name of the logfile, will be places in magentoroot/var/log/
 
*/
$fecha = date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time()));
$logFileName= 'import_'.$fecha.'.log';
/**
 
* how many products will be parsed at each post. Usually 10-50.
 
*/
 
$atOnce = 500;
 
/**
 
* DO NOT EDIT BELOW THIS LINE
 
*/
 
function convert ($size)
 
{
 
$unit=array('b','kb','mb','gb','tb','pb');
 
return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
 
}
 
 
 
set_time_limit(0);
 
ini_set('memory_limit', '512M');
 
 
 
$profileId = $argv[1];
 
if (! isset($profileId)) {
 
exit ("\nPlease specify a profile id. You can find it in the admin panel->Import/Export->Profiles.\nUsage: \n\t\t php -f $argv[0] PROFILE_ID\n\t example: php -f $argv[0] 7\n");
 
}
 
$recordCount = 0;
 

 
 
 
//starting the import
 
Mage::log("\n\n", null, $logFileName);
 
Mage::log(convert(memory_get_usage()) . " - " . "STARTING IMPORT", null, $logFileName);
 
 
 
$profile = Mage::getModel('dataflow/profile');
 
$userModel = Mage::getModel('admin/user');
 
$userModel->setUserId(0);
 
Mage::getSingleton('admin/session')->setUser($userModel);
 
if ($profileId) {
 
$profile->load($profileId);
 
if (!$profile->getId()) {
 
Mage::getSingleton('adminhtml/session')->addError('ERROR: Could not load profile');
 
}
 
}
 
 
 
/**
 
* get het login information.
 
*/
 
exec("php -f {$root}{$login}", $result);


$loginInformation = json_decode($result[0]);
 
$sessionId = $loginInformation->sessionId;
 
$formKey = $loginInformation->formKey;


 
 
//clean dataflow_batch_import table so it doesn't get amazingly big.
$db = Mage::getSingleton('core/resource')->getConnection('core_write');
$db->query("TRUNCATE TABLE `dataflow_batch_import`");
Mage::log(convert(memory_get_usage()) . " - " . "Table dataflow_batch_import cleaned", null, $logFileName);
 
 
 
//load profile
if ($profileId) {
	$profile->load($profileId);
	if (!$profile->getId()) {
 		Mage::getSingleton('adminhtml/session')->addError('ERROR: Could not load profile');
 	}
}



 Mage::register('current_convert_profile', $profile);

  $profile->run();
  
  $batchModel = Mage::getSingleton('dataflow/batch');
  if ($batchModel->getId()) {
    if ($batchModel->getAdapter()) {
      $batchId = $batchModel->getId(); 
      //echo $batchId."<--";
      $batchImportModel = $batchModel->getBatchImportModel();
      $importIds = $batchImportModel->getIdCollection();  
	  //print_r($batchImportModel->getIdCollection());
      
      $batchModel = Mage::getModel('dataflow/batch')->load($batchId);      
      $adapter = Mage::getModel($batchModel->getAdapter());
      
      foreach ($importIds as $importId) {
        $recordCount++;
        try{
          $batchImportModel->load($importId);
          if (!$batchImportModel->getId()) {
             $errors[] = Mage::helper('dataflow')->__('Skip undefined row');
             continue;
          }

          $importData = $batchImportModel->getBatchData();
          try {

            $adapter->saveRow($importData);
          } catch (Exception $e) {
            Mage::log($e->getMessage(),null,$logFileName);          
            continue;
          }
        
          if ($recordCount%20 == 0) {
            Mage::log($recordCount . ' - Completed!!',null,$logFileName);
          }
        } catch(Exception $ex) {
          Mage::log('Record# ' . $recordCount . ' - SKU = ' . $importData['sku']. ' - Error - ' . $ex->getMessage(),null,$logFileName);        
        }
      }
      foreach ($profile->getExceptions() as $e) {
        Mage::log($e->getMessage(),null,$logFileName);          
      }
      
    }
  }
  echo '\n\nImport Completed';
  Mage::log("Import Completed",null,$logFileName);
  
  //renombrar el fichero 
  //rename("/tmp/archivo_tmp.txt", "/home/user/login/docs/mi_archivo.txt");
  try{

  	if(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB)=='http://clientes.magento-spain.com/elite/'):
  		$ruta_origen= "/var/www/vhosts/magento-spain.com/clientes/elite/var/import/";;
  	else:
  		$ruta_origen= "/var/www/vhosts/elitestore.es/var/import/";
  	endif;

  	rename($ruta_origen."pruebawebsite.csv", $ruta_origen."pruebawebsite-ejecutado_".$fecha.".csv");
  } catch(Exception $e){
  	echo $e->getMessage();
  }
  
//añadido log de ejecucion  
Mage::log('Fin proceso: '.date("Ymd H:i s"),null,"log_cron_articulos.csv");
?>