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



class Mirasvit_Fpc_Helper_Debug extends Mage_Core_Helper_Abstract
{
    protected $rowClass = null;

    /**
     * @var Mirasvit_Fpc_Model_Config
     */
    protected $_config;

    /**
     * @var bool|array
     */
    protected $_custom;

    public function __construct()
    {
        $this->_config = Mage::getSingleton('fpc/config');
        $this->_custom = Mage::helper('fpc/custom')->getCustomSettings();
    }

    public function appendDebugInformation(&$content, $isHit = 0, $storage = false)
    {
        if (!$debugInfo = $this->_config->isDebugInfoEnabled()) {
            return $this;
        }

        if ($this->_custom
            && in_array('isMfCache', $this->_custom)
            && !Mage::helper('fpc/customDependence')->isMfCache(false)) {
                return $this;
        }

        $time = round(microtime(true) - $_SERVER['FPC_TIME'], 3);
        $hit = 'Cache Hit';
        $cacheClass = 'm-fpc-debug-info-hit';
        if ($isHit == 0) {
            $hit = 'Cache Miss';
            $cacheClass = 'm-fpc-debug-info-miss';
        }
        if ($isHit == 2) {
            $hit = 'Not cacheable';
            $cacheClass = 'm-fpc-debug-info-not-cacheable';
        }

        if ($debugInfo == 2) {
            $info = $this->getSmallInfoContent($isHit, $storage, $debugInfo, $time, $cacheClass, $hit);
            $content .= $info;
            return $this;
        }

        if ($storage && is_object($storage)) {
            $detailInfo = $this->getStorageData($storage, $isHit);
        }

        $info = '
        <div class="m-fpc-debug-info ' . $cacheClass . '">
            <h1 class="m-fpc-h1">Full Page Cache</h1>
            <div class="m-fpc-debug-info-main-block">
                <h2 class="m-fpc-hit-info">' . $hit . '</h2>';

        if ($isHit == 2) {
            $info .= '<h2 class="m-fpc-action">Action: ' . Mage::helper('fpc')->getFullActionCode() . '</h2>';
        } else {
            $info .= '
                <h2 class="m-fpc-time-text">Response time</h2>
                <div class="m-fpc-time">' . $time
                . '<span class="m-fpc-time-sec-text">s</span></div>';
            $info .= '<div id="m-fpc-info-scroll-hide-show" class="m-fpc-info-scroll"><div class="m-fpc-detail-info-table">';
            foreach ($detailInfo as $keyDetail => $detail) {
                $this->rowClass = $this->getRowClass($this->rowClass);
                $info .= '
                     <div class="m-fpc-detail-row ' . $this->rowClass . '">
                        <div class="m-fpc-detail-col">' . $keyDetail . '</div><div class="m-fpc-detail-col">' . $detail . '</div>
                     </div>';
            }
            $info .= '</div></div>';
            $info .= '<div id="m-fpc-detail-info-hide-show-button" class="m-fpc-detail-info-hide-show-button-style" onclick="fpcDetailInfoHide(this, false)">hide details</div>';
        }

        $info = $this->getFlushButtonInfo($isHit, $storage, $info, $debugInfo);

        $isUseCache = Mage::app()->useCache('fpc');
        $isCacheEnabled = $this->_config->getCacheEnabled(Mage::app()->getStore()->getStoreId());
        $isIgnoredPage = Mage::helper('fpc/request')->isIgnoredPage();

        if ($isHit == 2 && $isIgnoredPage && $isUseCache && $isCacheEnabled) {
            $class = "m-fpc-action";
        } elseif ($isHit == 2 && $isIgnoredPage) {
            $class = "m-fpc-action m-fpc-ignored";
        }

        if (isset($class)) {
            $info .= '<h2 class="' . $class . '">Ignored in "Ignored Pages"</h2>';
        }

        if ($isHit == 2 && $isUseCache && $isCacheEnabled
            && Mage::helper('fpc/processor_canprocessrequest')->isTbDevelopAllowed()) {
                $info .= '<h2 class="m-fpc-disabled-info">FPC disabled for your IP because TB_Develop is enabled</h2>';
        }

        if ($isHit == 2 && !$isUseCache) {
            $info .= '<h2 class="m-fpc-disabled-info">FPC disabled in Cache Storage Management</h2>';
        }

        if ($isHit == 2 && !$isCacheEnabled) {
            $info .= '<h2 class="m-fpc-disabled-info">FPC disabled in Full Page Cache Settings</h2>';
        }

        $info .= '
            </div>
        </div>';

        if ($isHit != 2) {
            $info .= $this->getFpcDetailInfoJs();
        }

        $content .= $info;

        return $this;
    }

