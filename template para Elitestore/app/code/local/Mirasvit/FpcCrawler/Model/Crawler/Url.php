<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Full Page Cache
 * @version   1.0.32
 * @build     662
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_FpcCrawler_Model_Crawler_Url extends Mage_Core_Model_Abstract
{
    /**
     * Htaccess authentication
     *  @var null|int
     */
    protected static $_htaccessAuth = null;

    /**
     * Verify peer
     *  @var null|int
     */
    protected static $_verifyPeer = null;

    /**
     * @var array
     */
    protected static $crawlerDeleteUrlStoreIds = array();

    /**
     * @var array
     */
    protected static $storeIds = array();

    protected function _construct()
    {
        $this->_init('fpccrawler/crawler_url');
        $this->setConfigData();
    }

    protected function setConfigData()
    {
        if (self::$_htaccessAuth === null || self::$_verifyPeer === null) {
            $config = Mage::getSingleton('fpccrawler/config');
            self::$_htaccessAuth = $config->getHtaccessAuth();
            self::$_verifyPeer = $config->getVerifyPeer();
        }

        return true;
    }

    public function saveUrl($line, $rate = 1)
    {
        if (count($line) < 9) {
            return $this;
        }

        $url = $line[2];
        $cacheId = preg_replace('/\s+/', ' ', trim($line[3]));
        $storeId = trim($line[7]);
        $currency = trim($line[8]);
        $mobileGroup = trim($line[9]);

        $collection = $this->getCollection();
        $collection->getSelect()->where('url = ?', $url)
                                ->where('store_id = ?', $storeId)
                                ->where('currency = ?', $currency)
                                ->where('mobile_group = ?', $mobileGroup)
                                ;
        $model = $collection->getFirstItem();

        try {
            if (trim($cacheId) != '') {
                $model->setCacheId($cacheId)
                        ->setUrl($url)
                        ->setRate(intval($model->getRate()) +  $rate)
                        ->setSortByPageType(trim($line[4]))
                        ->setStoreId($storeId)
                        ->setCurrency($currency)
                        ->setMobileGroup($mobileGroup)
                        ;
                if (isset($line[5])) {
                    $model->setSortByProductAttribute(trim($line[5]))
                        ->save();
                } else {
                    $model->save();
                }
            } elseif ($model->getId()) {
                $model->setRate(intval($model->getRate()) +  $rate)
                    ->save();
            }
        } catch (Exception $e) {
        }

        return $this;
    }

    public function isCacheExist()
    {
        $cache = Mirasvit_Fpc_Model_Cache::getCacheInstance();
        $cacheId = $this->getCacheId();

        if (is_string($cacheId) && $cache->load($cacheId)) {
            return true;
        }

        return false;
    }

    public function clearCache()
    {
        $cache = Mirasvit_Fpc_Model_Cache::getCacheInstance();
        $cache->remove($this->getCacheId());

        return $this;
    }

    public function warmCache()
    {
        $storeCrawlerConfig = $this->_getStoreCrawlerConfig($this->getStoreId());
        if ($storeCrawlerConfig && $storeCrawlerConfig->getIsDeleteCrawlerUrls()) {
            $this->delete();
            return false;
        } elseif($storeCrawlerConfig && !$storeCrawlerConfig->getIsCrawlerEnabled()) {
            return false;
        }

        $url = $this->getUrl();
        $userAgent = Mage::helper('fpccrawler')->getUserAgent(null, $this->getStoreId(), $this->getCurrency());
        $content = '';
        if (function_exists('curl_multi_init')) {
            $adapter = new Varien_Http_Adapter_Curl();
            $options = array(
                CURLOPT_USERAGENT => $userAgent,
                CURLOPT_HEADER => true,
                CURLOPT_SSL_VERIFYPEER => self::$_verifyPeer,
                CURLOPT_USERPWD => (self::$_htaccessAuth) ? self::$_htaccessAuth : null,
            );

            $content = $adapter->multiRequest(array($url), $options);
            $content = $content[0];
        } else {
            ini_set('user_agent', $userAgent);
            $content = implode(PHP_EOL, get_headers($url));
        }

        if (strpos($content, '404 Not Found') !== false
            || strpos($content, '301 Moved Permanently') !== false
            || strpos($content, 'HTTP/1.1 302 Found') !== false) {
                $this->delete();
        }

        preg_match('/Fpc-Cache-Id: ('.Mirasvit_Fpc_Model_Config::REQUEST_ID_PREFIX.'[a-z0-9]{32})/', $content, $matches);
        if (count($matches) == 2) {
            $cacheId = $matches[1];
            if ($this->getCacheId() != $cacheId) {
                $this->setCacheId($cacheId)
                    ->save();
            }
        } else {
            $this->delete();
        }

        return $this;
    }

    /**
     * Check if "Delete urls from crawler table if crawler disabled" enabled and if crawler enabled
     * @param int $storeId
     * @return object
     */
    protected function _getStoreCrawlerConfig($storeId) {
        if (self::$crawlerDeleteUrlStoreIds && array_key_exists($storeId, self::$crawlerDeleteUrlStoreIds)) {
            return self::$crawlerDeleteUrlStoreIds[$storeId];
        }

        if(!self::$storeIds) {
            self::$storeIds = Mage::getModel('core/store')->getCollection()->getAllIds();
        }

        if (!in_array($storeId, self::$storeIds)) {
            self::$crawlerDeleteUrlStoreIds[$storeId] = new Varien_Object(array(
                'is_crawler_enabled' => true,
                'is_delete_crawler_urls'   => true,
            ));

            return self::$crawlerDeleteUrlStoreIds[$storeId];
        }

        $config = Mage::getSingleton('fpccrawler/config');
        $isCrawlerEnabled = $config->isEnabled(false, $storeId);
        self::$crawlerDeleteUrlStoreIds[$storeId] = new Varien_Object(array(
            'is_crawler_enabled' => $isCrawlerEnabled,
            'is_delete_crawler_urls'   => false,
        ));

        if (!$isCrawlerEnabled
            && $config->isDeleteCrawlerUrls(false, $storeId)) {
                self::$crawlerDeleteUrlStoreIds[$storeId]->setIsDeleteCrawlerUrls(true);
        }

        return  self::$crawlerDeleteUrlStoreIds[$storeId];
    }

}
