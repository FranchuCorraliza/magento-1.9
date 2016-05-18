<?php
/*
 * Developer: Rene Voorberg
* Team site: http://cmsideas.net/
* Support: http://support.cmsideas.net/
*
*
*/
class Glace_Ajaxcart_Model_Catalog_Product_Url extends Mage_Catalog_Model_Product_Url
{
 	public function getUrl(Mage_Catalog_Model_Product $product, $params = array())
    {
    	if (Mage::helper('ajaxcart')->isEnabled() && $params['_query']['options']=='cart') {
	    	return Mage::helper('checkout/cart')->getAddUrl($product, $params);
    	} else {
	    	return parent::getUrl($product, $params);
    	}
    }
	
}