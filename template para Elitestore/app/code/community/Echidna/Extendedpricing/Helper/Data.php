<?php
/**
 * Product type price model
 *  
 * Author@ Nimila Jose
 * Company@ Echidna Software Pvt Ltd
 * Purpose@ Custom Price List for B2B according to the Group.
 *
 */
 
 
class Echidna_Extendedpricing_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function customer_mainpricegrouplabel($main_price_group_id)
	{
		$maingroup_id = $main_price_group_id;
		$maingroup_attribute = 'priceregion';		
		$allOptions_maingroup_attribute = Mage::getResourceModel('customer/customer')->getAttribute($maingroup_attribute)->getSource()->getAllOptions();
		foreach ($allOptions_maingroup_attribute as $allOptions_maingroup) 
		{
			if($allOptions_maingroup['value'] == $maingroup_id)
			{
				$maingroup = $allOptions_maingroup['label'];
				break;
			}
		}
		
		return $maingroup;
	}
	
	public function customer_subpricegrouplabel($sub_price_group_id)
	{
		$subgroup_id = $sub_price_group_id;
		$subgroup_attribute = 'pricebook';		
		$allOptions_subgroup_attribute = Mage::getResourceModel('customer/customer')->getAttribute($subgroup_attribute)->getSource()->getAllOptions();
		foreach ($allOptions_subgroup_attribute as $allOptions_subgroup) 
		{
			if($allOptions_subgroup['value'] == $subgroup_id)
			{
				$subgroup = $allOptions_subgroup['label'];
				break;
			}
		}
		
		return $subgroup;
		
	}

    public function priceregion()
        {
            $attribute = Mage::getModel('eav/config')->getAttribute('customer', 'priceregion');
            $allOptions= $attribute->getSource()->getAllOptions(true, true);
                    
                foreach ($allOptions as $instance) {
                        $priceregion[$instance['value']] = $instance['label'];
                       }
                return $priceregion;
       }
       
    public function pricebook()
        {
            $attribute = Mage::getModel('eav/config')->getAttribute('customer', 'pricebook');
            $allOptions= $attribute->getSource()->getAllOptions(true, true);
                    foreach ($allOptions as $instance) {
                        $pricebook[$instance['value']] = $instance['label'];
                        }
                 return $pricebook;
       }
       
}
