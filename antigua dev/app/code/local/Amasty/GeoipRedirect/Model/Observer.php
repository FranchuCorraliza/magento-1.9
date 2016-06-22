<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_GeoipRedirect
 */

class Amasty_GeoipRedirect_Model_Observer {

    protected $redirectAllowed = false;

    public function redirectStore($observer) {
        $controller = $observer->getControllerAction();
        $isApi = $controller->getRequest()->getControllerModule() == 'Mage_Api';
        if ($isApi || !Mage::helper('amgeoipredirect')->isModuleEnabled('Amasty_Geoip')) {
            return;
        }
        $userAgent = Mage::app()->getRequest()->getHeader('USER_AGENT');
        $userAgentsIgnore = Mage::getStoreConfig('amgeoipredirect/restriction/user_agents_ignore');
        if (!empty($userAgentsIgnore)) {
            $userAgentsIgnore = explode(',', $userAgentsIgnore);
            foreach ($userAgentsIgnore as $agent) {
                if ($userAgent && $agent && stripos($userAgent, $agent) !== false) {
                    return;
                }
            }
        }
        $addToUrl = $this->applyLogic();
        if (Mage::getStoreConfig('amgeoipredirect/general/enable') && $this->redirectAllowed) {
            $ip = Mage::helper('core/http')->getRemoteAddr();
            $location = Mage::getModel('amgeoip/geolocation')->locate($ip);
            $country = $location->getCountry();

            $session = Mage::getSingleton('customer/session');
            $getAmYetRedirectStore = $session->getAmYetRedirectStore();
            $getAmYetRedirectCurrency = $session->getAmYetRedirectCurrency();
            $allStores = Mage::app()->getStores();

            if (Mage::getStoreConfig('amgeoipredirect/country_url/enable_url')) {
                $urlMapping = unserialize(Mage::getStoreConfig('amgeoipredirect/country_url/url_mapping'));
                $currentUrl = Mage::helper('core/url')->getCurrentUrl();
                foreach ($urlMapping as $value) {
                    if ($value['country_url'] == $country && $value['url_mapping'] != $currentUrl) {
                        Mage::app()->getResponse()->setRedirect($value['url_mapping']);
                        Mage::app()->getResponse()->sendResponse();
                        exit;
                    }
                }
            }
            if (!$getAmYetRedirectStore && Mage::getStoreConfig('amgeoipredirect/country_store/enable_store')) {
                foreach ($allStores as $store) {
                    $currentUrl = $store->getCurrentUrl();
                    $redirectStoreUrl = trim($currentUrl, '/') . $addToUrl;
                    $countries = Mage::getStoreConfig('amgeoipredirect/country_store/affected_countries', $store->getId());
                    if ($country && $countries && strpos($countries, $country) !== false && $store->getId() != Mage::app()->getStore()->getId()) {
                        $session->setAmYetRedirectStore(1);
                        Mage::app()->setCurrentStore($store);
                        Mage::app()->getResponse()->setRedirect($redirectStoreUrl);
                    }
                }
            }
            if (!$getAmYetRedirectCurrency && Mage::getStoreConfig('amgeoipredirect/country_currency/enable_currency')) {
                $currencyMapping = unserialize(Mage::getStoreConfig('amgeoipredirect/country_currency/currency_mapping'));
                foreach ($currencyMapping as $value) {
                    if ($value['country_currency'] == $country && Mage::app()->getStore()->getCurrentCurrencyCode() != $value['currency']) {
                        $session->setAmYetRedirectCurrency(1);
                        Mage::app()->getStore()->setCurrentCurrencyCode($value['currency']);
                    }
                }
            }
        }
    }

    protected function applyLogic() {
        $applyLogic = Mage::getStoreConfig('amgeoipredirect/restriction/apply_logic');
        $currentUrl = Mage::helper('core/url')->getCurrentUrl();
        $baseUrl = Mage::app()->getStore()->getCurrentUrl();
        switch ($applyLogic) {
            case Amasty_GeoipRedirect_Model_Source_ApplyLogic::ALL_URLS :
                $this->redirectAllowed = true;
                $url = substr($currentUrl, strlen($baseUrl)-1);
                return $url;
                break;
            case Amasty_GeoipRedirect_Model_Source_ApplyLogic::SPECIFIED_URLS :
                $acceptedUrls = explode(PHP_EOL, Mage::getStoreConfig('amgeoipredirect/restriction/accepted_urls'));
                foreach ($acceptedUrls as $url) {
                    $url = trim($url);
                    if ($url && $currentUrl && strpos($currentUrl, $url)) {
                        $this->redirectAllowed = true;
                        return $url;
                    }
                }
                break;
            case Amasty_GeoipRedirect_Model_Source_ApplyLogic::EXCEPT_URLS :
                $exceptedUrls = explode(PHP_EOL, Mage::getStoreConfig('amgeoipredirect/restriction/excepted_urls'));
                foreach ($exceptedUrls as $url) {
                    $url = trim($url);
                    if ($url && $currentUrl && strpos($currentUrl, $url)) {
                        $this->redirectAllowed = false;
                        return $url;
                    } else {
                        $this->redirectAllowed = true;
                    }
                }
                break;
        }
        return '';
    }
}