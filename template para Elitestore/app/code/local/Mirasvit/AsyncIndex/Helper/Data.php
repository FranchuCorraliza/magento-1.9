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
class Mirasvit_AsyncIndex_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Ð¡Ð¾ÑÑÐ°Ð½ÑÐµÐ¼ Ð·Ð½Ð°ÑÐµÐ½Ð¸Ðµ Ð² ÐÐ
     * 
     * @param string $key   Ð¸Ð´ÐµÐ½ÑÐ¸ÑÐ¸ÐºÐ°ÑÐ¾Ñ
     * @param string $value Ð·Ð½Ð°ÑÐµÐ½Ð¸Ðµ
     *
     * @return object
     */
    public function setVariable($key, $value)
    {
        $variable = Mage::getModel('core/variable');
        $variable = $variable->loadByCode('asyncindex_'.$key);

        $variable->setPlainValue($value)
            ->setHtmlValue(Mage::getSingleton('core/date')->gmtTimestamp())
            ->setName($key)
            ->setCode('asyncindex_'.$key)
            ->save();

        return $variable;
    }

    /**
     * ÐÐ¾Ð»ÑÑÐ°ÐµÐ¼ ÑÐ¾ÑÑÐ°Ð½ÐµÐ½Ð½Ð¾ Ð·Ð½Ð°ÑÐµÐ½Ð¸Ðµ Ð¸Ð· ÐÐ
     *
     * @param  string $key Ð¸Ð´ÐµÐ½ÑÐ¸ÑÐ¸ÐºÐ°ÑÐ¾Ñ
     *
     * @return string
     */
    public function getVariable($key)
    {
        $variable = Mage::getModel('core/variable')->loadByCode('asyncindex_'.$key);

        return $variable->getPlainValue();
    }

    /**
     * ÐÐ¾Ð»ÑÑÐ°ÐµÐ¼ timestamp Ð¿Ð¾ÑÐ»ÐµÐ´Ð½ÐµÐ³Ð¾ Ð¸Ð·Ð¼ÐµÐ½ÐµÐ½Ð¸Ñ Ð·Ð½Ð°ÑÐµÐ½Ð¸Ñ Ð² ÐÐ
     *
     * @param  string $key Ð¸Ð´ÐµÐ½ÑÐ¸ÑÐ¸ÐºÐ°ÑÐ¾Ñ Ð·Ð½Ð°ÑÐµÐ½Ð¸Ñ
     *
     * @return integer
     */
    public function getVariableTimestamp($key)
    {
        $variable = Mage::getModel('core/variable')->loadByCode('asyncindex_'.$key);

        return $variable->getHtmlValue();
    }

    /**
     * Ð¡Ð¾ÑÑÐ°Ð½ÑÐµÐ¼ ÑÐ¾Ð¾Ð±ÑÐµÐ½Ð¸Ðµ Ð² ÐÐ, status = start
     *
     * @param  string  $text  ÑÐ¾Ð¾Ð±ÑÐµÐ½Ð¸Ðµ
     * @param  integer $level ÑÑÐ¾Ð²ÐµÐ½Ñ ÑÐ¾Ð¾Ð±ÑÐµÐ½Ð¸Ðµ
     */
    public function start($text, $parentUid = null)
    {
        $content = array(
            'status'     => 'start',
            'text'       => $text,
            'created_at' => microtime(true),
            'parent_id'  => $parentUid
        );

        $obj = Mage::helper('mstcore/logger')->log($this, $text, serialize($content), 0, false, true);

        return $obj->getId();
    }

    /**
     * Ð¡Ð¾ÑÑÐ°Ð½ÑÐµÐ¼ ÑÐ¾Ð¾Ð±ÑÐµÐ½Ð¸Ðµ Ð² ÐÐ, status = finish
     *
     * @param  string  $text  ÑÐ¾Ð¾Ð±ÑÐµÐ½Ð¸Ðµ
     * @param  integer $level ÑÑÐ¾Ð²ÐµÐ½Ñ ÑÐ¾Ð¾Ð±ÑÐµÐ½Ð¸Ðµ
     */
    public function finish($uid)
    {
        $logger = Mage::getModel('mstcore/logger')->load($uid);
        $content =  @unserialize($logger->getContent());
        $content['status'] = 'finish';
        $content['finished_at'] = microtime(true);
        $logger->setContent(serialize($content))
            ->save();

        return $logger->getId();
    }

    public function error($uid, $message)
    {
        $uid = end(explode('/', $uid));
        $logger = Mage::getModel('mstcore/logger')->load($uid);
        
        $content =  @unserialize($logger->getContent());

        // we can't serialize some type of objects, that's why we log only error message
        if (is_object($message) && method_exists($message, '_toString')) {
            $message = $message->_toString();
        }

        $content['status'] = 'error';
        $content['message'] = $message;
        $content['finished_at'] = microtime(true);
        $logger->setContent(serialize($content))
            ->save();

        return $logger->getId();
    }

    public function getEventDescription($event)
    {
        $str = '';
        if (is_object($event)) {
            $entity = uc_words($event->getEntity());
            $entity = str_replace('_', ' ', $entity);
            $str .= $entity;

            $additional = array();
            
            if ($event->getEntityPk()) {
                $additional[] = 'ID: '.$event->getEntityPk();
            }
            if ($event->getType()) {
                $type = uc_words($event->getType());
                $type = str_replace('_', ' ', $type);
                $additional[] = 'Action: '.$type;
            }

            if (count($additional)) {
                $str .= ' ('.implode(' / ', $additional).')';
            }

        } else {
            $str = $event;
        }

        return $str;
    }


    /**
     * ÐÐ¾Ð·Ð²ÑÐ°ÑÐ°ÐµÑ Ð²ÑÐµÐ¼Ñ Ð¿ÑÐ¾ÑÐµÐ´ÑÐµÐµ Ñ Ð¼Ð¾Ð¼ÐµÐ½ÑÐ° $time
     * ÑÐ¾ÑÐ¼Ð°Ð¼ x years x months x days x hours x min x sec
     * 
     * @param  integer $time timestamp Ñ ÐºÐ°ÐºÐ¾Ð³Ð¾ Ð¼Ð¾Ð¼ÐµÐ½ÑÐ°
     * 
     * @return string
     */
    public function timeSince($time)
    {
        if ($time > 30 * 24 * 60 * 60) {
            return '';
        }

        $time = abs($time);
        $print = '';
        $chunks = array(
            array(60 * 60 * 24 * 365 , 'year'),
            array(60 * 60 * 24 * 30 , 'month'),
            array(60 * 60 * 24 , 'day'),
            array(60 * 60 , 'hour'),
            array(60 , 'min'),
            array(1 , 'sec')
        );

        for ($i = 0; $i < count($chunks); $i++) {
            $seconds = $chunks[$i][0];
            $name    = $chunks[$i][1];

            if (($count = floor($time / $seconds)) != 0) {
                $print .= $count.' ';
                $print .= $name;
                $print .= ' ';

                $time -= $count * $seconds;
            }
        }

        if ($print == '') {
            $print = '0 seconds';
        }

        return $print;
    }

    /**
     * ÑÐµÐºÑÑÐ¸Ð¹ ÑÑÐ°ÑÐ°ÑÑÑ Ð¼Ð¾Ð´ÑÐ»Ñ Ð Ð°Ð±Ð¾ÑÐ°ÐµÑ / ÐÐ¶Ð¸Ð´Ð°ÐµÑ
     * 
     * @return boolean
     */
    public function isProcessing()
    {
        $result = false;

        if (Mage::getModel('asyncindex/control')->isLocked()) {
            $result = true;
        }

        return $result;
    }

    public function getCronStatus()
    {
        if (!Mage::getStoreConfig('asyncindex/general/cronjob')) {
            return true;
        }

        $job = Mage::getModel('cron/schedule')->getCollection()
            ->addFieldToFilter('job_code', 'asyncindex')
            ->addFieldToFilter('status', 'success')
            ->setOrder('scheduled_at', 'desc')
            ->getFirstItem();
        
        if (!$job->getId()) {
            return false;
        }

        $jobTimestamp = strtotime($job->getExecutedAt());
        $timestamp    = Mage::getSingleton('core/date')->gmtTimestamp();

        if (abs($timestamp - $jobTimestamp) > 6 * 60 * 60) {
            return false;
        }

        return true;
    }

    public function getCronExpression()
    {
        $phpBin = $this->getPhpBin();
        $root   = Mage::getBaseDir();
        $var    = Mage::getBaseDir('var');

        $line = '* * * * * date >> '.$var.DS.'log'.DS.'cron.log;'
            .$phpBin.' -f '.$root.DS.'cron.php >> '.$var.DS.'log'.DS.'cron.log;';

        return $line;
    }

    public function getPhpBin()
    {
        $phpBin = 'php';

        if (PHP_BINDIR) {
            $phpBin = PHP_BINDIR.DS.'php';
        }

        return $phpBin;
    }
}
