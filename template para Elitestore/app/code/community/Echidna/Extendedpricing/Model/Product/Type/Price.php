<?php
/**
 * Product type price model
 *  
 * Author@ Nimila Jose
 * Company@ Echidna Software Pvt Ltd
 * Purpose@ Custom Price List for B2B according to the Group.
 *
 */
 
class Echidna_Extendedpricing_Model_Product_Type_Price extends Mage_Catalog_Model_Product_Type_Price
{
   public function getPrice($product)
   { 
		if(Mage::getSingleton('customer/session')->isLoggedIn())
		{
			$customerData = Mage::getSingleton('customer/session')->getCustomer();
			
			$helper = Mage::helper('extendedpricing');
			
			$priceregion_id = $customerData->getPriceregion();
			$priceregion = $helper->customer_mainpricegrouplabel($priceregion_id);
			
			$pricebook_id = $customerData->getPricebook();
			$pricebook = $helper->customer_subpricegrouplabel($pricebook_id);
                  
			if(($priceregion) && ($pricebook))
			{   
				$SKU = $product->getSku();
				
				if(!$product->getSpecialPrice())
				{   
                      																			
					$pricelist = Mage::getModel('extendedpricing/extendedpricing')->getCollection()
								->addFieldToSelect('price')
								->addFieldToFilter('sku', array('eq' => $SKU))
								->addFieldToFilter('priceregion', array('eq' => $priceregion_id))
								->addFieldToFilter('subpriceregion', array('eq' => $pricebook_id));
								
					
					if($pricelist->getData())
					{ 
						foreach($pricelist->getData() as $data)
						{   
							return $data['price'];
						}
					}
					else
					{   
						return $product->getData('price');
					}
				}
				else
				{
					return $product->getData('price');
				}
			}
			else
			{
				return $product->getData('price');
			}
		}
		else
		{
			return $product->getData('price');
		}

   }
}
 
