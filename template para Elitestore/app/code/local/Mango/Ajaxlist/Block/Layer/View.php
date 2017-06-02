<?php
	class Mango_Ajaxlist_Block_Layer_View extends Mage_Catalog_Block_Layer_View{
		
		public function hasSaleItems(){
			$result=false;
			$currentCategory=Mage::registry('current_category');
			$manufacturer=Mage::app()->getRequest()->getParam('manufacturer');
			if ($currentCategory){
				$saleId=$currentCategory->getData('sale_id');
				if ($saleId!=0){
					$category=Mage::getModel('catalog/category')->load($saleId);
					if ($category){
						$collection=$category->getProductCollection();
						if ($manufacturer){
							$collection->addAttributeToFilter('manufacturer', array('eq'=>$manufacturer));
							
						}
						$result=(count($collection)>0);
					}
				}
			}
			return $result;
			
		}
		public function hasOutletItems(){
			$result=false;
			$currentCategory=Mage::registry('current_category');
			$manufacturer=Mage::app()->getRequest()->getParam('manufacturer');
			if ($currentCategory){
				$saleId=$currentCategory->getData('outlet_id');
				if ($saleId!=0){
					$category=Mage::getModel('catalog/category')->load($saleId);
					if ($category){
						$collection=$category->getProductCollection();
						if ($manufacturer){
							$collection->addAttributeToFilter('manufacturer', array('eq'=>$manufacturer));
							
						}
						$result=(count($collection)>0);
					}
				}
			}
			return $result;
		}
		public function getSaleLink(){
			$categoryUrl="";
			$currentCategory=Mage::registry('current_category');
			$manufacturer=Mage::app()->getRequest()->getParam('manufacturer');
			if ($currentCategory){
				$saleId=$currentCategory->getData('sale_id');
				if ($saleId!=0){
					$category=Mage::getModel('catalog/category')->load($saleId);
					if ($category){
						if ($manufacturer){
							$resource = Mage::getSingleton('core/resource');
							$readConnection = $resource->getConnection('core_read');
							$query = 'SELECT url_key FROM ' . $resource->getTableName('manufacturer/manufacturer').' WHERE option_id='.$manufacturer;
							$manufacturerUrlKey = $readConnection->fetchOne($query);
							$categoryUrlPath=$category->getUrlPath();
							$categoryUrl=Mage::getBaseUrl().$manufacturerUrlKey.'/'.$categoryUrlPath;
						}else{
							$categoryUrl=$category->getUrl();
						}	
					}
				}
			}
			return $categoryUrl;
		}
		
		public function getOutletLink(){
			$categoryUrl="";
			$currentCategory=Mage::registry('current_category');
			$manufacturer=Mage::app()->getRequest()->getParam('manufacturer');
			if ($currentCategory){
				$saleId=$currentCategory->getData('outlet_id');
				if ($saleId!=0){
					$category=Mage::getModel('catalog/category')->load($saleId);
					if ($category){
						if ($manufacturer){
							$resource = Mage::getSingleton('core/resource');
							$readConnection = $resource->getConnection('core_read');
							$query = 'SELECT url_key FROM ' . $resource->getTableName('manufacturer/manufacturer').' WHERE option_id='.$manufacturer;
							$manufacturerUrlKey = $readConnection->fetchOne($query);
							$categoryUrlPath=$category->getUrlPath();
							$categoryUrl=Mage::getBaseUrl().$manufacturerUrlKey.'/'.$categoryUrlPath;
						}else{
							$categoryUrl=$category->getUrl();
						}	
					}
				}
			}
			return $categoryUrl;
		}
		
	}