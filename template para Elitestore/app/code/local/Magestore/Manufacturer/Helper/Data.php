<?php

class Magestore_Manufacturer_Helper_Data extends Mage_Core_Helper_Abstract
{
	
	public  function uploadManufacturerImage($manufactureName,$uploadImageFile,$campoImagen)
	{
		$this->createImageFolder($manufactureName);
		
		$manufacturer_image_path = $this->getImagePath($manufactureName);
		$manufacturer_image_path_cache = $this->getImagePathCache($manufactureName); 
		$imageName = "";
		$newImageName = "";
		if(isset($uploadImageFile['name']) && $uploadImageFile['name'] != '') {
			try {	
				/* Starting upload */	
				$imageName =  $uploadImageFile['name'] ;
				$uploader = new Varien_File_Uploader($campoImagen);
				$newImageName = $this->refineImageName($manufactureName) .'_'. $this->refineImageName($imageName);
				// Any extention would work
				$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
				$uploader->setAllowRenameFiles(true);
									
				$uploader->setFilesDispersion(false);
																		
				$uploader->save($manufacturer_image_path, $uploadImageFile['name'] );
				$fileImg = new Varien_Image($manufacturer_image_path.DS.$imageName);
				$fileImg->keepAspectRatio(true);
				$fileImg->keepFrame(true);
				$fileImg->keepTransparency(true);
				$fileImg->constrainOnly(false);
				$fileImg->backgroundColor(array(255,255,255));
				$fileImg->save($manufacturer_image_path_cache.DS.$newImageName,null);
				
				if($newImageName != $imageName){
					copy($manufacturer_image_path .DS. $imageName,$manufacturer_image_path .DS.$newImageName);
					unlink($manufacturer_image_path.DS.$imageName);
				}
				
			} catch (Exception $e) {
			
				Mage::getSingleton('adminhtml/session')->addError($this->getErrorMessage() .  $e->getMessage());
			}
	        			
			$imageName = $newImageName;
		}
		return $imageName;
	}
	
	public  function uploadManufacturerImageFromCsv($manufactureName,$imageName,$campoImagen)
	{
		$this->createImageFolder($manufactureName);
		
		$manufacturer_image_path = $this->getImagePath($manufactureName);
		$manufacturer_image_path_cache = $this->getImagePathCache($manufactureName); 
		
		if(isset($imageName) && $imageName != '') {
			try {
				$tempFile= Mage::getBaseDir('media') . DS .'manufacturer-temp'. DS .$imageName;
				if (file_exists($tempFile)){
					$newImageName = $this->refineImageName($manufactureName) .'_'. $this->refineImageName($imageName);
					copy($tempFile, $manufacturer_image_path.DS.$imageName);
					copy($tempFile, $manufacturer_image_path_cache.DS.$imageName);
					$fileImg = new Varien_Image($manufacturer_image_path.DS.$imageName);
					$fileImg->keepAspectRatio(true);
					$fileImg->keepFrame(true);
					$fileImg->keepTransparency(true);
					$fileImg->constrainOnly(false);
					$fileImg->backgroundColor(array(255,255,255));
					$fileImg->save($manufacturer_image_path_cache.DS.$newImageName,null);
					if($newImageName != $imageName){
						copy($manufacturer_image_path .DS. $imageName,$manufacturer_image_path .DS.$newImageName);
						unlink($manufacturer_image_path.DS.$imageName);
					}
				}	
				
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($this->getErrorMessage() .  $e->getMessage());
			}
	        			
			$imageName = $newImageName;
		}
		return $imageName;
	}
	
	public  function createImageFolder($manufactureName)
	{
		$manufacturer_path = Mage::getBaseDir('media') . DS .'manufacturers';
		$manufacturer_path_cache = Mage::getBaseDir('media') . DS .'manufacturers'. DS .'cache';
		
		$manufacturer_image_path = $this->getImagePath($manufactureName);
		$manufacturer_image_path_cache = $this->getImagePathCache($manufactureName); 

		if(!is_dir($manufacturer_path))
		{
			try{
			
				chmod(Mage::getBaseDir('media'),0777);
				
				mkdir($manufacturer_path);
				
				chmod($manufacturer_path,0777);
				
			} catch(Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($this->getErrorMessage() . $e->getMessage());
			}
		}

		if(!is_dir($manufacturer_path_cache))
		{
			try{
			
				chmod($manufacturer_path,0777);
				
				mkdir($manufacturer_path_cache);
				
				chmod($manufacturer_path_cache,0777);
				
			} catch(Exception $e) {	
				Mage::getSingleton('adminhtml/session')->addError($this->getErrorMessage() . $e->getMessage());
			}		
		}		
		
		if(!is_dir($manufacturer_image_path))
		{
			try{
				chmod($manufacturer_path,0777);
				
				mkdir($manufacturer_image_path);
				
				chmod($manufacturer_image_path,0777);
			
			} catch(Exception $e) {	
				Mage::getSingleton('adminhtml/session')->addError($this->getErrorMessage() . $e->getMessage());
			}
		}
		
		if(!is_dir($manufacturer_image_path_cache))
		{
			try{
			
				mkdir($manufacturer_image_path_cache);
			
				chmod($manufacturer_image_path_cache,0777);
				
			} catch(Exception $e) {		
				Mage::getSingleton('adminhtml/session')->addError($this->getErrorMessage() . $e->getMessage());
			
			}
		}		
	}
	
