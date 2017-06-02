<?php 
	class Elite_OrderByRequest_Block_Form extends Mage_Directory_Block_Data{
		
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
		public function getSizeProduct(){
			$tallaId=Mage::app()->getRequest()->getParam('talla');
			$id=Mage::app()->getRequest()->getParam('id');
			$product=Mage::getModel('catalog/product')->load($id);
			if ($product){
				$attr=$product->getResource()->getAttribute('talla');
				if ($attr->usesSource()){
					$talla=$attr->getSource()->getOptionText($tallaId);
				}
				$talla.=" ".$product->getTallaje();
				return $talla;
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