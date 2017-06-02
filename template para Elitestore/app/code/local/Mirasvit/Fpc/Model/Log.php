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



class Mirasvit_Fpc_Model_Log extends Mage_Core_Model_Abstract
{
    const LOG_FILE = 'fpc.log';
    const LOGGED_LOG_FILE = 'fpclogged.log';
    const USER_AGENT = 'FpcCrawler';
    const LOGGED_USER_AGENT = 'FpcCrawlerlogged';

    protected $isLogged = null;
    protected $eventName = 'fpc_import_filelog';
    protected $logRenamed = 'fpc_.log';
    protected $logFile = self::LOG_FILE;
    protected $useLogFile = true; // false - use database
    protected $isFpcCrawlerInstalled = null;
    protected $isFpcCrawlerEnabled = null;

    protected function _construct()
    {
        $this->_init('fpc/log');
        $this->_initLogVariables();
        $this->isFpcCrawlerInstalled = Mage::helper('mstcore')->isModuleInstalled('Mirasvit_FpcCrawler');
        $this->useLogFile = $this->_isLogFileUsed();
        $this->isFpcCrawlerEnabled = $this->_isFpcCrawlerEnabled();
    }

    protected function _isLogFileUsed()
    {
        if ($this->isFpcCrawlerInstalled && $this->getCrawlerConfig()->isImportDirectlyDatabase()) {
            return false;
        }

        return true;
    }

    /**
     * Check if crawler enabled for current store
     *
     * @return bool
     */
    protected function _isFpcCrawlerEnabled()
    {
        if ($this->isFpcCrawlerInstalled && $this->getCrawlerConfig()->isEnabled($this->isLogged, Mage::app()->getStore()->getStoreId())) {
            return true;
        }

        return false;
    }

    protected function _initLogVariables($logged = null)
    {
        $jobCode = false;
        if (is_object($logged)) {
            $jobCode = $logged->getJobCode();
        }

        if (Mage::getSingleton('customer/session')->isLoggedIn()
            || ($jobCode == 'fpc_log_import_logged')
            || ($logged && !is_object($logged))) {
                $this->isLogged = true;
                $this->eventName = 'fpc_import_logged_filelog';
                $this->logFile = self::LOGGED_LOG_FILE;
                $this->logRenamed = 'fpclogged_.log';
        } else {
            $this->isLogged = false;
            $this->eventName = 'fpc_import_filelog';
            $this->logFile = self::LOG_FILE;
            $this->logRenamed = 'fpc_.log';
        }
    }

    protected function _getActionType($normalizedUrl)
    {
        $request = Mage::app()->getRequest();
        $type = $request->getModuleName().'/'.$request->getControllerName().'_'.$request->getActionName();
        if (strpos($normalizedUrl, '?') !== false) {
            $type .= '+';
        }

        return $type;
    }

    protected function _getOrder($type)
    {
        $order = 1000;
        $product = Mage::registry('current_product');
        if (!$product || !$this->isFpcCrawlerInstalled
            || ($type != 'catalog/product_view' && $type != 'catalog/product_view+')) {
                return $order;
        }

        $attributeValue = array();
        $productAttribute = $this->_getProductAttribute();
        foreach($productAttribute as $record) {
            if (!$product->offsetExists($record->getAttributeOptionCode())) {
                continue;
            }
            if (trim($record->getAttributeValue()) == '') {
                return $record->getCounter();
            }

            $attribute = ($attributeText = $product->getAttributeText($record->getAttributeOptionCode())) ? $attributeText : $product->getData($record->getAttributeOptionCode());
            $attributeValue[$record->getAttributeOptionCode()] = $attribute;
            if (count($attributeValue) > 0) {
                $attributeArray = explode(",",$record->getAttributeValue());
                $attributeArray = array_map("trim",$attributeArray);
                if ((is_array($attributeValue[$record->getAttributeOptionCode()])
                    && array_intersect($attributeValue[$record->getAttributeOptionCode()], $attributeArray)) //multiselect
                    || (!is_array($attributeValue[$record->getAttributeOptionCode()])
                        && in_array($attributeValue[$record->getAttributeOptionCode()], $attributeArray))) {

                        $order = $record->getCounter();
                        break;
                }
            }
        }

        return $order;
    }

    protected function _getProductAttribute()
    {
        return $this->isLogged ? Mage::getSingleton('fpccrawler/config')->getSortByProductAttribute(true) : Mage::getSingleton('fpccrawler/config')->getSortByProductAttribute();
    }

