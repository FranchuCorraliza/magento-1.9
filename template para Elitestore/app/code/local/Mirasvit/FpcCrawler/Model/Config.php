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



class Mirasvit_FpcCrawler_Model_Config
{
    const USER_AGENT_BEGIN_LABEL    = 'mir_cg_id_begin_';
    const USER_AGENT_END_LABEL      = '_mir_cg_id_end';
    const STORE_ID_BEGIN_LABEL      = 'mir_store_id_begin_';
    const STORE_ID_END_LABEL        = '_mir_store_id_end';
    const CURRENCY_BEGIN_LABEL      = 'mir_currency_begin_';
    const CURRENCY_END_LABEL        = '_mir_currency_end';

    const MOBILE_GROUP              = 'mobileDetectGroup';
    const COMPUTER_GROUP            = 'computerGroup';

    const CRAWL_URL_DEFAULT_LIMIT   = 1000000000000000;

    public function isEnabled($logged = null, $storeId = null)
    {
        $path = $logged ? 'fpccrawler/crawler_logged/enabled' : 'fpccrawler/crawler/enabled';

        return Mage::getStoreConfig($path, $storeId);
    }

    /**
     * @param bool $logged
     * @param int $storeId
     * @return int
     */
    public function isDeleteCrawlerUrls($logged = null, $storeId = null)
    {
        $path = $logged ? 'fpccrawler/crawler_logged/delete_crawler_urls' : 'fpccrawler/crawler/delete_crawler_urls';

        return Mage::getStoreConfig($path, $storeId);
    }

    public function getCrawlerMaxThreads($logged = null, $storeId = null)
    {
        $path = $logged ? 'fpccrawler/crawler_logged/max_threads' : 'fpccrawler/crawler/max_threads';
        $threads = Mage::getStoreConfig($path, $storeId);

        if (!$threads) {
            $threads = 1;
        }

        return $threads;
    }

    public function getCrawlerMaxUrlsPerRun($logged = null, $storeId = null)
    {
        $path = $logged ? 'fpccrawler/crawler_logged/max_urls_per_run' : 'fpccrawler/crawler/max_urls_per_run';
        $urls = Mage::getStoreConfig($path, $storeId);

        if (!$urls) {
            $urls = 10;
        }

        return $urls;
    }

    public function getCrawlerSchedule($logged = null, $storeId = null)
    {
        $path = $logged ? 'fpccrawler/crawler_logged/schedule' : 'fpccrawler/crawler/schedule';
        return Mage::getStoreConfig($path, $storeId);
    }

    public function getSortCrawlerUrls($logged = null)
    {
        $path = $logged ? 'fpccrawler/crawler_logged/sort_crawler_urls' : 'fpccrawler/crawler/sort_crawler_urls';
        return Mage::getStoreConfig($path);
    }

    public function getSortByPageType($logged = null)
    {
        $path = $logged ? 'fpccrawler/crawler_logged/sort_by_page_type' : 'fpccrawler/crawler/sort_by_page_type';
        $options = Mage::getStoreConfig($path);
        $options = unserialize($options);
        $result = array();
        if (is_array($options)) {
            foreach ($options as $value) {
                $result[] = new Varien_Object($value);
            }
        }

        return $result;
    }

    public function getSortByProductAttribute($logged = null)
    {
        $path = $logged ? 'fpccrawler/crawler_logged/sort_by_product_attribute' : 'fpccrawler/crawler/sort_by_product_attribute';
        $options = Mage::getStoreConfig($path);
        $options = unserialize($options);
        $result = array();
        if (is_array($options)) {
            $counter = 0;
            foreach ($options as $value) {
                $counter++;
                $value['counter'] = $counter;
                $result[] = new Varien_Object($value);
            }
        }

        return $result;
    }

    public function getCrawlUrlLimit($logged = null, $storeId = null)
    {
        $path = $logged ? 'fpccrawler/crawler_logged/crawl_url_limit' : 'fpccrawler/crawler/crawl_url_limit';
        $limit = (int) Mage::getStoreConfig($path, $storeId);

        if (!$limit) {
            $limit = self::CRAWL_URL_DEFAULT_LIMIT;
        }

        return $limit;
    }

    public function getCrawlCustomerGroupIds()
    {
        if ($crawlCustomerGroupIds = Mage::getStoreConfig('fpccrawler/crawler_logged/crawl_customer_group')) {
            $crawlCustomerGroupIds = explode(',', $crawlCustomerGroupIds);
        }

        return $crawlCustomerGroupIds;
    }

    public function isAllowedGroup($customerGroupId)
    {
        if (($crawlCustomerGroupIds = $this->getCrawlCustomerGroupIds()) && in_array($customerGroupId, $crawlCustomerGroupIds)) {
            return true;
        }

        return false;
    }

    //Extended crawler setting (for both crawlers)
    public function isRunAsApacheUser()
    {
        return Mage::getStoreConfig('fpccrawler/extended_crawler_settings/run_as_apache_user');
    }

    public function isUrlFilterDisabled()
    {
        return Mage::getStoreConfig('fpccrawler/extended_crawler_settings/is_url_filter_disabled');
    }

    public function isImportDirectlyDatabase()
    {
        return Mage::getStoreConfig('fpccrawler/extended_crawler_settings/directly_database_import');
    }

    /**
     * Retrieve whether SSL certificates are validated for requests sent over a HTTPS connection.
     *
     * @return integer
     */
    public function getVerifyPeer()
    {
        $verifyPeer = (Mage::getStoreConfig('fpccrawler/extended_crawler_settings/verify_peer')) ? 0 : 1;

        return $verifyPeer;
    }

    public function getHtaccessAuth()
    {
        return trim(Mage::getStoreConfig('fpccrawler/extended_crawler_settings/htaccess_authentication'));
    }
}