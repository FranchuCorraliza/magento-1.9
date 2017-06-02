<?php
/**
 * Magento Webshopapps Module
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.

 *
 * @category   Webshopapps
 * @package    Webshopapps_Tracker
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license   www.webshopapps.com/license/license.txt
 * @author    Karen Baker <sales@webshopapps.com>
 *
 */
abstract class Webshopapps_Tracker_Model_Carrier_Abstract
    extends Mage_Shipping_Model_Carrier_Abstract
{

	/**
	* Determins if tracking is set in the admin panel
	**/
	public function isTrackingAvailable()
    {
    	if (!$this->getConfigFlag('active')) {
            return false;
        }
		return true;
    }

/**
	* Dummy method - need to make this carrier work.
	* But tracker is only used for tracking - not for sending!
	**/
	 public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
    	return Mage::getModel('shipping/rate_result');
	}


	public function getTrackingInfo($tracking, $postcode=null)
    {
        $info = array();

        $result = $this->getTracking($tracking, $postcode);

        if($result instanceof Mage_Shipping_Model_Tracking_Result){
            if ($trackings = $result->getAllTrackings()) {
                return $trackings[0];
            }
        }
        elseif (is_string($result) && !empty($result)) {
            return $result;
        }

        return false;
    }

	public function getTracking($trackings, $postcode=null)
    {
    	$this->setTrackingReqeust();

        if (!is_array($trackings)) {
            $trackings=array($trackings);
        }
 		$this->_getCgiTracking($trackings, $postcode);

       return $this->_result;
    }

    protected function setTrackingReqeust()
    {
        $r = new Varien_Object();

        $id = $this->getConfigData('id');
        $r->setId($id);

        $this->_rawTrackRequest = $r;
    }

	/** Popup window to tracker **/
  protected function _getCgiTracking($trackings, $postcode=null)
    {

        $result = Mage::getModel('shipping/tracking_result');
        //try

        $defaults = $this->getDefaults();
        foreach($trackings as $tracking){
            $status = Mage::getModel('shipping/tracking_result_status');
            $status->setCarrier('Tracker');
            $status->setCarrierTitle($this->getConfigData('title'));
            $status->setTracking($tracking);
            $status->setPopup(1);
            $manualUrl=$this->getConfigData('url');
            $preUrl=$this->getConfigData('preurl');
            if ($preUrl!='none') {
            	$taggedUrl = $this->getCode('tracking_url',$preUrl);
            } else {
            	$taggedUrl = $manualUrl;
            }
        	if (strpos($taggedUrl, '#SPECIAL#'))
            {
            	$taggedUrl=str_replace("#SPECIAL#","",$taggedUrl);
            	$fullUrl=str_replace("#TRACKNUM#","",$taggedUrl);
            }
            else
            {
	            $fullUrl=str_replace("#TRACKNUM#",$tracking,$taggedUrl);
	            if ($postcode && strpos($taggedUrl, '#POSTCODE#'))
	            {
	            	$fullUrl = str_replace("#POSTCODE#",$postcode,$fullUrl);
	            }
            }
            $status->setUrl($fullUrl);
          //  $status->setUrl("http://www.parcelforce.com/portal/pw/track?trackNumber=$tracking");
            $result->append($status);
        }

        $this->_result = $result;
        return $result;
    }


	public function getCode($type, $code='')
    {
        $codes = array(

            'preurl'=>array(
                'none' => Mage::helper('shipping')->__('Use Manual Url'),
                'ddt' => Mage::helper('shipping')->__('DDT (Italy)'),
        		'post_danmark' => Mage::helper('shipping')->__('Post Danmark (Denmark)'),
        		'tnt'  => Mage::helper('shipping')->__('TNT (Netherlands)'),
                'dhl_de'  => Mage::helper('shipping')->__('DHL (Germany)'),
        		'dpd_de'  => Mage::helper('shipping')->__('DPD (Germany)'),
            	'gls'  => Mage::helper('shipping')->__('GLS (Germany)'),
        		'apc' => Mage::helper('shipping')->__('APC (UK)'),
        		'dpd_uk'  => Mage::helper('shipping')->__('DPD (UK)'),
        		'dhl_uk'  => Mage::helper('shipping')->__('DHL (UK)'),
        		'fedex'  => Mage::helper('shipping')->__('Fedex (UK)'),
            	'fedex_us'  => Mage::helper('shipping')->__('Fedex (USA)'),
                'parcelforce' => Mage::helper('shipping')->__('Parcelforce (UK)'),
        		'royal_mail' => Mage::helper('shipping')->__('Royal Mail (UK)'),
                'uk_mail'  => Mage::helper('shipping')->__('UK Mail (UK)'),
                'tnt_uk'  => Mage::helper('shipping')->__('TNT (UK)'),
                'usps_usa'  => Mage::helper('shipping')->__('USPS (USA)'),
        ),

        	'tracking_url'=>array(
        		'ddt' => Mage::helper('shipping')->__('http://www.DDT.com/portal/pw/track?trackNumber=#TRACKNUM#'),
          		'post_danmark' => Mage::helper('shipping')->__('http://www.postdanmark.dk/tracktrace/TrackTrace.do?i_stregkode=#TRACKNUM#&i;_lang=IND'),
        		'tnt'  => Mage::helper('shipping')->__('https://securepostplaza.tntpost.nl/TPGApps/tracktrace/findByBarcodeServlet?BARCODE=#TRACKNUM#&ZIPCODE=#POSTCODE#'),
        		'dpd_de'  => Mage::helper('shipping')->__('http://extranet.dpd.de/cgi-bin/delistrack?pknr=#TRACKNUM#&typ=1&lang=de'),
        		'dhl_de'  => Mage::helper('shipping')->__('http://nolp.dhl.de/nextt-online-public/set_identcodes.do?lang=de&idc=#TRACKNUM#'),
        		'gls'  => Mage::helper('shipping')->__('http://www.gls-group.eu/276-I-PORTAL-WEB/content/GLS/DE03/DE/5001.htm?txtAction=71000&txtQuery=#TRACKNUM#&x=0&y=0'),
        		'apc' => Mage::helper('shipping')->__('https://emea.netdespatch.com/mba/9116x0/track/?type=1&ref=#TRACKNUM#'),
        		'dpd_uk'  => Mage::helper('shipping')->__('http://www.dpd.co.uk/tracking/trackingSearch.do?search.searchType=0&search.parcelNumber=#TRACKNUM#'),
                'dhl_uk'  => Mage::helper('shipping')->__('http://www.dhl.co.uk/content/gb/en/express/tracking.shtml?brand=DHL&AWB=#TRACKNUM#'),
        		'fedex'  => Mage::helper('shipping')->__('http://www.fedexuk.net/accounts/QuickTrack.aspx?consignment=#TRACKNUM#'),
        		'fedex_us'  => Mage::helper('shipping')->__('http://www.fedex.com/Tracking?language=english&cntry_code=us&tracknumbers=#TRACKNUM#'),
                'parcelforce' => Mage::helper('shipping')->__('http://www.parcelforce.com/portal/pw/track?trackNumber=#TRACKNUM#'),
                'royal_mail' => Mage::helper('shipping')->__('http://www.royalmail.com/track-trace/?trackNumber=#TRACKNUM#'),
                'uk_mail'  => Mage::helper('shipping')->__('https://www.ukmail.com/manage-my-delivery/manage-my-delivery/?con=#TRACKNUM#'),
                'tnt_uk'  => Mage::helper('shipping')->__('http://www.kiosk.tnt.com/webshipper/doTrack.asp?C=#TRACKNUM#&L=EN'),
                'usps_usa'  => Mage::helper('shipping')->__('https://tools.usps.com/go/TrackConfirmAction_input?qtc_tLabels1=#TRACKNUM#'),
        ),

        );

        if (!isset($codes[$type])) {
            throw Mage::exception('Mage_Shipping', Mage::helper('shipping')->__('Invalid Tracking code type: %s', $type));
        }

        if (''===$code) {
            return $codes[$type];
        }

        if (!isset($codes[$type][$code])) {
            throw Mage::exception('Mage_Shipping', Mage::helper('shipping')->__('Invalid Tracking code for type %s: %s', $type, $code));
        }

        return $codes[$type][$code];
    }


}