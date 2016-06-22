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
 * ÐÐµÑÐµÐ¾Ð¿ÑÐµÐ´ÐµÐ»ÑÐµÐ¼ Ð´ÐµÑÐ¾Ð»ÑÐ½ÑÑ Ð¼Ð¾Ð´ÐµÐ»Ñ, Ð´Ð»Ñ ÑÐ¾Ð³Ð¾ ÑÑÐ¾ Ð±Ñ Ð¿ÑÐ¸Ð¼ÐµÐ½ÑÑÑ Catalog Rule ÑÐ¾Ð»ÑÐºÐ¾ ÐµÑÐ»Ð¸ ÐµÑÑÑ Ð¿Ð°ÑÐ°Ð¼ÐµÑÑ force
 * 
 * @category Mirasvit
 * @package  Mirasvit_AsyncIndex
 */
class Mirasvit_AsyncIndex_Model_CatalogRule_Rule extends Mage_CatalogRule_Model_Rule
{
    public function applyAllRulesToProduct($product, $force = false)
    {
        if ($force) {
            return parent::applyAllRulesToProduct($product);
        } else {
            return $this;
        }
    }
}
