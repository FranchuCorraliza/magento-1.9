<?php
/*
 * Developer: Rene Voorberg
* Team site: http://cmsideas.net/
* Support: http://support.cmsideas.net/
*
*
*/
class Glace_Ajaxcart_Block_Checkout_Cart_Item_Renderer extends Mage_Checkout_Block_Cart_Item_Renderer
{
    
    //Get quote item qty
    //Add qty input with increase/decrease buttons if enabled 
    public function getQty()
    {
    	if (Mage::helper('core')->isModuleEnabled('Glace_Ajaxcart')) {
	    	return Mage::helper('ajaxcart')->getQty($this, parent::getQty()); 
	    } else {
		    return parent::getQty();
	    }
    }   
    
}