    public function log($cacheId, $isHit = 1)
    {
        if (!$this->isFpcCrawlerInstalled || !$this->isFpcCrawlerEnabled || $this->isMobile()) {
            return true;
        }

        $normalizedUrl = Mage::helper('fpc')->getNormalizedUrl(true);
        if (!$this->_isLogUrl($normalizedUrl)) {
            return true;
        }
        $type = $this->_getActionType($normalizedUrl);
        $order = $this->_getOrder($type);
        $logData = array(
            $normalizedUrl,
            $cacheId,
            $type,
            $order,
            $this->_getCustomerGroup(),
            Mage::app()->getStore()->getStoreId(),
            Mage::app()->getStore()->getCurrentCurrencyCode(),
            Mirasvit_FpcCrawler_Model_Config::COMPUTER_GROUP,
        );

        if ((Mage::helper('core/http')->getHttpUserAgent() == self::USER_AGENT
            || Mage::helper('core/http')->getHttpUserAgent() == self::LOGGED_USER_AGENT)
            && ($product = Mage::registry('current_product'))
            && Mage::getSingleton('fpccrawler/config')->getSortCrawlerUrls() == 'custom_order'
            && count($this->_getProductAttribute()) > 0) {
                $line = array(
                            0, //not use
                            0, //not use
                        );

                $line = $this->_addLogData($line, $logData);

                Mage::dispatchEvent($this->eventName, array('line' => $line));


            return true;
        }

        $time = microtime(true) - $_SERVER['FPC_TIME'];

        $data = array(
                    $isHit,
                    round($time, 5),
                );

        $data = $this->_addLogData($data, $logData);

        if ($this->useLogFile) {
            Mage::log(implode('|', $data), null, $this->logFile, true);
        } else {
            $resource   = Mage::getSingleton('core/resource');
            $connection = $resource->getConnection('core_write');
            $tableName  = Mage::getSingleton('core/resource')->getTableName('fpc/log');
            $rows       = array();
            $line = implode('|', $data);
            $line = explode('|', $line);
            $rows[] = array(
                'response_time' => $line[1],
                'from_cache'    => $line[0],
                'created_at'    => date('Y-m-d H:i:s'),
            );

            if (!$this->isLogged) {
                $connection->insertArray($tableName, array('response_time', 'from_cache', 'created_at'),  $rows);
            }

            Mage::dispatchEvent($this->eventName, array('line' => $line));
        }

        return true;
    }

    protected function _addLogData($data, $logData) {
        $data = array_merge($data, $logData);

        return $data;
    }

    protected function _getCustomerGroup() {
        $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        if (!$customerGroupId) {
            $customerGroupId = 0;
        }

        return $customerGroupId;
    }

    protected function _isLogUrl($url) {
        if ($this->getCrawlerConfig()->isUrlFilterDisabled()) {
            return true;
        }
        $expressions = array(
                        '/catalog\/product\/view/',
                        '/catalog\/category\/view/',
                        '/index.php/',
                        '/\/\//'
                        );

        $url = str_replace(array('http://', 'https://'), '', $url);

        foreach ($expressions as $exp) {
            if (preg_match($exp, $url)) {
                return false;
            }
        }

        return true;
    }

    public function importFileLog($logged = null)
    {
        if (!$this->isFpcCrawlerInstalled) {
            return true;
        }

        if ($logged) {
            $this->_initLogVariables($logged);
        }
        $logPath = Mage::getBaseDir('var').DS.'log';
        $filePath = $logPath.DS.$this->logFile;

        if (!file_exists($filePath)) {
            return true;
        }

        @rename($filePath, $logPath.DS.$this->logRenamed);

        $filePath = $logPath.DS.$this->logRenamed;

        if (!file_exists($filePath)) {
            return true;
        }

        $handle = fopen($filePath, 'r');
        if ($handle) {
            $resource = Mage::getSingleton('core/resource');
            $connection = $resource->getConnection('core_write');
            $tableName = Mage::getSingleton('core/resource')->getTableName('fpc/log');
            $rows = array();
            $importLimit = 1000; //import limit from one file
            $totalRowsNumber = 0;
            while (($line = fgets($handle)) !== false) {
                $line = explode('):', $line);
                $line = explode('|', $line[1]);

                $rows[] = array(
                    'response_time' => $line[1],
                    'from_cache' => $line[0],
                    'created_at' => date('Y-m-d H:i:s'),
                );

                if (count($rows) > 100 && !$this->isLogged) {
                    $totalRowsNumber += count($rows);
                    $connection->insertArray($tableName, array('response_time', 'from_cache', 'created_at'), $rows);
                    $rows = array();
                }
                if ($totalRowsNumber >= $importLimit) {
                    break;
                }

                Mage::dispatchEvent($this->eventName, array('line' => $line));
            }

            if (count($rows) > 0 && !$this->isLogged) {
                $connection->insertArray($tableName, array('response_time', 'from_cache', 'created_at'), $rows);
            }

            unlink($filePath);
        }

        return true;
    }

    public function logAggregate()
    {
        $this->getResource()->aggregate();

        return true;
    }


    protected function isMobile() {
        if (strpos(Mage::getDesign()->getTheme('layout'), 'mobile') !== false) {
            return true;
        }

        foreach ($this->getConfig()->getUserAgentSegmentation() as $segment) {
            if ($segment['useragent_regexp']
                && preg_match($segment['useragent_regexp'], Mage::helper('core/http')->getHttpUserAgent())) {
                    return true;
            }
        }

        if (($deviceType = Mage::helper('fpc/mobile')->getMobileDeviceType())
            && $deviceType == Mirasvit_FpcCrawler_Model_Config::MOBILE_GROUP) {
                return true;
        }

        if (Mage::helper('mstcore')->isModuleInstalled('AW_Mobile2')
            && Mage::helper('aw_mobile2')->isCanShowMobileVersion()) {
                return true;
        }

        return false;
    }

    public function getConfig()
    {
        return Mage::getSingleton('fpc/config');
    }

    public function getCrawlerConfig()
    {
        return Mage::getSingleton('fpccrawler/config');
    }
}
