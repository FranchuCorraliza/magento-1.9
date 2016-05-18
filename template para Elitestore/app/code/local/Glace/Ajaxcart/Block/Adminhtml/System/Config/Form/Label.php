<?php 
/*
 * Developer: Rene Voorberg
* Team site: http://cmsideas.net/
* Support: http://support.cmsideas.net/
*
*
*/
class Glace_Ajaxcart_Block_Adminhtml_System_Config_Form_Label extends Mage_Adminhtml_Block_System_Config_Form_Field 
{ 
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) 
	{ 
		$html = ''; 
		if (!$this->getRequest()->getParam('website', false) && !$this->getRequest()->getParam('store', false)) 
		{ 
			$label = 'Please enter the license code(s) for the domains in which you want to use our extension.'; 
		} else { 
			$label = 'The licenses can only be entered for the Default Configuration Scope. Please change the Current Configuration Scope from the top left dropdown.'; 
		} 
		$html.=''.$label.''."\n"; 
		return $html; 
	} 
	public function render(Varien_Data_Form_Element_Abstract $element) 
	{ 
		$html = ''; 
		$html .= $this->_getElementHtml($element); 
		return $html; 
	} 

}