	public  function deleteImageFile($manufactureName,$image)
	{
		
		if(!$image)
		{
			return;
		}
		$manufacturer_image_path = $this->getImagePath($manufactureName) .DS.$image;
		$manufacturer_image_path_cache = $this->getImagePathCache($manufactureName) .DS.$image;
		
		if(file_exists($manufacturer_image_path))
		{
			try{
				unlink($manufacturer_image_path);

			} catch(Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($this->getErrorMessage() . $e->getMessage());			
			}
		}
		
		if(file_exists($manufacturer_image_path_cache))
		{
			try{
				unlink($manufacturer_image_path_cache);

			} catch(Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($this->getErrorMessage() . $e->getMessage());			
			}
		}
	}
	
	public function getManufacturerCatalogUrl()
	{
		$url = $this->_getUrl("manufacturer/index", array());

		return $url;	
	}

	
	public function getOptionStore()
	{
		$arrStore = array();
		$arrOptionStore = array();
		$arrOptionStore[] = array('value' => 0, 'label' => 'admin' ); 
		$collection_store = Mage::getModel('core/store')->getCollection();
		foreach($collection_store as $store)
		{
			$arrOptionStore[] = array( 'value' => $store->getId(), 'label' => $store->getName(),); 
		}
		return $arrOptionStore;
	}	
	
	public function getArrStore()
	{
		$arrStore = array();
		$arrStore[0] = 'admin';
		$collection_store = Mage::getModel('core/store')->getCollection();
		foreach($collection_store as $store)
		{
			$arrStore[$store->getId()] = $store->getName();
		}
		return $arrStore ;		
	}
	
	public function getManufacturerAttributeName()
	{
		return Mage::getStoreConfig('manufacturer/general/attribute_code');
	}
	
	public function getDefaultData($optionManufacturer)
	{
		$storeName = Mage::getModel('core/store')->load($optionManufacturer['store_id'])->getData('name');
		$urlKey = $optionManufacturer['value'] .'-'. $storeName;
		$data['name'] = $optionManufacturer['value'];	
		$data['name_store'] = $optionManufacturer['value'];	
		$data['description'] = $optionManufacturer['value'];
		$data['titulodesc1'] = $optionManufacturer['value'];
		$data['status'] = 1;
		$data['created_time'] = now();
		$data['update_time'] = now();
		$data['store_id'] = $optionManufacturer['store_id'];
		$data['url_key'] = $this->refineUrlKey($urlKey);	
		
		return $data;
	}

	public function updateDataManufacturerStoresFormAdminStore(Magestore_Manufacturer_Model_Manufacturer $manufacturer)
	{
		$arrOptionStore = $this->getOptionStore();
		$numStore = count($arrOptionStore);
		
		for($i=1;$i<$numStore;$i++)
		{
			$store = $arrOptionStore[$i];
			$manufacturer->setData("manufacturer_id",null);
			$manufacturer->setData("store_id",$store['value']);
			$urlKey = $manufacturer->getData('name') .'-'. $store['label'];
			$manufacturer->setData("url_key",$this->refineUrlKey($urlKey));
			$manufacturer->save();
			$this->updateManufacturerToCatalog($manufacturer);
		}
	}
	
