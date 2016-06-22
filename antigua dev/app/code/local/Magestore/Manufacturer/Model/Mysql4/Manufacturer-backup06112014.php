<?php

class Magestore_Manufacturer_Model_Mysql4_Manufacturer extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the manufacturer_id refers to the key field in your database table.
        $this->_init('manufacturer/manufacturer', 'manufacturer_id');
    }
	
	public function getCategoriesByManufacturer($manufacturer)
	{		
		$reference_attribute_code = Mage::helper('manufacturer')->getAttributeCode();
		$websiteID =  Mage::app()->getStore()->getWebsiteId();
		$storeID =  Mage::app()->getStore()->getId();
	
		$prefix = Mage::helper('manufacturer')->getTablePrefix();
		
		$manufacturer_name = $manufacturer->getName();
		
		$option_id = $this->getManufacturerOptionIdByName($manufacturer_name);

		$select = $this->_getReadAdapter()->select()
			 ->distinct()
			->from(array('ccp'=> $prefix .'catalog_category_product'), 'category_id')
            ->join(array('cpei'=> $prefix .'catalog_product_entity_int'), 'ccp.product_id=cpei.entity_id',array())
            ->join(array('ea' => $prefix .'eav_attribute'),'cpei.attribute_id = ea.attribute_id',array())
            ->join(array('ccpi' => $prefix .'catalog_category_product_index'),'ccp.category_id= ccpi.category_id',array())
            ->join(array('cpw' => $prefix .'catalog_product_website'),'ccp.product_id= cpw.product_id',array())
			
			->where('ccpi.product_id=ccp.product_id','')
			
			->where('ea.attribute_code=?',$reference_attribute_code)
			
			->where('cpei.value=?',$option_id)
			
			->where('ccpi.store_id=?',$storeID)
			
			->where('cpw.website_id=?',$websiteID);
			
		$items = $this->_getReadAdapter()->fetchAll($select);		
			
		$listCatID = array();
		$listCatID[] = 0;
		foreach($items as $item)
		{
			$listCatID[] = $item['category_id'];
		}		
		
		$categoryids = implode(",",$listCatID);	

		$select = $this->_getReadAdapter()->select()
			 ->distinct()
			->from(array('ccp'=> $prefix .'catalog_category_product'), 'category_id')
            ->join(array('cpei'=> $prefix .'catalog_product_entity_int'), 'ccp.product_id=cpei.entity_id',array())
            ->join(array('ea' => $prefix .'eav_attribute'),'cpei.attribute_id = ea.attribute_id',array())
   
			->where('ea.attribute_code=?','status')
			
			->where('cpei.value=?',1)
		
			->where("ccp.category_id IN ($categoryids)","");			
			
		$items = $this->_getReadAdapter()->fetchAll($select);		
	
        return $items; 
	}
	
	
	public function getCategoryProductCount($manufacturer,$category_id)
	{
		$reference_attribute_code = Mage::helper('manufacturer')->getAttributeCode();
		$manufacturer_name = $manufacturer->getName();
		$option_id = $this->getManufacturerOptionIdByName($manufacturer_name);
		$prefix = Mage::helper('manufacturer')->getTablePrefix();
		
		$select = $this->_getReadAdapter()->select()
			->from(array('cpei'=> $prefix .'catalog_product_entity_int'),'entity_id')
			->join(array('ea' => $prefix .'eav_attribute'),
                    'cpei.attribute_id = ea.attribute_id',array())
			->join(array('ccp'=> $prefix .'catalog_category_product'), 'ccp.product_id=cpei.entity_id',array())		
			
			->where('ea.attribute_code=?',$reference_attribute_code )
			->where('cpev.entity_type_id=?',$this->getProductEntityTypeId())
			->where('ea.entity_type_id=?',$this->getProductEntityTypeId())
			->where('cpei.value=?',$option_id)
			->where('ccp.category_id=?',$category_id);
			
		$items = $this->_getReadAdapter()->fetchAll($select);	
		return count($items);
	}
	
	public function getProductEntityTypeId()
	{
		$prefix = Mage::helper('manufacturer')->getTablePrefix();
		
		$select = $this->_getReadAdapter()->select()
			->from(array('e'=> $prefix .'eav_entity_type'),'entity_type_id')
			->where('e.entity_type_code=?','catalog_product');
			
		$item = $this->_getReadAdapter()->fetchOne($select);	

		return $item['entity_type_id'];
	}
	
	
	public function getManufacturerOptionIdByName($manufacturer_name)
	{
		$reference_attribute_code = Mage::helper('manufacturer')->getAttributeCode();
		$prefix = Mage::helper('manufacturer')->getTablePrefix();		
		
		$select = $this->_getReadAdapter()->select()
					->from(array('eao'=> $prefix .'eav_attribute_option'),'option_id')
					->join(array('ea'=> $prefix .'eav_attribute'),'eao.attribute_id=ea.attribute_id',array())
					->join(array('eaov'=> $prefix .'eav_attribute_option_value'),'eao.option_id=eaov.option_id',array())
					->where('ea.attribute_code=?',$reference_attribute_code)
					->where('eaov.value=?',$manufacturer_name);
		$option = $this->_getReadAdapter()->fetchOne($select);
		
		
		return $option;	
	}
	
	
	public function getFirstProductId()
	{
		$prefix = Mage::helper('manufacturer')->getTablePrefix();			
		
		$select = $this->_getReadAdapter()->select()
					->from(array('eao'=> $prefix .'catalog_product_entity'),'entity_id');
					
		$product_id = $this->_getReadAdapter()->fetchOne($select);
	
		return $product_id;	
	}

	public function getCatalogManufacturer()
	{
		$prefix = Mage::helper('manufacturer')->getTablePrefix();			
		$reference_attribute_code = Mage::helper('manufacturer')->getAttributeCode();
		$select = $this->_getReadAdapter()->select()
					->from(array('eao'=> $prefix .'eav_attribute_option'),array('option_id','eaov.value','eaov.store_id'))
					->join(array('ea'=> $prefix .'eav_attribute'),'eao.attribute_id=ea.attribute_id',array())
					->join(array('eaov'=> $prefix .'eav_attribute_option_value'),'eao.option_id=eaov.option_id',array())
					->where('ea.attribute_code=?',$reference_attribute_code);
		$option = $this->_getReadAdapter()->fetchAll($select);
		
		return $option;	
	}	

	public function getOptiond_IdByName($manufacturerName)
	{	
		$prefix = Mage::helper('manufacturer')->getTablePrefix();			
		
		$select = $this->_getReadAdapter()->select()
					->from(array('eaov'=> $prefix .'eav_attribute_option_value'),'option_id')
					->where('eaov.value=?',$manufacturerName)
					->where('eaov.store_id=?',0);
		$option_id = $this->_getReadAdapter()->fetchOne($select);	
		
		return $option_id;
	}
	
	public function getValue_IdManufacturer(Magestore_Manufacturer_Model_Manufacturer $manufacturer)
	{
		$prefix = Mage::helper('manufacturer')->getTablePrefix();	
		
		$option_id = $this->getOptiond_IdByName($manufacturer->getData('name'));
		$select = $this->_getReadAdapter()->select()
					->from(array('eaov'=> $prefix .'eav_attribute_option_value'),'value_id')
					->where('option_id=?',$option_id)
					->where('store_id=?',$manufacturer->getData('store_id'));	
		$value_id = $this->_getReadAdapter()->fetchOne($select);
		
		return $value_id;
	}
	
	public function getManufacturerAttributeId()
	{
		$prefix = Mage::helper('manufacturer')->getTablePrefix();
		
		$reference_attribute_code = Mage::helper('manufacturer')->getAttributeCode();
		$select = $this->_getReadAdapter()->select()
					->from(array('ea'=> $prefix .'eav_attribute'),'attribute_id')
					->where('ea.attribute_code=?',$reference_attribute_code);
		$attributeID = $this->_getReadAdapter()->fetchOne($select);	

		return $attributeID;
	}
	
	public function deleteStore($manufacturer)
	{
		$prefix = Mage::helper('manufacturer')->getTablePrefix();		
		
		$manufacturerTable = $prefix .'manufacturer';
		$core_url_rewriteTable = $prefix .'core_url_rewrite';
		
		$tables = "`$manufacturerTable` left join `$core_url_rewriteTable` ON `$manufacturerTable`.`url_key`=`$core_url_rewriteTable`.`request_path`";
		$wheres = "`name` = '". $manufacturer->getData('name') ."'"; 
		$sql = "DELETE `$manufacturerTable`.*, `$core_url_rewriteTable`.* FROM $tables WHERE $wheres";
		$this->_getWriteAdapter()->query($sql);
	}
	
	public function clearManufacturer()
	{
		$prefix = Mage::helper('manufacturer')->getTablePrefix();		
		
		$manufacturerTable = $prefix .'manufacturer';
		$core_url_rewriteTable = $prefix .'core_url_rewrite';
		
		$tables = "`$manufacturerTable` left join `$core_url_rewriteTable` ON `$manufacturerTable`.`url_key`=`$core_url_rewriteTable`.`request_path`";
		
		$sql = "DELETE`$manufacturerTable.*, `$core_url_rewriteTable`.* FROM $tables";
		
		$this->_getWriteAdapter()->query($sql);
		
	}
	
	public function getManufacturerByOption($optionManufacturer)
	{
		$prefix = Mage::helper('manufacturer')->getTablePrefix();	
		
		$select = $this->_getReadAdapter()->select()
					->from(array('manu'=> $prefix .'manufacturer'),'manufacturer_id')
					->where('manu.option_id=?',$optionManufacturer['option_id'])
					->where('manu.store_id=?',$optionManufacturer['store_id']);	
		$manufacturer_id = $this->_getReadAdapter()->fetchOne($select);	
		
		return Mage::getModel('manufacturer/manufacturer')->load($manufacturer_id);
	}
	
	public function getCatalogManufacturerByOption($manufacturer)
	{
		$prefix = Mage::helper('manufacturer')->getTablePrefix();	
		
		$select = $this->_getReadAdapter()->select()
					->from(array('eaov'=> $prefix .'eav_attribute_option_value'),'value_id')
					->where('eaov.option_id=?',$manufacturer->getData('option_id'))
					->where('eaov.store_id=?',$manufacturer->getData('store_id'));
		
		$valueID = $this->_getReadAdapter()->fetchOne($select);
		return $valueID; 
	}
	
	public function deleteManufacturerStore()
	{
		$prefix = Mage::helper('manufacturer')->getTablePrefix();	
		$manufacturerTable = $prefix ."manufacturer";
		
		$arrOptionStore = Mage::helper('manufacturer')->getOptionStore();
		
		$storeIDs = array();
		foreach($arrOptionStore as $store)
		{
			$storeIDs[] = $store['value'];
		}
		
		$IDS = implode( ',', $storeIDs );
		
		//$tables = "`manufacturer` left join `core_url_rewrite` ON `manufacturer`.`url_key`=`core_url_rewrite`.`request_path`";
		$tables = $manufacturerTable;
		$wheres = " WHERE `$manufacturerTable`.store_id NOT IN ($IDS)";
		//$sql = "DELETE manufacturer.*, core_url_rewrite.* FROM $tables  $wheres";
		$sql = "DELETE  FROM $tables $wheres";
	
		$this->_getWriteAdapter()->query($sql);
	}
	
	public function getStoreManufacturer($manufacturerID,$storeID)
	{
		
		$manufacturer = Mage::getModel('manufacturer/manufacturer')->load($manufacturerID);
		$manufacturerName = $manufacturer->getData('name');
		
		$select = $this->_getReadAdapter()->select()
					->from(array('manu'=> $this->getTable('manufacturer')),'manufacturer_id')
					->where('manu.name=?',$manufacturerName)
					->where('manu.store_id=?',$storeID);		
		$newManufacturerID = $this->_getReadAdapter()->fetchOne($select);
		
		return $manufacturer->load($newManufacturerID);
	}			
	
	public function getCategoryParent($categoryID)
	{
		$prefix = Mage::helper('manufacturer')->getTablePrefix();	
		
		$select = $this->_getReadAdapter()->select()
					->from(array('manu'=> $prefix .'catalog_category_entity'),'*')
					->where('manu.name=?',$manufacturerName)
					->where('manu.store_id=?',$storeID);		
		$newManufacturerID = $this->_getReadAdapter()->fetchOne($select);			
	}
}