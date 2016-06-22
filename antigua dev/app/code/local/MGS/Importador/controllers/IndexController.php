<?php
ini_set('memory_limit', '-1');
set_time_limit(0);



class MGS_Importador_IndexController extends Mage_Core_Controller_Front_Action{
	
	public function indexAction(){            
		date_default_timezone_set('Europe/Berlin');
		
		
		//$root = '/var/www/vhosts/magento-spain.com/clientes/elite/';
		$root = '/var/www/vhosts/elitestore.es/httpdocs/';
		
		//$url = 'http://clientes.magento-spain.com/elite/';
		$url = 'http://www.elitestore.es/'; 
 
		require_once $root.'app/Mage.php';
		ob_implicit_flush();

		//Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
		Mage::app()->setCurrentStore(Mage::getModel('core/store')->load(Mage_Core_Model_App::ADMIN_STORE_ID));
		 

		$login = 'shell/mag_login.php';
		 
		 //añadido log de ejecucion  
		Mage::log('Comienzo: '.date("Ymd H:i s"),null,"log_cron_articulos.csv");

		$fecha = date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time()));
		$logFileName= 'import_'.$fecha.'.log';

		$atOnce = 500;
		
		
		set_time_limit(0); 
		ini_set('memory_limit', '512M');
	
		$profileId = 13;
		 
		if (! isset($profileId)) {
		 
		exit ("\nPlease specify a profile id. You can find it in the admin panel->Import/Export->Profiles.\nUsage: \n\t\t php -f $argv[0] PROFILE_ID\n\t example: php -f $argv[0] 7\n");
		 
		}
		 
		$recordCount = 0;
		 //starting the import
		$logFileName = "Log.txt";
		

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
		
		exec("php -f {$root}{$login}", $result);


$loginInformation = json_decode($result[0]);
 
$sessionId = $loginInformation->sessionId;
 
$formKey = $loginInformation->formKey;
		
		$db = Mage::getSingleton('core/resource')->getConnection('core_write');
		$db->query("TRUNCATE TABLE `dataflow_batch_import`");

		
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
  echo '<br>Import Completed';
  Mage::log("Import Completed",null,$logFileName);
		
		echo "<br>Termino ejecucion";
	}
	function convert ($size){
		$unit=array('b','kb','mb','gb','tb','pb');
		return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
	}


}


?>
