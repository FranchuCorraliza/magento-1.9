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



class Mirasvit_Fpc_Block_Adminhtml_OptimalConfiguration extends Mage_Adminhtml_Block_Template
{
    protected $cacheTags = array('FPC_TEST_CACHE_STORAGE_PEERFORMANCE_TAG');
    protected $storagePerformanceSaveTime = null;
    protected $storagePerformanceCleanTime = null;

    public function isRedisInstalled() {
        $cacheType = Mage::getSingleton('fpc/cache')
                    ->getCacheInstance()
                    ->getFrontend()
                    ->getBackend();

        if (get_class($cacheType) == 'Cm_Cache_Backend_Redis'
            || get_class($cacheType) == 'Mage_Cache_Backend_Redis') {
                return true;
        }

        return false;
    }

    public function getCronStatusError() {
        return Mage::helper('fpc')->showCronStatusError(true);
    }

    public function getProductsCount() {
        $productsCount = 0;
        foreach (Mage::app()->getStores() as $store)
        {
            if ($store->getIsActive()) {
                $collection = Mage::getModel('catalog/product')->getCollection()
                                ->addAttributeToSelect('*')
                                ->addStoreFilter($store->getId())
                                ->addAttributeToFilter('status', 1)
                                ->addAttributeToFilter('visibility', array(Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG, Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH))
                                ;
                $productsCount += $collection->getSize();
            }
        }

        return $productsCount;
    }

    public function getCategoryCount() {
        $categoryCount = 0;
        foreach (Mage::app()->getStores() as $store)
        {
            if ($store->getIsActive()) {
                 $collection = Mage::getModel('catalog/category')->getCollection()
                                ->setStoreId($store->getId())
                                ->addFieldToFilter('is_active', 1)
                                ;
                $categoryCount += $collection->getSize();
            }
        }

        return $categoryCount;
    }

    public function getCacheLifetime() {
        $cacheLifetime = 259200;
        if ($this->isRedisInstalled()) {
            $cacheLifetime = 604800;
        } elseif ($this->getCronStatusError() !== true) {
            $cacheLifetime = 3600;
        }

        return $cacheLifetime;
    }

    public function getCacheLifetimeNote() {
        $note = '';
        if ($this->getCronStatusError() !== true) {
            $note = $this->getCronStatusError();
        }

        return $note;
    }

    public function getMaxCacheSize() {
        $maxCacheSize = 1024;
        if ($this->isRedisInstalled() || $this->getCronStatusError() !== true) {
            $maxCacheSize = 128;
        }

        return $maxCacheSize;
    }

    public function getMaxCacheSizeNote() {
        $note = '';
        if ($this->isRedisInstalled()) {
            $note = 'Redis installed. Redis keep all cache in RAM. FPC will not use the value. Redis will flush cache automatically if not enough RAM or using memory limit (if configured for Redis).';
        } elseif ($this->getCronStatusError() !== true) {
            $note = $this->getCronStatusError();
        }

        return $note;
    }

    //1000 pages = 90Mb (without compression) if use File system [1024Mb = 11377 pages]
    //1000 pages = 35Mb if use REDIS.
    public function getMaxNumberCacheFiles() {
        $maxNumberCacheFiles = 20000;
        if (!$this->isRedisInstalled()) {
            $maxNumberCacheFiles = ($this->getCategoryCount() * 20) + ($this->getProductsCount() * 2);
            if ($maxNumberCacheFiles < 20000) {
                $maxNumberCacheFiles = 20000;
            }
        }

        return $maxNumberCacheFiles;
    }

    public function getMaxNumberCacheFilesNote() {
        $note = '';
        if ($this->isRedisInstalled()) {
            $note = 'Redis installed. Redis keep all cache in RAM. FPC will not use the value. Redis will flush cache automatically if not enough RAM or using memory limit (if configured for Redis).';
        }

        return $note;
    }

