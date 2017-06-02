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



class Mirasvit_Fpc_Helper_Help extends Mirasvit_MstCore_Helper_Help
{
    protected $_help = array(
        'system' => array(
            'general_enabled' => 'Enables full page cache. You can enable/disable full page cache for each store view.',
            'general_lifetime' => 'Cache lifetime (in seconds). Determines the time after which the page cache will be invalid. A new page cache will be created the next time a visitor visits the page.',
            'general_flush_cache_schedule' => 'Specifies how often cron must clear (flush) cache. Leave empty for disable this feature.<xmp></xmp> <b>Can be a cron expression only.</b> Example:<br/>0 1 * * *<br/>0 0 */3 * *',
            'general_max_cache_size' => 'Maximum full page cache size in megabytes. If the limit is reached, extension will clear cache. If REDIS installed FPC will not use the limit (REDIS will flush cache automatically if not enough RAM).',
            'general_max_cache_number' => 'Maximum number of cache files. If the limit is reached, extension will clear cache. If REDIS installed FPC will not use the limit (REDIS will flush cache automatically if not enough RAM).',
            'general_gzcompress_level'     => 'Compress the cache. Use only for filecache. Flush Fpc cache after changing.',
            'general_cache_tags_level'     => 'In most situation recommended to use Minimal set of tags. Default - refresh cache if visible product changed. Minimal set of tags - create minimal set of tags and use observer to flush cache. Don\'t use tags - don\'t create product and category tags. Flush Fpc cache after changing.',

            'cache_rules_max_depth' => 'Determines the number of layered navigation filters, or parameters, that can be applied in order for a page to be cached.',
            'cache_rules_cacheable_actions' => 'List of cacheable actions.',
            'cache_rules_allowed_pages' => 'List of allowed pages (regular expression). In cache will be only allowed pages, other pages will be ignored by FPC. <xmp></xmp><b>Can be a regular expression only.</b> Example:<br/>/books\/a-tale-of-two-cities.html/<br/>/books/',
            'cache_rules_ignored_pages' => 'List of not allowed for caching pages (regular expression).<xmp></xmp><b>Can be a regular expression only.</b> Example:<br/>/books\/a-tale-of-two-cities.html/<br/>/books/',
            'cache_rules_user_agent_segmentation' => 'Determines the cache by user agent  (regular expression)',
            'cache_rules_ignored_url_params' => 'Ignore GET parameters when creating a cache',
            'cache_rules_mobile_detect' => 'Determines the cache by device type',


            'debug_info' => 'Show green block with FPC info in frontend.',
            'debug_flush_cache_button' => 'Add button in green block in frontend. "Flush current page cache" - flush cache only for current page. "Flush depending tags cache" - flush cache by tags for current page and depending pages.',
            'debug_hints' => 'Show debug hints.',
            'debug_log' => 'Create log file ( fpc_debug.log ) with FPC actions.',
            'debug_allowed_ip' => 'Comma separated IP addresses',
        ),
    );
}
