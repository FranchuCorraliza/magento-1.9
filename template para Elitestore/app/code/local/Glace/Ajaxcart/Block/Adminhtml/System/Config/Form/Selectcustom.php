<?php
/*
 * Developer: Rene Voorberg
 * Team site: http://cmsideas.net/
 * Support: http://support.cmsideas.net/
 * 
 *
*/
class Glace_Ajaxcart_Block_Adminhtml_System_Config_Form_Selectcustom extends Glace_Ajaxcart_Block_Adminhtml_System_Config_Form_Select
{    
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
    	$templateFile = Mage::getBaseDir('design') . DS . 'frontend' . DS . 'ultimo';
    	if (!file_exists($templateFile)) {
	    	$script = '<script type="text/javascript">	
	    					var group = document.getElementById(\'config_edit_form\').getElementsByClassName(\'entry-edit\')[0].lastChild; 
	    					group.style.display="none"; 
	    			   </script>';
	    }
    	
        return $element->getElementHtml().$script;
    }  
}
 