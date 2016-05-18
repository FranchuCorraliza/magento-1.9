<?php
/*
 * Developer: Rene Voorberg
* Team site: http://cmsideas.net/
* Support: http://support.cmsideas.net/
*
*
*/
class Glace_Ajaxcart_Block_Wishlist_Customer_Sidebar extends Mage_Wishlist_Block_Customer_Sidebar
{    
	//Prepare before to html          
	//If customer is logged in, display wishlist even if no items are available
	protected function _toHtml()    
	{        
		if (Mage::helper('ajaxcart')->isEnabled() && Mage::getSingleton('customer/session')->isLoggedIn()) {		
			return Mage_Wishlist_Block_Abstract::_toHtml();    
		} else {
			return parent::_toHtml();
		}
	}	
}