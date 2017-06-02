<?php
	class Elite_ProductsDetails_Block_Details_Shareviaemail extends Mage_Directory_Block_Data
	{
		public function getProductId(){
			return Mage::app()->getRequest()->getParam('id');
		}
		public function getNameProduct(){
			$id=Mage::app()->getRequest()->getParam('id');
			$product=Mage::getModel('catalog/product')->load($id);
			if ($product){
				return $product->getName();
			}
		}
		public function getBrandProduct(){
			$id=Mage::app()->getRequest()->getParam('id');
			$product=Mage::getModel('catalog/product')->load($id);
			if ($product){
				return $product->getAttributeText('manufacturer');
			}
		}
		public function getImageProduct(){
			$id=Mage::app()->getRequest()->getParam('id');
			$product=Mage::getModel('catalog/product')->load($id);
			if ($product){
				return (string) $this->helper('catalog/image')->init($product, 'image')->resize(200);
			}
		}
	}