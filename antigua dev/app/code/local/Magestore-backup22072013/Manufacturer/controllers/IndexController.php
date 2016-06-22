<?php
class Magestore_Manufacturer_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
	//	var_dump(Mage::helper('manufacturer')->getCollectionActiveStoreID());die();
		Mage::helper('manufacturer')->autoUpdateManufacturerFormCatalog();
		$this->loadLayout();   
		$this->renderLayout();
	}
	
	public function viewAction()
    {
		$catid = $this->getRequest()->getParam("catid");
		$id = $this->getRequest()->getParam("id");
		if($catid || $id)
			Mage::getSingleton('manufacturer/manufacturer')->addFilters($catid, $id);
		$this->loadLayout();  
		if($id)
		{
			$title = "";
			if($catid)
			{
				$cat = Mage::getModel('catalog/category')->load($catid);
				$title = $cat->getName() .' - ';
			}
			$block = $this->getLayout()->getBlock('head');
			//$keywords = $block->getKeywords();
		//	$keywords = "";
			//$description = $block->getDescription();
		//	$description ="";
			
			$manufacturerID = Mage::getModel('manufacturer/manufacturer')->getManufacturerID($id);
			$manufacturer = Mage::getModel('manufacturer/manufacturer')->load($manufacturerID);
		
			/*
			$this->getLayout()->getBlock('manufacturer.view')->generateCategoryTree($manufacturer);
			$categories =  $this->getLayout()->getBlock('manufacturer.view')->getData('categories');
			if(count($categories))
				$title = Mage::helper('manufacturer')->getMaxItem($categories,'level')->getName() .'-';
			*/
			
			$manufacturer = $manufacturer->loadDataManufacturer($manufacturer);
			$newMetaKeywords = $manufacturer->getData('meta_keywords');
			
			$newMetaDescription = $manufacturer->getData('meta_description');
			$block->setKeywords($newMetaKeywords );
			
			$block->setDescription($newMetaDescription );
			$block->setTitle($title . $manufacturer->getData('name_store'));
		}
		$this->renderLayout();
    }
}