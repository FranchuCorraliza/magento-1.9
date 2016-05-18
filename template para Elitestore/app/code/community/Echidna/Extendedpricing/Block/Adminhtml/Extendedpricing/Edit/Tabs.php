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
 
class Echidna_Extendedpricing_Block_Adminhtml_Extendedpricing_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct()
	{
			parent::__construct();
			$this->setId("extendedpricing_tabs");
			$this->setDestElementId("edit_form");
			$this->setTitle(Mage::helper("extendedpricing")->__("Price Information"));
	}
	protected function _beforeToHtml()
	{
			$this->addTab("form_section", array(
			"label" => Mage::helper("extendedpricing")->__("Price Information"),
			"title" => Mage::helper("extendedpricing")->__("Price Information"),
			"content" => $this->getLayout()->createBlock("extendedpricing/adminhtml_extendedpricing_edit_tab_form")->toHtml(),
			));
			return parent::_beforeToHtml();
	}

}
