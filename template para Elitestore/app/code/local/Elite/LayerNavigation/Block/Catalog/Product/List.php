<?php
class Elite_LayerNavigation_Block_Catalog_Product_List extends Mage_Catalog_Block_Product_List
{
	public function getLoadedProductCollection()
    {
		return $this->_getProductCollection();
    }
	
}
			