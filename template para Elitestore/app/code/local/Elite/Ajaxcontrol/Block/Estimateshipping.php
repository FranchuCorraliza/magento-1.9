<?php 
class Elite_Ajaxcontrol_Block_Estimateshipping extends Mage_Checkout_Block_Cart_Shipping
{
	public function getEstimateRates()
    {
		
        if (empty($this->_rates)) {
			$groups = $this->getAddress()->getGroupedAllShippingRates();
            $this->_rates = $groups;
        }
		
        return $this->_rates;
    }
}