<?php 
class Elite_Nostocklist_Block_Nostocklist extends Mage_Catalog_Block_Product_List{
	public function getInStockProducts($_productCollection){
		$inStockCollection=array();
		foreach ($_productCollection as $product){
			if ($product){
				$product = Mage::getModel('catalog/product')->load($product->getId()); 
				$isInStock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getIsInStock();
				if ($isInStock){
					$inStockCollection[]=$product->getId();
				}
			}
		}
		return $inStockCollection;
	}
}