	public function updateManufacturerToCatalog(Magestore_Manufacturer_Model_Manufacturer $manufacturer)
	{
		$value_id = Mage::getResourceModel('manufacturer/manufacturer')->getValue_IdManufacturer($manufacturer);
		$option_id = Mage::getResourceModel('manufacturer/manufacturer')->getOptiond_IdByName($manufacturer->getData('name'));
		if(!$option_id)
		{
			$manufacturerAttributeId = Mage::getResourceModel('manufacturer/manufacturer')->getManufacturerAttributeId();
			$modelEao = Mage::getModel('manufacturer/eao');
			$modelEao->setData('attribute_id',$manufacturerAttributeId);
			$modelEao->save();
			$option_id = $modelEao->getData('option_id');
		}
		$modelEaov = Mage::getModel('manufacturer/eaov')->load($value_id);
		$modelEaov->setData('option_id',$option_id);
		$modelEaov->setData('store_id',$manufacturer->getData('store_id'));
		$modelEaov->setData('value',$manufacturer->getData('name_store'));
		$modelEaov->save();
		$manufacturer->setData('option_id',$option_id);
		$manufacturer->save();		
	}
	
	public function autoUpdateManufacturerFormCatalog()
	{	
		$this->updateManufacturerFormCatalog();
		$this->filterManufacturerFromCatalog();
		$this->updateAllManufacturerStore();
	}
	

	public function updateManufacturerFormCatalog()
	{
		$arrOptionManufacturer = Mage::getResourceModel('manufacturer/manufacturer')->getCatalogManufacturer();
		$arrOptionStore = $this->getOptionStore();
		foreach($arrOptionManufacturer as $optionManufacturer)
		{
			$manufacturer = Mage::getResourceModel('manufacturer/manufacturer')->getManufacturerByOption($optionManufacturer);
			if(!$manufacturer->getId())
			{
				$this->insertManufacturerFromOption($optionManufacturer);
			} 
			elseif($manufacturer->getData('name_store') != $optionManufacturer['value'])
			{
				$manufacturer->setData('name_store',$optionManufacturer['value']);
				$manufacturer->save();
			}
			if(($manufacturer->getData('name') != $optionManufacturer['value']) AND ($optionManufacturer['store_id'] ==0) )
			{
				$this->updateAdminNameManufacturer($manufacturer,$optionManufacturer['value']);
			}
		}
	}
	
	public function insertManufacturerFromOption($optionManufacturer)
	{
		$data = $this->getDefaultData($optionManufacturer);			
		$arrOptionStore = $this->getOptionStore();
		if($optionManufacturer['store_id'] == 0)	
			foreach($arrOptionStore as $store)
			{
				if($store['value'] ==0)
					$data['option_id'] = $optionManufacturer['option_id'];	
				$data['store_id'] = $store['value'];
				$data['url_key'] = $optionManufacturer['value'];					
				$data['url_key'] = $this->refineUrlKey($data['url_key']);					
				$model = Mage::getModel('manufacturer/manufacturer');
				$model->setData($data);
				$model->save();
				//update url_key
				$model->updateUrlKey();	
			}			
	}
	
	public function updateAdminNameManufacturer($manufacturer,$newName)
	{
		$collection = Mage::getResourceModel('manufacturer/manufacturer_collection')
					  ->addFieldToFilter("name",array("=" => $manufacturer->getData('name')));
		foreach($collection as $model)
		{
			$model->setData('name',$newName);
			$model->save();
		}
	}
	
	public function filterManufacturerFromCatalog()
	{
		$collection = Mage::getResourceModel('manufacturer/manufacturer_collection')
					  ->addFieldToFilter("store_id",array("=" => 0));	
		foreach($collection as 	$model)
		{
			$catMenuID= Mage::getResourceModel('manufacturer/manufacturer')->getCatalogManufacturerByOption($model);			
			if(! $catMenuID)
				$model->deleteStore();
		}		
	}
	
	public function updateAllManufacturerStore()
	{
		//update manufacturer stores
		$arrOptionStore = $this->getOptionStore();
		foreach($arrOptionStore as $store)
		{
			$collectionManu = Mage::getResourceModel('manufacturer/manufacturer_collection')
							->addFieldToFilter("store_id",array("=" => $store['value']));
			if(!count($collectionManu))
				$this->updateManufacturerStore($store['value']);
		}
		// delete manufacturer stores
		Mage::getResourceModel('manufacturer/manufacturer')->deleteManufacturerStore();	
	}
	
