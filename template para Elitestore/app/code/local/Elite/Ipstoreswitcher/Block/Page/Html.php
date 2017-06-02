<?php
class Elite_Ipstoreswitcher_Block_Page_Html extends Mage_Page_Block_Html
{
	/**
    * redirects customer to store view based on GeoIP
    * @param $event
    */
    public function setCookiePorGeoip()
    {
		$cookie = Mage::getSingleton('core/cookie');
        if ($cookie->get('geoip_processed') != 1) {
			$geoIPCountry = Mage::getSingleton('geoip/country');
            $countryCode = $geoIPCountry->getCountry();
			if ($countryCode) {
                $storeId = $this->getStoreByCountry($countryCode);//llamamos al helper dentro del modulo con this
				
                if ($storeId) {
					$store = Mage::getModel('core/store')->load($storeId[0]);
					$countryList = Mage::getModel('directory/country')->getResourceCollection()
                            ->loadByStore()
                            ->toOptionArray(true);
					$nombrePais="";
					foreach ($countryList as $country) {
						if($country['value']==$countryCode)
						{
							$nombrePais=$country['label'];
						}
					}	
						$cookie->set('geoip_nombre', $nombrePais, time() + 86400, '/');
						$cookie->set('geoip_bandera', $countryCode, time() + 86400, '/');
						$cookie->set('geoip_idioma', Mage::getStoreConfig('general/locale/code', $storeId) , time() + 86400, '/');
						$cookie->set('geoip_moneda', $store->getCurrentCurrencyCode(), time() + 86400, '/');
                    //if ($store->getName() != Mage::app()->getStore()->getName()) {
                        //$event->getControllerAction()->getResponse()->setRedirect($store->getCurrentUrl(false));
						$cookie->set('geoip_direccion', $store->getCurrentUrl(false), time() + 86400, '/');
						Mage::app()->getFrontController()->getResponse()->setRedirect($store->getCurrentUrl(false));
                    //}
                }
            }	
            $cookie->set('geoip_processed', '1', time() + 86400, '/');
        }
    }
}