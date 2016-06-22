<?php
class Ameex_Freeship_Model_Carrier_Freeshipment extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface
{
		protected $_code = 'ameex_freeship';	
		public function collectRates(Mage_Shipping_Model_Rate_Request $request)	{
			 if (!$this->getConfigFlag('active')) {
				return false;
			}
				$result = Mage::getModel('shipping/rate_result');
					if(Mage::getSingleton('customer/session')->isLoggedIn()){
					$groupid=Mage::getSingleton('customer/session')->getCustomerGroupId();
					}else{
					$groupid='0';
					}
				$customergroupconfigdata=$this->getConfigData('customergroup');
				$grouparray=explode(',',$customergroupconfigdata);			
					if (($request->getBaseSubtotalInclTax() >= $this->getConfigData('ameex_free_shipping_subtotal') && in_array($groupid,$grouparray))
				) {	
				   $method = Mage::getModel('shipping/rate_result_method');

					$method->setCarrier('freeshipping');
					$method->setCarrierTitle($this->getConfigData('title'));

					$method->setMethod('freeshipping');
					$method->setMethodTitle($this->getConfigData('name'));

					$method->setPrice('0.00');
					$method->setCost('0.00');	
				$result->append($method);			
				}		
		return $result;
		}
    public function getAllowedMethods()
    {
        return array('ameex_freeship' => $this->getConfigData('name'));
    }
		
}