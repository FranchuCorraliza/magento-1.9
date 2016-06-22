<?php

/**
 * WebShopApps Shipping Module
 *
 * @category    WebShopApps
 * @package     WebShopApps_Tracker
 * User         Amish Patel
 * Date         11/03/2014
 * Time         13:21
 * @copyright   Copyright (c) 2014 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2014, Zowta, LLC - US license
 * @license     http://www.WebShopApps.com/license/license.txt - Commercial license
 *
 */
class Webshopapps_Tracker_Helper_Data extends Mage_Core_Helper_Abstract
{

    public function getTrackUrl($title, $trackref = null, $postcode = null)
    {
        if (empty($trackref)) {
            return null;
        }

        $fullUrl = "";

        $carrierInstances = Mage::getSingleton('shipping/config')->getAllCarriers(Mage::app()->getStore()->getId());

        foreach ($carrierInstances as $code => $carrier) {
            if ($carrier->isTrackingAvailable()) {
                if ($carrier->getConfigData('title') == $title) {
                    $manualUrl = $carrier->getConfigData('url');
                    $preUrl = $carrier->getConfigData('preurl');
                    if ($preUrl != 'none') {
                        $taggedUrl = $carrier->getCode('tracking_url', $preUrl);
                    } else {
                        $taggedUrl = $manualUrl;
                    }
                    if (strpos($taggedUrl, '#SPECIAL#')) {
                        $taggedUrl = str_replace("#SPECIAL#", "", $taggedUrl);
                        $fullUrl = str_replace("#TRACKNUM#", "", $taggedUrl);
                    } else {
                        $fullUrl = str_replace("#TRACKNUM#", $trackref, $taggedUrl);
                        if ($postcode && strpos($taggedUrl, '#POSTCODE#')) {
                            $fullUrl = str_replace("#POSTCODE#", $postcode, $fullUrl);
                        }
                    }

                    break;
                }
            }
        }

        return $fullUrl;
    }
}