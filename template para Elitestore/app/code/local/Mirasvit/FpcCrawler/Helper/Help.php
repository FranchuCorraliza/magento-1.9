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



class Mirasvit_FpcCrawler_Helper_Help extends Mirasvit_MstCore_Helper_Help
{
    protected $_help = array(
        'system' => array(
            'crawler_enabled' => 'Enable this feature. I.e. extension will automatically visit all not cached pages defined at "Crawler URLs". If feature disabled, extension will work as before, but without automatically caching not cached pages.',
            'crawler_delete_crawler_urls' => 'If enabled crawler will delete all urls for current store from crawler table',
            'crawler_max_threads' => 'Determines the number of parallel requests during this process.',
            'crawler_thread_delay' => 'Delay between crawler requests.',
            'crawler_max_urls_per_run' => 'Maximum number of crawled URLs per one cron (or shell) run.',
            'crawler_schedule' => 'Specifies how often cron must run crawler. For example, 0 */3 * * * - every 3 hours.<xmp></xmp><b>Can be a cron expression only.</b> Example:<br/>*/5 * * * *<br/>*/15 * * * *',
            'crawler_sort_crawler_urls' => 'Specifies the crawl order.',
            'crawler_sort_by_page_type' => 'Specifies the order in which the crawler should add pages in a cache.',
            'crawler_sort_by_product_attribute' => 'Specifies the order in which the crawler should add pages in a cache. If you use "Sort by page type", will be applied after the first sorting. To apply the changes, please, flush all cache.',
            'crawler_crawl_url_limit' => 'Leave empty or set 0 to don\'t use. If enabled will crawl only a predetermined number of links.',
            'crawler_status' => '',

            'crawler_logged_enabled' => 'Enable this feature. I.e. extension will automatically visit all not cached pages defined at "Crawler URLs for logged in users". If feature disabled, extension will work as before, but without automatically caching not cached pages.',
            'crawler_logged_delete_crawler_urls' => 'If enabled crawler will delete all urls for current store from crawler for logged in users table',
            'crawler_logged_crawl_customer_group' => 'Customer groups which will be crawled by "Crawler for logged user"',
            'crawler_logged_max_threads' => 'Determines the number of parallel requests during this process.',
            'crawler_logged_max_urls_per_run' => 'Maximum number of crawled URLs per one cron (or shell) run.',
            'crawler_logged_schedule' => 'Specifies how often cron must run crawler. For example, 0 */3 * * * - every 3 hours.<xmp></xmp><b>Can be a cron expression only.</b> Example:<br/>*/5 * * * *<br/>*/15 * * * *',
            'crawler_logged_sort_crawler_urls' => 'Specifies the crawl order.',
            'crawler_logged_sort_by_page_type' => 'Specifies the order in which the crawler should add pages in a cache.',
            'crawler_logged_sort_by_product_attribute' => 'Specifies the order in which the crawler should add pages in a cache. If you use "Sort by page type", will be applied after the first sorting. To apply the changes, please, flush all cache.',
            'crawler_logged_crawl_url_limit' => 'Leave empty or set 0 to don\'t use. If enabled will crawl only a predetermined number of links.',

            'extended_crawler_settings_run_as_apache_user' => 'Crawler will run using apache user. Preferably use No.<xmp></xmp><b>Enable only if crawler don\'t work with current configuration.</b>',
            'extended_crawler_settings_is_url_filter_disabled' => 'For most part of stores recommended to disable. If enabled will add in Crawler Urls also urls which contains \'/catalog/product/view/\', \'/catalog/category/view/\', \'index.php\', \'//\'.',
            'extended_crawler_settings_directly_database_import' => 'If enabled add urls directly in database. Will not use fpc.log file.',
            'extended_crawler_settings_verify_peer' => 'Determines whether SSL certificates are validated for requests sent over a HTTPS connection.',
            'extended_crawler_settings_generate_crawler_urls' => 'Generate category and product urls for crawler. Urls will be added in "System->Full Page Cache->Crawler URLs" and "System->Full Page Cache->Crawler URLs for logged in users". <b>This action is not necessary and we suggest you to do it only one time after FPC installation.</b>',
            'extended_crawler_settings_htaccess_authentication' => 'If htaccess authentication enabled add username and password in this field<xmp></xmp><b>username:password</b>',
        ),
    );
}