<?php
/*
 * Developer: Rene Voorberg
* Team site: http://cmsideas.net/
* Support: http://support.cmsideas.net/
*
*
*/
require_once Mage::getModuleDir('controllers', 'Mage_Catalog').DS.'Product'.DS.'CompareController.php';
class Glace_Ajaxcart_Product_CompareController extends Mage_Catalog_Product_CompareController
{	

	protected function _redirectReferer()
	{
		$action = Mage::app()->getFrontController()->getRequest()->getActionName();
		
		if ($action == 'remove' || $action == 'clear'){
			// Load messages in background in case of errors
			Mage::getSingleton('catalog/session')->getMessages(true);		
		} 
		
        $result = array();
		if ($action != 'remove' && $action != 'clear'){
			$result['popup'] = 'success';
			$result['is_action'] = 'compare';	
			$result['update_section']['html_layout_messages'] = $this->_getLayoutMessagesHtml();
			$result['update_section']['compare_onclick'] = "popWin('".Mage::helper('catalog/product_compare')->getListUrl()."', 'compare', 'top:0, left:0, width=820, height=600, resizable=yes, scrollbars=yes');ajaxcartTools.hidePopup('success', true);";
		}   
		
		$result['update_section']['html_compare'] = Mage::helper('ajaxcart')->getCompareHtml($this);
		
		//update compare popup
		if ($this->getRequest()->getParam('is_compare_popup', false)) {
			$result['update_section']['html_compare_popup'] = Mage::helper('ajaxcart')->getComparePopupHtml($this);
		}
		
		$this->getResponse()->setHeader('Content-Type', 'text/plain');
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
	}
	
    protected function _getLayoutMessagesHtml()
    {        
	    Mage::app()->getCacheInstance()->cleanType('layout');
		/* Mage::getModel('ajaxcart/ajaxcart')->loadQuoteMessages(); */
        $layout = $this->getLayout();   
        if (Mage::helper('ajaxcart')->getMagentoVersion()>1411) {     
			$this->_initLayoutMessages(array('checkout/session', 'catalog/session', 'customer/session'));
		}
        $update = $layout->getUpdate();
        $update->load('ajaxcart_index_messages');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }
	
}