    public function getGzcompressLevelNote() {
        $note = '';
        if ($this->isRedisInstalled()) {
            $note = 'Redis use own compression.';
        }

        return $note;
    }

    public function getIgnoredPages() {
        $ignoredPages = '';
        if ($this->getProductsCount() > 1000) {
            $ignoredPages = '/\?[^p][^=]*/';
        }

        return $ignoredPages;
    }

    public function getIgnoredPagesNote() {
        $note = '';
        if ($this->getIgnoredPages()) {
            $note = 'FPC will cache urls like: http://example.com/url, http://example.com/url?p=n FPC will ignore pages urls like http://example.com/url?param1=n&m2=n. This way FPC will add to cache more visited pages.';
        }

        return $note;
    }

    public function isCrawlerInstalled() {
        return Mage::helper('mstcore')->isModuleInstalled('Mirasvit_FpcCrawler');
    }

    public function getLimitCrawledUrlsPerRun($jobCode, $canDetect = false)
    {
        $crawledUrlCount = 0;
        $maxUrlsPerRun = ($jobCode == 'fpc_crawler') ? Mage::helper('fpccrawler')->getVariable('status') : Mage::helper('fpccrawler')->getVariable('status_logged');
        preg_match('/\s\d+\s/', $maxUrlsPerRun, $crawled);
        if (isset($crawled[0])) {
            $crawledUrlCount = $crawled[0];
        }
        $collection = Mage::getModel('cron/schedule')->getCollection()
            ->setOrder('executed_at', 'desc');
        if ($jobCode) {
            $collection->addFieldToFilter('job_code', $jobCode);
        }

        $collection->getSelect()->limit('1');
        $cron = $collection->getFirstItem();

        if ($cron->getFinishedAt() && $cron->getExecutedAt() && $crawledUrlCount) {
            $crawlerWorkTime = Mage::getModel('core/date')->timestamp(strtotime($cron->getFinishedAt())) - Mage::getModel('core/date')->timestamp(strtotime($cron->getExecutedAt()));
            if ($crawledUrlCount != 0
                && ($oneUrlCrawlTime = $crawlerWorkTime/$crawledUrlCount)) {
                $limitCrawledUrls = ceil(180/$oneUrlCrawlTime);
            }
        }

        if (!isset($limitCrawledUrls) && $canDetect) {
            return false;
        } elseif (!isset($limitCrawledUrls)) {
            $limitCrawledUrls = 10;
        }

        return $limitCrawledUrls;
    }

    public function getLimitCrawledUrlsPerRunNote($jobCode)
    {
        $note = '';
        $isEnabled = ($jobCode == 'fpc_crawler') ? Mage::getSingleton('fpccrawler/config')->isEnabled() : Mage::getSingleton('fpccrawler/config')->isEnabled(true);
        if ($this->getCronStatusError() !== true) {
            $note = $this->getCronStatusError();
        } elseif (!$isEnabled) {
            $note = 'Not enough data to detect "Limit of Crawled URLs per Run". Crawler disabled ( <a href="' . $this->getFpccrawlerConfigUrl() . '" target="_blank"><b>Full Page Cache Crawler<b/></a> )';
        } elseif (!$this->getLimitCrawledUrlsPerRun($jobCode, true)) {
            $note = 'Not enough data to detect "Limit of Crawled URLs per Run". Try check after after some time (to detect need one or more crawl run). Other reason can be all urls crawled (current data correct).';
        }

        return $note;
    }

    public function getFpccrawlerConfigUrl()
    {
        return Mage::helper("adminhtml")->getUrl('*/system_config/edit/section/fpccrawler');
    }

    public function getFpcConfigUrl()
    {
        return Mage::helper("adminhtml")->getUrl('*/system_config/edit/section/fpc');
    }

