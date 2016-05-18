<?php
/**
 * Mageplace Survey
 *
 * @category	Belitsoft
 * @package		Belitsoft_Survey
 * @copyright	Copyright (c) 2012 Mageplace. (http://www.mageplace.com)
 * @license		http://www.mageplace.com/disclaimer.html
 */

class Belitsoft_Survey_Model_Feed extends  Mage_Core_Model_Abstract
{
	const XML_USE_HTTPS_PATH	= 'survey/feed/use_https';
	const XML_FEED_URL_PATH		= 'survey/feed/url';
	const XML_FREQUENCY_PATH	= 'survey/feed/check_frequency';
	const XML_FREQUENCY_ENABLE	= 'survey/feed/enabled';
	const XML_LAST_UPDATE_PATH	= 'survey/feed/last_update';

	
	public static function check()
	{
		if(!Mage::getStoreConfig(self::XML_FREQUENCY_ENABLE)) {
			return;
		}
		
		return Mage::getModel('belitsoft_survey/feed')->checkUpdate();
	}
	
	public function getFrequency()
	{
		return Mage::getStoreConfig(self::XML_FREQUENCY_PATH) * 3600;
	}

	public function getLastUpdate()
	{
		return Mage::app()->loadCache('survey_notifications_lastcheck');
	}

	public function setLastUpdate()
	{
		Mage::app()->saveCache(time(), 'survey_notifications_lastcheck');

		return $this;
	}
	
	public function getFeedData()
	{
		$url = $this->getFeedUrl();
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url); 
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);  
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);  
		curl_setopt($ch, CURLOPT_TIMEOUT, 2); 
		$data = curl_exec($ch); 
		curl_close($ch);  
		
		if ($data === false) {
			return false;
		}
		
		try {
			$xml  = new SimpleXMLElement($data);
		} catch (Exception $e) {
			return false;
		}
	  
		return $xml;
	}	
	
	public function getDate($rssDate)
	{
		return gmdate('Y-m-d H:i:s', strtotime($rssDate));
	}
   
	public function getFeedUrl()
	{
		if (is_null($this->_feedUrl)) {
			$this->_feedUrl = (Mage::getStoreConfigFlag(self::XML_USE_HTTPS_PATH) ? 'https://' : 'http://')
				. Mage::getStoreConfig(self::XML_FEED_URL_PATH);
		}
		
		return $this->_feedUrl;
	}

	public function checkUpdate()
	{
		
		if (($this->getFrequency() + $this->getLastUpdate()) > time()) {
			return $this;
		}
		
		$feedData = array();

		$feedXml = $this->getFeedData();

		if ($feedXml && $feedXml->channel && $feedXml->channel->item) {
			foreach ($feedXml->channel->item as $item) {
				$feedData[] = array(
					'severity'		=> (int)$item->severity ? (int)$item->severity : 3,
					'date_added'	=> $this->getDate((string)$item->pubDate),
					'title'			=> (string)$item->title,
					'description'	=> (string)$item->description,
					'url'			=> (string)$item->link,
				);
			}

			if ($feedData) {
				Mage::getModel('adminnotification/inbox')->parse(array_reverse($feedData));
			}
		}

		$this->setLastUpdate();

		return $this;
	}

}
