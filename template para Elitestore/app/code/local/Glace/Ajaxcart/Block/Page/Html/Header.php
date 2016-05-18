<?php
/*
 * Developer: Rene Voorberg
* Team site: http://cmsideas.net/
* Support: http://support.cmsideas.net/
*
*
*/
class Glace_Ajaxcart_Block_Page_Html_Header extends Mage_Page_Block_Html_Header
{
    public function getWelcome()
    {
		if (Mage::helper('ajaxcart')->isEnabled()) {		
			return '<span id="ac-welcome-message">'.parent::getWelcome().'</span>';    
		} else {
			return parent::getWelcome();
		}
    }
    
}