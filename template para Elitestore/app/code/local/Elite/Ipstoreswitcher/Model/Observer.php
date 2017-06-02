<?php

/* app/code/local/Elite/Ipstoreswitcher/Model/Observer.php */
 
class Elite_Ipstoreswitcher_Model_Observer
{
    /**
     * redirects customer to store view based on GeoIP
     * @param $event
     */
    public function controllerActionPostdispatch($event)
    {
        $cookie = Mage::getSingleton('core/cookie');
		
        if ($cookie->get('geoip_processed') != 1) {
            $geoIPCountry = Mage::getSingleton('geoip/country');
            $countryCode = $geoIPCountry->getCountry();
			if ($countryCode) {
                $storeId = Mage::helper('elite_ipstoreswitcher')->getStoreByCountry($countryCode);
                if ($storeId) {
					$store = Mage::getModel('core/store')->load($storeId);
					
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
						//opciones para ver la bandera y demas
						//$cookie->set('geoip_nombre', $this->__(Mage::getModel('directory/country')->loadByCode($countryCode)->getName()), time() + 86400, '/');
						$cookie->set('geoip_nombre', $nombrePais, time() + 86400, '/');
						$cookie->set('geoip_bandera', $countryCode, time() + 86400, '/');
						$cookie->set('geoip_idioma', Mage::getStoreConfig('general/locale/code', $storeId) , time() + 86400, '/');
						$cookie->set('geoip_moneda', $store->getCurrentCurrencyCode(), time() + 86400, '/');
                    
					
					
                    if ($store->getName() != Mage::app()->getStore()->getName()) {
                        $event->getControllerAction()->getResponse()->setRedirect($store->getCurrentUrl(false));
                    }
                }
            }
						
            $cookie->set('geoip_processed', '1', time() + 86400, '/');

        }
    }
}