    protected function getSmallInfoContent($isHit, $storage, $debugInfo, $time, $cacheClass, $hit)
    {
        $smallBlockTime = ($isHit == 2) ? '' : ': ' . $time . 's';
        $info = '
            <div class="m-fpc-debug-info-small ' . $cacheClass . '">
                <div class="m-fpc-debug-info-main-block-small">
                    <h2 class="m-fpc-hit-info-small">' . $hit . $smallBlockTime. ' </h2>';

        $info = $this->getFlushButtonInfo($isHit, $storage, $info, $debugInfo);

        $info .= '
            </div>
        </div>';

        return $info;
    }

    protected function getFlushButtonInfo($isHit, $storage, $info, $debugInfo)
    {
        $styleClass = ($debugInfo == 2) ? "m-fpc-flush-cache-button-style-small" : "m-fpc-flush-cache-button-style";
        if ($isHit == 1 && $this->_config->getDebugButtonConfiguration()) {
            $info .= '<span id="m-fpc-flush-cache-button" class="' . $styleClass . '" onclick="callFpcFlushCache(this)">flush cache</span>';
            $info .= $this->getFpcFlushCacheJs($storage);
        }

        return $info;
    }

    public function appendDebugInformationToBlock(&$content, $container, $fromCache, $startTime)
    {
        if (!Mage::getSingleton('fpc/config')->isDebugHintsEnabled()) {
            return $this;
        }

        $hit = 'Cache Hit';
        if (!$fromCache) {
            $hit = 'Cache Miss';
        }

        $hit .= ' ' . round(microtime(true) - $startTime, 3) . ' s.';

        $definition = $container->getDefinition();

        $infoText = $definition['block'];

        if (isset($definition['template']) && $definition['template']) {
            $infoText .= ' ' . $definition['template'];
        } elseif ($definition['block'] == 'cms/block' && $definition['block_id']) {
            $infoText .= ' ' . $definition['block_id'];
        } elseif ($definition['block'] != 'cms/block' && $definition['block_name']) {
            $infoText .= ' ' . $definition['block_name'];
        }

        $cacheId = ($fromCache) ?  hash('crc32', $container->getCacheId()) : '';
        $infoText .= ' (' . $hit . ')' . '<br>' . $cacheId;
        $info = '<div style="position:absolute; left:0; top:0; padding:2px 5px; background:#faa; color:#333; font:normal 9px Arial;
        text-align:left !important; z-index:998;text-transform:none;">' . $infoText . '</div>';
        $content = '<div style="position:relative; border:1px dotted red; margin:6px 2px; padding:18px 2px 2px 2px; zoom:1;">' . $info . $content . '</div>';
    }

