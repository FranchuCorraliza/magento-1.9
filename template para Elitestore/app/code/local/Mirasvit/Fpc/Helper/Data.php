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



class Mirasvit_Fpc_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected static $_ignoredUrlParams = array();

    protected function _getIgnoredUrlParams()
    {
        if (!self::$_ignoredUrlParams) {
            self::$_ignoredUrlParams = $this->getConfig()->getIgnoredUrlParams();
        }

        return self::$_ignoredUrlParams;
    }

    protected function _getCacheInfo()
    {
        $info = array('size' => 0, 'number' => 0);

        $cache = Mage::getSingleton('fpc/cache')->getCacheInstance();
        $frontend = $cache->getFrontend();
        $backend = $frontend->getBackend();

        $cacheDir = Mage::getBaseDir('cache');
        if (Mirasvit_Fpc_Model_Cache::$cacheDir) {
            $cacheDir = Mirasvit_Fpc_Model_Cache::$cacheDir;
        }
        $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($cacheDir), RecursiveIteratorIterator::SELF_FIRST);
        foreach ($objects as $name => $object) {
            if ($object->isFile()) {
                $info['size'] += $object->getSize();
                $info['number']++;
            }
        }

        return $info;
    }

    public function getCacheSize()
    {
        $info = $this->_getCacheInfo();

        return $info['size'];
    }

    public function getCacheNumber()
    {
        $info = $this->_getCacheInfo();

        return $info['number'];
    }

    public function getNormalizedUrl($protocol = false)
    {
        $uri = false;

        if (isset($_SERVER['HTTP_HOST'])) {
            $uri = $_SERVER['HTTP_HOST'];
        } elseif (isset($_SERVER['SERVER_NAME'])) {
            $uri = $_SERVER['SERVER_NAME'];
        }

        if ($uri) {
            if (isset($_SERVER['REQUEST_URI'])) {
                $uri .= $_SERVER['REQUEST_URI'];
                $uri = strtok($uri, '?');
            } elseif (!empty($_SERVER['IIS_WasUrlRewritten']) && !empty($_SERVER['UNENCODED_URL'])) {
                $uri .= $_SERVER['UNENCODED_URL'];
            } elseif (isset($_SERVER['ORIG_PATH_INFO'])) {
                $uri .= $_SERVER['ORIG_PATH_INFO'];
            }

            $query = $_GET;
            foreach ($this->_getIgnoredUrlParams() as $param) {
                if (isset($query[$param])) {
                    unset($query[$param]);
                }
            }
            ksort($query);
            $query = http_build_query($query);
            if ($query) {
                $uri .= '?' . $query;
            }
        }

        if ($protocol) {
            $ssl = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? true : false;
            $sp = strtolower($_SERVER['SERVER_PROTOCOL']);
            $protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
            $uri = $protocol . '://' . $uri;
        }

        return $uri;
    }

    public function checkCronStatusFunctionVersion() //check if we use new version of function
    {
        $checkCronStatusReflectionMethod = new ReflectionMethod('Mirasvit_MstCore_Helper_Cron', 'checkCronStatus');
        if (is_object($checkCronStatusReflectionMethod)
            && $checkCronStatusReflectionMethod->getNumberOfParameters() > 2
        ) {
            return true;
        }

        return false;
    }

    //MW Dailydeal Timer compatibility
    public function prepareMwDailydealTimer(&$content)
    {
        if (Mage::registry('current_product_id') && Mage::helper('mstcore')->isModuleInstalled('MW_Dailydeal')) {
            $_deal = Mage::getModel('dailydeal/dailydeal')->getCollection()->loadcurrentdeal(Mage::registry('current_product_id'));
            if ($_deal && is_object($_deal)) {
                $remainSecond = strtotime($_deal->getEndDateTime()) - Mage::getModel('core/date')->timestamp();
                $content = preg_replace('/var product_detail_server_time = {[^s]+second : [0-9]+,/ims', 'var product_detail_server_time = { second : ' . $remainSecond . ',', $content);
            }
        }

        return $this;
    }

    public function showCronStatusError($errorHtml = false)
    {
        if ($this->checkCronStatusFunctionVersion()) {
            $cronStatus = Mage::helper('mstcore/cron')->checkCronStatus(false, false, 'Cron job is required for correct work of Full Page Cache.');
            if ($cronStatus !== true && !$errorHtml) {
                Mage::getSingleton('adminhtml/session')->addError($cronStatus);
            }
        }

        return $cronStatus;
    }

    public function showExtensionDisabledInfo($errorHtml = false)
    {
        $info = array();
        $storeInfo = array();
        $activeStoreCount = 0;

        foreach (Mage::app()->getStores() as $store) {
            if ($store->getIsActive()) {
                $activeStoreCount += 1;
                if (!$this->getConfig()->getCacheEnabled($store->getId())) {
                    $storeInfo[] = 'Full Page Cache disabled for "' . $store->getName() . '" store — ' . $store->getBaseUrl() . '&nbsp;&nbsp;&nbsp;( ID: ' . $store->getId() . ')
                    in <a href="' . Mage::helper("adminhtml")->getUrl('*/system_config/edit/section/fpc/website/' . $store->getWebsite()->getCode() . '/store/' . $store->getCode())
                        . '" target="_blank">System->Configuration->Full Page Cache->General Settings</a>';
                }
            }
        }

        if ($activeStoreCount == count($storeInfo) && !$this->getConfig()->getCacheEnabled()) {
            $info[] = 'Full Page Cache disabled in <a href="' . Mage::helper("adminhtml")->getUrl('*/system_config/edit/section/fpc') . '" target="_blank">System->Configuration->Full Page Cache->General Settings</a>';
        } else {
            $info = array_merge($info, $storeInfo);
        }

        if (!Mage::app()->useCache('fpc')) {
            $info[] = 'Full Page Cache disabled in "Cache Storage Management" ( <a href="' . Mage::helper("adminhtml")->getUrl('*/cache') . '" target="_blank">System->Cache Management</a> )';
        }

        $infoText = implode('<br/>', $info);

        if (!$errorHtml && $infoText) {
            Mage::getSingleton('adminhtml/session')->addError($infoText);
            return true;
        }

        return $infoText;
    }

    public function showFreeHddSpace($errorHtml = false, $freeValue = false)
    {
        $cacheType = Mage::getSingleton('fpc/cache')
            ->getCacheInstance()
            ->getFrontend()
            ->getBackend();

        $infoText = '';
        $dir = '/';
        @$free = disk_free_space($dir);
        @$total = disk_total_space($dir);
        if (!$total
            || get_class($cacheType) == 'Cm_Cache_Backend_Redis'
            || get_class($cacheType) == 'Mage_Cache_Backend_Redis'
        ) {
            return false;
        }
        $freeToMb = $free / (1024 * 1024);
        $totalToMb = $total / (1024 * 1024);

        if ($freeValue) {
            return $freeToMb;
        }

        if ($freeToMb <= Mirasvit_Fpc_Model_Config::ALLOW_HDD_FREE_SPACE) {
            $infoText = 'You have HDD free space ' . $freeToMb . ' Mb from ' . $totalToMb . ' total Mb. FPC stop add pages in cache. Increase the free space on the disk.';
        }

        if (!$errorHtml && $infoText) {
            Mage::getSingleton('adminhtml/session')->addError($infoText);
        }

        return $infoText;
    }

    public function getConfig()
    {
        return Mage::getSingleton('fpc/config');
    }

    public function getFullActionCode()
    {
        $request = Mage::app()->getRequest();
        return strtolower($request->getModuleName() . '/' . $request->getControllerName() . '_' . $request->getActionName());
    }

    public function getSessionSize() {
        if(isset($_SESSION)) {
            return strlen(serialize($_SESSION))/1000000; //Mb-
        }

        return false;
    }
}