    protected function getCacheStoragePerformanceSaveTime()
    {
        if (!$this->storagePerformanceSaveTime) {
            $cacheId = 'FPC_TEST_CACHE_STORAGE_PEERFORMANCE_SAVE';
            $mktime = microtime(true);
            $storage = Mage::getModel('fpc/storage');
            $storage->setCacheId($cacheId);
            $storage->setCacheLifetime(86400);
            $storage->setResponse($this->getCacheStoragePerformanceText());
            $storage->setCacheTags($this->cacheTags);
            $storage->save();
            $result = microtime(true) - $mktime;
            $this->storagePerformanceSaveTime = round($result, 5);
        }

        return $this->storagePerformanceSaveTime;
    }

    protected function getCacheStoragePerformanceCleanTime()
    {
        if (!$this->storagePerformanceCleanTime) {
            $mktime = microtime(true);
            Mirasvit_Fpc_Model_Cache::getCacheInstance()->clean($this->cacheTags);
            $result = microtime(true) - $mktime;
            $this->storagePerformanceCleanTime = round($result, 5);
        }

        return $this->storagePerformanceCleanTime;
    }

    protected function getAdditionalPerformanceInfo()
    {
        $fpcVersion = Mage::helper('fpc/version')->getCurrentFpcVersion();
        $info = '';
        if ($this->getProductsCount() < 2000 && !$this->isRedisInstalled()) {
            $info = '. To improve this valuse try switch store cache to  <a target="_blank" href="https://mirasvit.com/doc/fpc/' . $fpcVersion
            . '/cachesupport">database</a> or install <a target="_blank" href="https://mirasvit.com/doc/fpc/'
            . $fpcVersion . '/cachesupport">REDIS</a>.';
        } elseif ($this->getProductsCount() >= 2000 && !$this->isRedisInstalled()) {
            $info = '. To improve this value try install <a target="_blank" href="https://mirasvit.com/doc/fpc/' . $fpcVersion . '/cachesupport">REDIS</a>.';
        }

        return $info;
    }

    public function getCacheStoragePerformanceSaveInfo()
    {
        $result = $this->getCacheStoragePerformanceSaveTime();
        if ($result > Mirasvit_Fpc_Model_Config::ALLOWED_PEERFORMANCE_SAVE_TIME) {
            $result = 'Cache Storage Performance (Save): ' . round($result, 5);
            $result .= $this->getAdditionalPerformanceInfo();
        } else {
            $result = 'Cache Storage Performance (Save): ' . round($result, 5);
        }

        return $result;
    }

    public function getCacheStoragePerformanceCleanInfo()
    {
        $result = $this->getCacheStoragePerformanceCleanTime();
        if ($result > Mirasvit_Fpc_Model_Config::ALLOWED_PEERFORMANCE_CLEAN_TIME) {
            $result = 'Cache Storage Performance (Clean): ' . round($result, 5);
            $result .= $this->getAdditionalPerformanceInfo();
        } else {
            $result = 'Cache Storage Performance (Clean): ' . round($result, 5);
        }

        return $result;
    }

    public function getCacheStoragePerformanceClass($save = false)
    {
        if ($save) {
            $result = $this->getCacheStoragePerformanceSaveTime();
            if ($result > Mirasvit_Fpc_Model_Config::ALLOWED_PEERFORMANCE_SAVE_TIME) {
                $class = 'notice-msg';
            }
        } else {
            $result = $this->getCacheStoragePerformanceCleanTime();
            if ($result > Mirasvit_Fpc_Model_Config::ALLOWED_PEERFORMANCE_CLEAN_TIME) {
                $class = 'notice-msg';
            }
        }

        if (!isset($class)) {
            $class = 'success-msg';
        }

        return $class;
    }

    protected function getCacheStoragePerformanceText()
    {
        $text = 'Full Page Cache extension - is the best solution for Magento store that significantly speeds up page load time,
        reduces the load on the server, improves website ranking and remarkably increases sales conversion.
        An extension that directly affects sales conversion and successfully generate the revenue. ';

        for ($i=1; $i <10; $i++) {
            $text .= $text;
        }

        return $text;
    }
}
