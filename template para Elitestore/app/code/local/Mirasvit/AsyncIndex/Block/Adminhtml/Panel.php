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
 * ÐÐ»Ð¾Ðº Ð½Ð° ÑÑÑÐ°Ð½Ð¸ÑÐµ System / Index Management
 * ÐÑÐ²Ð¾Ð´Ð¸Ñ ÑÐµÐºÑÑÑÑ ÑÐ¾Ð¾Ð±ÑÐµÐ½Ð¸Ñ + Ð»Ð¾Ð³
 *
 * @category Mirasvit
 * @package  Mirasvit_AsyncIndex
 */
class Mirasvit_AsyncIndex_Block_Adminhtml_Panel extends Mage_Adminhtml_Block_Template
{
    public function _prepareLayout()
    {
    }
    /**
     * Return total saved time
     *
     * @return string
     */
    public function getSavedTime()
    {
        $seconds = intval(Mage::helper('asyncindex')->getVariable('time'));

        $time = new Zend_Date();
        $time->setTime('00:00:00');
        $time->addSecond($seconds);

        return $time->toString('HH').' hr '.$time->toString('mm').' min '.$time->toString('ss').' sec';
    }

    public function ucString($string)
    {
        $string = uc_words($string);
        $string = str_replace('_', ' ', $string);

        return $string;
    }

    public function isStreamVisible()
    {
        $display = 0;

        if (isset($_COOKIE['async_detailed_log'])) {
            $display = $_COOKIE['async_detailed_log'];
        }

        return $display;
    }
}