<?php

class Openstream_CustomListing_Block_Attribute extends Openstream_CustomListing_Block_Abstract
{
    protected function _getProductCollection()
    {
        if (($attributeCode = $this->getAttributeCode()) && ($attributeValue = $this->getValue())) {
            if (is_null($this->_productCollection)) {
                $this->_productCollection = Mage::getResourceModel('reports/product_collection');
                $this->_productCollection->addAttributeToFilter($attributeCode, array('eq' => $attributeValue))
                                         ->addAttributeToSelect('*')
                                         ->addStoreFilter();
			$this->_addProductAttributesAndPrices($this->_productCollection);
	        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($this->_productCollection);
    	    $this->setProductCollection($this->_productCollection);	
            }
        }
        return parent::_getProductCollection();
    }
}
