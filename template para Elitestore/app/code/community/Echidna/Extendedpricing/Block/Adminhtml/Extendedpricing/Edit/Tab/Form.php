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
 
class Echidna_Extendedpricing_Block_Adminhtml_Extendedpricing_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{

		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset("extendedpricing_form", array("legend"=>Mage::helper("extendedpricing")->__("Price information")));              
                
                $fieldset->addField("sku", "text", array(
		"label" => Mage::helper("extendedpricing")->__("SKU"),			
		"class" => "required-entry",
		"required" => true,
		"name" => "sku",
		));
		$attributePriceregion = Mage::getModel('eav/config')->getAttribute("customer", 'priceregion');
                $priceregionOptions = $attributePriceregion->getSource()->getAllOptions();                
               
                
		$fieldset->addField("priceregion", "select", array(
		"label" => Mage::helper("extendedpricing")->__("Price Region"),	
                "values" =>$priceregionOptions,      
		"class" => "required-entry",
		"required" => true,
		"name" => "priceregion",
		));
                
                $attributePricebook = Mage::getModel('eav/config')->getAttribute("customer", 'pricebook');
                $pricebookOptions = $attributePricebook->getSource()->getAllOptions();
                
		$fieldset->addField("subpriceregion", "select", array(
		"label" => Mage::helper("extendedpricing")->__("Price Book"),
                "values" => $pricebookOptions,
		"class" => "required-entry",
		"required" => true,
		"name" => "subpriceregion",
		));
		
		$fieldset->addField("price", "text", array(
		"label" => Mage::helper("extendedpricing")->__("Price"),			
		"class" => "validate-number",
		"required" => true,
		"name" => "price",
		));
		
		$fieldset->addField("hidden_name", "hidden", array(			
		"name" => "hidden_name",
		));
				
				
		if (Mage::getSingleton("adminhtml/session")->getExtendedpricingData())
		{
			$form->setValues(Mage::getSingleton("adminhtml/session")->getExtendedpricingData());
			Mage::getSingleton("adminhtml/session")->setExtendedpricingData(null);
		} 
		elseif(Mage::registry("extendedpricing_data")) 
		{
			$form->setValues(Mage::registry("extendedpricing_data")->getData());					
			$data = Mage::registry('extendedpricing_data')->getData();
			$hidden_name = $data['name'];				
	
			$data['hidden_name'] = $hidden_name;
			$form->setValues($data);
			
		}
		
		return parent::_prepareForm();
	}
}
