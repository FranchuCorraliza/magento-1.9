<?php
/**
 * 
 *
 *
 * Author@ Nimila Jose
 * Company@ Echidna Software Pvt Ltd
 * Purpose@ Extended Pricing Sheet
 * 
 *
 */
	
class Echidna_Extendedpricing_Block_Adminhtml_Extendedpricing_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{
			parent::__construct();
			$this->_objectId = "id";
			$this->_blockGroup = "extendedpricing";
			$this->_controller = "adminhtml_extendedpricing";
			$this->_updateButton("save", "label", Mage::helper("extendedpricing")->__("Save Item"));
			$this->_updateButton("delete", "label", Mage::helper("extendedpricing")->__("Delete Item"));

		}

		public function getHeaderText()
		{
			if( Mage::registry("extendedpricing_data") && Mage::registry("extendedpricing_data")->getId() )
			{
				return Mage::helper("extendedpricing")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("extendedpricing_data")->getId()));
			} 
			else
			{
				return Mage::helper("extendedpricing")->__("Add Item");
			}
		}
}