    public function getStorageData($storage, $isHit)
    {
        $detailInfo = array(
            'Created At'                                  => date('Y-m-d H:i:s', $storage->getCreatedAt()) . ' GMT',
            'Expires At'                                  => date('Y-m-d H:i:s', $storage->getCreatedAt() + $storage->getCacheLifetime()) . ' GMT',
            'Created By'                                  => $storage->getCreatedBy(),
            'Life time'                                   => $storage->getCacheLifetime() . 's' . ' (' . $storage->getCacheLifetime()/3600 . 'h)',
            'Action'                                      => Mage::helper('fpc')->getFullActionCode(),
            'Cache ID'                                    => $storage->getCacheId(),
            'Fpc switch for crawler time'                 => $this->getTimer('SWITCH_FOR_CRAWLER_TIME'),
            'Fpc can request time'                        => $this->getTimer('CHECK_PROCESS_REQUEST_TIME'),
            'Fpc self time'                               => $this->getTimer('SELF_TIME'),
            'Fpc send content time'                       => ($isHit) ? '<span id="m-fpc-send-content-time">FPC_SEND_CONTENT_TIME</span>' : 'No data',
            'Cache Tags'                                  => implode('<br/>', $storage->getCacheTags()),
            'Block update time'                           => $this->getUpdateTime(),
            'Total block update time'                     => $this->getUpdateTime(true),
            'Dependences time'                            => $this->getUpdateTime(false, true),
            'Total dependencies time'                     => $this->getUpdateTime(true, true),
            'Session size'                                => Mage::helper('fpc')->getSessionSize() . 'Mb',
            'Register Model Tag Observer Time'            => $this->getUpdateTime(false, false, Mirasvit_Fpc_Model_Config::REGISTER_MODEL_TAG),
            'Total Register Model Tag Observer Time'      => $this->getUpdateTime(true, false, Mirasvit_Fpc_Model_Config::REGISTER_MODEL_TAG),
            'Register Product Tag Observer Time'          => $this->getUpdateTime(false, false, Mirasvit_Fpc_Model_Config::REGISTER_PRODUCT_TAG),
            'Total Register Product Tag Observer Time'    => $this->getUpdateTime(true, false, Mirasvit_Fpc_Model_Config::REGISTER_PRODUCT_TAG),
            'Register Collection Tag Observer Time'       => $this->getUpdateTime(false, false, Mirasvit_Fpc_Model_Config::REGISTER_COLLECTION_TAG),
            'Total Register Collection Tag Observer Time' => $this->getUpdateTime(true, false, Mirasvit_Fpc_Model_Config::REGISTER_COLLECTION_TAG),
        );

        return $detailInfo;
    }

    protected function getUpdateTime($total = false, $dependences = false, $registerTag = false)
    {
        $prefix = 'FPC_BLOCK_';
        $data = 'No data';
        $update = array();

        if ($dependences) {
            $prefix = 'FPC_DEPENDENCES_';
        }

        if ($registerTag) {
            switch ($registerTag) {
                case Mirasvit_Fpc_Model_Config::REGISTER_MODEL_TAG:
                    $prefix = 'FPC_' . Mirasvit_Fpc_Model_Config::REGISTER_MODEL_TAG;
                    break;

                case Mirasvit_Fpc_Model_Config::REGISTER_PRODUCT_TAG:
                    $prefix = 'FPC_' . Mirasvit_Fpc_Model_Config::REGISTER_PRODUCT_TAG;
                    break;

                case Mirasvit_Fpc_Model_Config::REGISTER_COLLECTION_TAG:
                   $prefix = 'FPC_' . Mirasvit_Fpc_Model_Config::REGISTER_COLLECTION_TAG;
                    break;

                default:
                    break;
           }
        }

        foreach ($_SERVER as $key => $value) {
            if (strpos($key, $prefix) !== false && strpos($key, '_RESULT') !== false) {
                $updateKey = str_replace(array($prefix, '_RESULT'), '', $key);
                $update[$updateKey] = $value;
            }
        }

        if ($update && $total) {
            $time = 0;
            foreach ($update as $updateKey => $updateValue) {
                $time += $updateValue;
            }

            $data = $this->getConvertedTime($time) . 'ms';
        } elseif ($update && !$total) {
            foreach ($update as $updateKey => $updateValue) {
                $update[$updateKey] = $updateKey . ' - ' . $this->getConvertedTime($updateValue) . 'ms';
            }

            $data = implode('<br/>', $update);
        }

        return $data;
    }

    protected function getRowClass($rowClass)
    {
        if (!$rowClass || $rowClass == 'm-fpc-light') {
            $rowClass = 'm-fpc-dark';
        } elseif ($rowClass == 'm-fpc-dark') {
            $rowClass = 'm-fpc-light';
        }

        return $rowClass;
    }

    protected function getFpcFlushCacheJs($storage)
    {
        $fpcTags = $storage->getCacheTags();
        $cacheId = $storage->getCacheId();
        $script = '<script type="text/javascript">
        function callFpcFlushCache(e)
        {
            var fpcTags = \'' . json_encode($fpcTags) . '\';
            var cacheId = "' . $cacheId . '";
            new Ajax.Request("' . Mage::getUrl('fpc/fpc_flushCache/flush') . '" , {
                method: "Post",
                parameters: {"fpcTags":fpcTags,
                            "cacheId":cacheId},
                onComplete: function(transport) {
                    e.innerHTML = "cache flushed";
                    e.setAttribute("class", "m-fpc-flush-cache-button-style-flushed");
                }
            });
        } </script>';

        return $script;
    }


