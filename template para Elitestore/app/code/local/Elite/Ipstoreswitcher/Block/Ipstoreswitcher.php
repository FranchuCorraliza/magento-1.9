<?php
class Elite_Ipstoreswitcher_Block_Ipstoreswitcher extends Mage_Page_Block_Html
{
	/**
    * redirects customer to store view based on GeoIP
    * @param $event
    */
	public function isEnabledCookies(){
		$cookie = Mage::getSingleton('core/cookie');
		$cookies=$cookie->get();
		if($cookies){
			return true;
		}else{
			return false;
		}
		
	}
	
    public function setCookiePorGeoip()
    {	
		$cookie = Mage::getSingleton('core/cookie');
		if ($this->isEnabledCookies()){
			if ($cookie->get('geoip_processed') != 1) {
				$geoIPCountry = Mage::getSingleton('geoip/country');
				$countryCode = $geoIPCountry->getCountry();
				if (!$countryCode) {
					$countryCode="US";
				}
				$storeId = Mage::helper('ipstoreswitcher')->getStoreByCountry($countryCode);//llamamos al helper dentro del modulo con this
				if ($storeId) {
					$store = Mage::getModel('core/store')->load($storeId[0]);
					if(is_array ($storeId)){
						$store = Mage::getModel('core/store')->load($storeId[0]);
					}else{
						$store = Mage::getModel('core/store')->load($storeId);
					}
					$store->getStoreId();
					$countryList = Mage::getModel('directory/country')->getResourceCollection()
							->loadByStore($store->getStoreId())
							->toOptionArray(true);
					$nombrePais="";
					foreach ($countryList as $country) {
						if($country['value']==$countryCode)
						{
							$nombrePais=$country['label'];
						}	
					}
					$cookie->set('geoip_nombre', $nombrePais, time() + 86400, '/');
					$cookie->set('geoip_tienda', $storeId[0], time() + 86400, '/');
					$cookie->set('geoip_bandera', $countryCode, time() + 86400, '/');
					$cookie->set('geoip_idioma', Mage::getStoreConfig('general/locale/code', $store->getStoreId()) , time() + 86400, '/');
					$cookie->set('geoip_moneda', $store->getCurrentCurrencyCode(), time() + 86400, '/');
					$cookie->set('geoip_processed', '1', time() + 86400, '/');
					$nombre=$nombrePais;
					$banderita = $countryCode;
					$url=$store->getCurrentUrl(false);
					Mage::app()->getFrontController()->getResponse()->setRedirect($url);
					}
					
				$cookie->set('geoip_processed', '1', time() + 86400, '/');
			}else{
				$tiendaActual = Mage::app()->getStore()->getStoreId();
				$tiendaCookie = $cookie->get('geoip_tienda');
				$storeActual = Mage::getModel('core/store')->load($tiendaActual)->getCurrentUrl(false);
				$storeCookie = explode("/",Mage::getModel('core/store')->load($tiendaCookie)->getCurrentUrl(false));
				$urlNavegador = explode("/","http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
				if($urlNavegador[3]!=$storeCookie[3])
				{
					$store = Mage::getModel('core/store')->load($tiendaCookie);
					$url=$store->getCurrentUrl(false);
					Mage::app()->getFrontController()->getResponse()->setRedirect($url);
				}
			}
		}
    }
}