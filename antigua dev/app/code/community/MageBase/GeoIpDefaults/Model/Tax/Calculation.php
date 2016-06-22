<?php
class MageBase_GeoIpDefaults_Model_Tax_Calculation extends Mage_Tax_Model_Calculation
{
 
    public function getRateRequest ($shippingAddress = null,
            $billingAddress = null, $customerTaxClass = null, $store = null)
    {
        $request = parent::getRateRequest($shippingAddress, $billingAddress, $customerTaxClass, $store);
		$countryCode = Mage::getSingleton('geoip/country')->getCountry();
        if ($countryCode) {
            if (((is_null($shippingAddress)) && is_null($billingAddress)) || ($shippingAddress === false && $billingAddress === false)) {
                $request->setCountryId($countryCode);
                $request->setRegionId(0);
                $request->setPostcode('*');
            } elseif (!$shippingAddress->getCountryId() && !$billingAddress->getCountryId()) {
                $request->setCountryId($countryCode);
                $request->setRegionId(0);
                $request->setPostcode('*');
            }
        }
        return $request;
    }
 
}