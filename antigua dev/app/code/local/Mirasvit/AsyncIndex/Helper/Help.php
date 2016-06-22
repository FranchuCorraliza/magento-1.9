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
 * @package   Fast Asynchronous Re-indexing
 * @version   1.1.6
 * @build     285
 * @copyright Copyright (C) 2014 Mirasvit (http://mirasvit.com/)
 */


/**
 * @category Mirasvit
 * @package  Mirasvit_AsyncIndex
 */
class Mirasvit_AsyncIndex_Helper_Help extends Mirasvit_MstCore_Helper_Help
{
    protected $_help = array(
        'system' => array(
            'general_full_reindex'            => 'If option enabled, full reindex will be processed in background by cron.',
            'general_change_reindex'          => 'If option enabled, reindex of changed items will be processed in background by cron.',
            'general_validate_product_index'  => 'If option enabled, extension will validate product\'s index. If index for some product is incorrect, extension will add this product to the reindexing queue.
                Usually this feature should be enabled if you import products from extenal system directly to the database (e.g. you use Magmi).
                The option "Use Flat Catalog Product" must be enabled for correct work of this feature.',
            'general_validate_category_index' => 'If option enabled, extension will validate categories index. If index for some category is incorrect, extension will add this category to the reindexing queue.
                Usually this feature should be enabled if you import categories from extenal system directly to the database (e.g. you use Magmi).
                The option "Use Flat Catalog Category" must be enabled for correct work of this feature.',
            'general_queue_batch_size'        => 'Set how many products/categories can be added to reindex queue (if product or category index incorrect)
                This variable depends on your server. Recomended value 100 for slower server and < 1000 for fast server.
            ',
            'general_cronjob'                 => 'If option enabled, extension will run reindexing of queue using default magento cronjob (cron.php).
                Also, you can disable this option and create custom cronjob which should run the file shell/asyncindex.php.
                ',
            'general_ignored_index'           => 'List of ignored indexes. Selected indexes will not be processed (reindexed) by our extension. They can use default magento reindexing mechanism.',
        
            'processing_mode'                 => '',
        ),
    );
}