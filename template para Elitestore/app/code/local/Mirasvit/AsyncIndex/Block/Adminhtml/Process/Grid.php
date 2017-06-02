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
 * ÐÐµÑÐµÐ¾Ð¿ÑÐµÐ´ÐµÑÐµÐ¼ Ð´ÐµÑÐ¾Ð»ÑÐ½ÑÐ¹ Ð³ÑÐ¸Ð´ Ð¸Ð½Ð´ÐµÐºÑÐ¾Ð², Ð´Ð»Ñ Ð´Ð¾Ð±Ð°Ð²Ð»ÐµÐ½Ð¸Ñ Ð½Ð¾Ð²Ð¾Ð³Ð¾ ÑÑÐ°ÑÑÑÐ° STATUS_WAIT
 *
 * @category Mirasvit
 * @package  Mirasvit_AsyncIndex
 */
class Mirasvit_AsyncIndex_Block_Adminhtml_Process_Grid extends Mage_Index_Block_Adminhtml_Process_Grid
{
    public function decorateStatus($value, $row, $column, $isExport)
    {
        $class = '';
        switch ($row->getStatus()) {
            case Mage_Index_Model_Process::STATUS_PENDING :
                $class = 'grid-severity-notice';
                break;
            case Mage_Index_Model_Process::STATUS_RUNNING :
                $class = 'grid-severity-major';
                break;
            case Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX :
                $class = 'grid-severity-critical';
                break;
            case Mirasvit_AsyncIndex_Model_Process::STATUS_WAIT :
            default:
                $class = 'grid-severity-major';
                break;
        }
        return '<span class="'.$class.'"><span>'.$value.'</span></span>';
    }
}