    protected function getFpcDetailInfoJs()
    {
        $js = '
        <script type="text/javascript">
            function fpcDetailInfoHide(elem, value) {
                if (elem) {
                    var elementText = elem.innerHTML;
                } else {
                    elem = document.getElementById("m-fpc-detail-info-hide-show-button")
                    elementText = value;
                }
                var infoBlock = document.getElementById("m-fpc-info-scroll-hide-show");
                if (elementText == "hide details") {
                    infoBlock.addClassName("m-fpc-info-scroll-hide");
                    elem.innerHTML = "show details";
                    setFpcToolbarCookie("m_fpc_toolbar_status", "hide details", 10);
                }
                if (elementText == "show details") {
                    infoBlock.removeClassName("m-fpc-info-scroll-hide");
                    elem.innerHTML = "hide details";
                    setFpcToolbarCookie("m_fpc_toolbar_status", "show details", 10);
                }
            }

            document.observe("dom:loaded", function(){
                var cookieStatus = checkFpcToolbarCookie();
                var fpcToolbarStatus = "hide details";

                if (!cookieStatus && fpcToolbarStatus) {
                    fpcDetailInfoHide(false, fpcToolbarStatus);
                } else if (cookieStatus) {
                    fpcDetailInfoHide(false, cookieStatus);
                } else {
                    fpcDetailInfoHide(false, "hide details");
                }
            });

            function setFpcToolbarCookie(cname, cvalue, exdays) {
                var path = "path=/";
                var d    = new Date();
                d.setTime(d.getTime() + (exdays*24*60*60*1000));
                var expires = "expires="+d.toUTCString();
                document.cookie = cname + "=" + cvalue + "; " + expires + "; " + path;
            }

            function getFpcToolbarCookie(cname) {
                var name = cname + "=";
                var ca = document.cookie.split(\';\');
                for(var i=0; i<ca.length; i++) {
                    var c = ca[i];
                    while (c.charAt(0)==\' \') c = c.substring(1);
                    if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
                }
                return "";
            }

            function checkFpcToolbarCookie() {
                var mFpcToolbar = getFpcToolbarCookie("m_fpc_toolbar_status");
                if (mFpcToolbar == "hide details" || mFpcToolbar == "show details") {
                    return mFpcToolbar;
                }

                return false;
            }
        </script>';

        return $js;
    }

    public function getSendContentTime()
    {
        if (!$this->_config->isDebugInfoEnabled()) {
            return false;
        }
        $fpcSendContentTime = Mage::helper('fpc/debug')->getTimer('FPC_SEND_CONTENT_TIME');

        return "<script type='text/javascript'>
            var fpcSendContentTime  = '" . $fpcSendContentTime . "';
            var fpcSendContentTimeElemnt = document.getElementById('m-fpc-send-content-time');
            if (typeof fpcSendContentTimeElemnt !== 'undefined' && fpcSendContentTimeElemnt!== null) {
                fpcSendContentTimeElemnt.innerHTML = fpcSendContentTime;
            }
        </script>";
    }

    /**
     * @param string $timer
     * @return $this
     */
    public function startTimer($timer)
    {
        $_SERVER['FPC_' . $timer] = microtime(true);

        return $this;
    }

    /**
     * @param string $timer
     * @return $this
     */
    public function stopTimer($timer)
    {
        if (isset($_SERVER['FPC_' . $timer])) {
            $_SERVER['FPC_' . $timer . '_RESULT'] = microtime(true) - $_SERVER['FPC_' . $timer];
        }

        return $this;
    }

    /**
     * @param string $timer
     * @return string
     */
    public function getTimer($timer)
    {
        if (isset($_SERVER['FPC_' . $timer . '_RESULT'])) {
            return $this->getConvertedTime($_SERVER['FPC_' . $timer . '_RESULT']) . 'ms';
        }

        return 'No data';
    }

    /**
     * Convert seconds to milliseconds
     * @param float $time
     * @return float
     */
    public function getConvertedTime($time)
    {
        return round($time * 1000, 1);
    }
}