	public function updateManufacturerStore($store_id)
	{
		//update manufacturer store
		$storeName = Mage::getModel('core/store')->load($store_id)->getData('name');
		$collection = Mage::getResourceModel('manufacturer/manufacturer_collection')
					  ->addFieldToFilter("store_id",array("=" => 0));	
		foreach($collection as 	$model)
		{
			$urlKey = $model->getData('name');
			$model->setData('manufacturer_id',null);
			$model->setData('store_id',$store_id);
			$model->setData('url_key',$this->refineUrlKey($urlKey));
			$model->save();
			//$model->updateUrlKey();
		}	
	}	
	private function quitar_tildes($cadena) {
		$no_permitidas= array ("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","À","Ã","Ì","Ò","Ù","Ã™","Ã ","Ã¨","Ã¬","Ã²","Ã¹","ç","Ç","Ã¢","ê","Ã®","Ã´","Ã»","Ã‚","ÃŠ","ÃŽ","Ã”","Ã›","ü","Ã¶","Ã–","Ã¯","Ã¤","«","Ò","Ã","Ã„","Ã‹","ä","ë","ï","ö","ü");
		$permitidas= array ("a","e","i","o","u","A","E","I","O","U","n","N","A","E","I","O","U","a","e","i","o","u","c","C","a","e","i","o","u","A","E","I","O","U","u","o","O","i","a","e","U","I","A","E","a","e","i","o","u");
		$texto = str_replace($no_permitidas, $permitidas ,$cadena);
		return $texto;
	}
	public function refineUrlKey($urlKey)
	{
		for($i=0;$i<5;$i++)
		{
			$urlKey = str_replace("  "," ",$urlKey);
		}
		$newUrlKey = str_replace(" ","-",$urlKey);
		$newUrlKey = strtolower($newUrlKey);
		$newUrlKey = $this->quitar_tildes($newUrlKey);
		
		return $newUrlKey;		
	}
	
	public function refineImageName($imageName)
	{
		for($i=0;$i<5;$i++)
		{
			$imageName = str_replace("  "," ",$imageName);
		}
		$imageName = str_replace(" ","-",$imageName);
		$imageName = strtolower($imageName);
		
		return $imageName;	
	}
	
	public function getImagePath($manufactureName)
	{
		$manufacturer_image_path = Mage::getBaseDir('media') . DS .'manufacturers' .DS. strtolower(substr($manufactureName,0,1)).substr(md5($manufactureName),0,10). $this->refineUrlKey($manufactureName);
		
		return $manufacturer_image_path;
	}

	public function getImagePathCache($manufactureName)
	{
		$manufacturer_image_path_cache = Mage::getBaseDir('media') . DS .'manufacturers' . DS .'cache'. DS . strtolower(substr($manufactureName,0,1)). substr(md5($manufactureName),0,10). $this->refineUrlKey($manufactureName);	
		
		return $manufacturer_image_path_cache;		
	}	
	
	public function getUrlImagePath($manufactureName)
	{
		$manufacturer_image_path_url = Mage::getBaseUrl('media') .'manufacturers/cache/'. strtolower(substr($manufactureName,0,1)). substr(md5($manufactureName),0,10) . $this->refineUrlKey($manufactureName);	
		
		return $manufacturer_image_path_url;				
	}
	
	public function getCollectionActiveStoreID()
	{
		$collectionActiveStoreID = array();
		
		$groupID =  Mage::app()->getStore()->getGroupId();
		
		$collectionStore = Mage::getModel('core/store')->getCollection()
            ->addGroupFilter($groupID);
		
		foreach($collectionStore as $store)
		{
			$collectionActiveStoreID[] = $store->getId();
		}
		
		return $collectionActiveStoreID;
	}
	

	public function getFirstItem($list)
	{
		$i=0;
		while($i<count($list))
		{
			if(isset($list[$i]))
				return $list[$i];
			else
			 $i++;
		}
		return null;
	}
	
	public function getLastItem($list)
	{
		$i=count($list) - 1;
		while($i>=0)
		{
			if(isset($list[$i]))
				return $list[$i];
			else
			 $i--;
		}
		return null;
	}	
	
	public function getMaxItem($list,$fiel)
	{
		$max = 0;
		for($i=0;$i<count($list);$i++)
		{
			if($list[$i][$fiel] >= $list[$max][$fiel])
				$max = $i;
		}
		return $list[$max];
	}
	
	public function getOptionSelect()
	{
		return array(array('value'=>1,'label'=>'Yes'),array('value'=>0,'label'=>'No'),);
	}
	
	public function getErrorMessage()
	{
		return "Please forward this error to us :<br/>";
	}
	
	public function getTablePrefix()
	{
		$tableName = Mage::getResourceModel('manufacturer/manufacturer')->getTable('manufacturer');	
		
		$prefix = str_replace('manufacturer','',$tableName);
		
		return $prefix;
	}
	
	public function getTable($tableName)
	{
		$prefix = $this->getTablePrefix();
		
		return $prefix.$tableName;
	}
	
	public function getAttributeCode()
	{
		return Mage::getStoreConfig('manufacturer/general/attribute_code');
	}
	
}