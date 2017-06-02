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



class Mirasvit_Fpc_Helper_Fpcmf_Debugmf extends Mage_Core_Helper_Abstract
{
    /**
     * @var string
     */
    protected $rowClass = null;

    /**
     * @var Mirasvit_Fpc_Model_Configmf
     */
    protected $_config;

    public function __construct()
    {
        $this->_config = new Mirasvit_Fpc_Model_Configmf();
    }

    /**
     * @param string $content
     * @param Mirasvit_Fpc_Model_Storagemf $storage
     * @param string $loadType
     * @return void
     */
    public function appendDebugInformation(&$content, $storage, $loadType)
    {
        $customDependence = new Mirasvit_Fpc_Helper_CustomDependence();
        if(!$customDependence->isMfCache(true)) {
            return false;
        }

        if (!$debugInfo = $this->_config->isDebugInfoEnabled()) {
            return true;
        }

        $time = round(microtime(true) - $_SERVER['FPC_TIME'], 3);

        $hit = 'Cache Hit';
        $cacheClass = 'm-fpc-debug-info-hit';
        if ($loadType ==  Mirasvit_Fpc_Model_Configmf::MISS) {
            $hit = 'Cache Miss';
            $cacheClass = 'm-fpc-debug-info-miss';
        }

        if ($debugInfo == 2) {
            $info = $this->getSmallInfoContent($loadType, $storage, $debugInfo, $time, $cacheClass, $hit);
            $content .= $info;
            return $this;
        }

        $info = '
        <div class="m-fpc-debug-info ' . $cacheClass . '">
            <h1 class="m-fpc-h1">Full Page Cache(mf)</h1>
            <div class="m-fpc-debug-info-main-block">
                <h2 class="m-fpc-hit-info">' . $hit . '</h2>';


        $info .= '
            <h2 class="m-fpc-time-text">Response time</h2>
            <div class="m-fpc-time">' . $time
            . '<span class="m-fpc-time-sec-text">s</span></div>';
        $info .= '<div id="m-fpc-info-scroll-hide-show" class="m-fpc-info-scroll"><div class="m-fpc-detail-info-table">';

        $info .= $this->getDetailInfo($storage);
        $info .= '</div></div>';
        $info .= '<div id="m-fpc-detail-info-hide-show-button" class="m-fpc-detail-info-hide-show-button-style" onclick="fpcDetailInfoHide(this, false)">hide details</div>';


        $info = $this->getFlushButtonInfo($loadType, $storage, $info, $debugInfo);

        $info .= '
            </div>
        </div>';

        $info .= $this->getFpcDetailInfoJs();

        $content .= $info;

        return true;
    }

    /**
     * @param string $isHit
     * @param Mirasvit_Fpc_Model_Storagemf $storage
     * @param int $debugInfo
     * @param float  $time
     * @param string $cacheClass
     * @param string $hit
     * @return string
     */
    protected function getSmallInfoContent($isHit, $storage, $debugInfo, $time, $cacheClass, $hit)
    {
        $smallBlockTime = ': ' . $time . 's';
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

    /**
     * @param Mirasvit_Fpc_Model_Storagemf $storage
     * @return string
     */
    protected function getDetailInfo($storage)
    {
        $info = array();
        $infoPrepared = '';

        $info[] = 'Cache ID: '.$storage->getCacheId();
        $info[] = 'Lifetime: '.$storage->getCacheLifetime();
        $info[] = 'Created At: '.date('d M, Y H:i:s', $storage->getCreatedAt());
        $info[] = 'Current Time: '.date('d M, Y H:i:s', time());
        $info[] = 'Tags: <br>&nbsp;&nbsp;&nbsp;&nbsp;'.implode('<br>&nbsp;&nbsp;&nbsp;&nbsp;', $storage->getCacheTags());

        if ($storage->getBlocksInApp()) {
            foreach ($storage->getBlocksInApp() as $block) {
                $info[] = 'In app: '.$block->getBlockName().' '.round($block->getRenderTime(), 2);
            }
        }

        if ($storage->getBlocksWithoutApp()) {
            foreach ($storage->getBlocksWithoutApp() as $block) {
                $info[] = 'Without app: '.$block->getBlockName().' '.round($block->getRenderTime(), 2);
            }
        }

        if ($storage->getBlocksNotApplied()) {
            foreach ($storage->getBlocksNotApplied() as $block) {
                $info[] = 'Not applied: '.$block->getBlockName();
            }
        }

        if (isset($_SESSION)) {
            foreach ($_SESSION as $key => $value) {
                if (substr($key, 0, 4) == 'FPC_') {
                    $info[] = 'Session: '.$key.' = '.$value;
                }
            }
        } else {
            $info[] = 'SESSION IS NOT SET';
        }

        foreach ($info as $keyDetail => $detail) {
            if (is_array($detail)) {
                $detail = implode('<br>', $info);
            }
            $this->rowClass = $this->getRowClass($this->rowClass);
            $infoPrepared .= '
                 <div class="m-fpc-detail-row ' . $this->rowClass . '">
                    <div class="m-fpc-detail-col">' . $detail . '</div>
                 </div>';
        }

        return $infoPrepared;
    }

    /**
     * @param string $rowClass
     * @return string
     */
    protected function getRowClass($rowClass)
    {
        if (!$rowClass || $rowClass == 'm-fpc-light') {
            $rowClass = 'm-fpc-dark';
        } elseif ($rowClass == 'm-fpc-dark') {
            $rowClass = 'm-fpc-light';
        }

        return $rowClass;
    }

    /**
     * @param string $isHit
     * @param Mirasvit_Fpc_Model_Storagemf $storage
     * @param string $info
     * @param int $debugInfo
     * @return string
     */
    protected function getFlushButtonInfo($isHit, $storage, $info, $debugInfo)
    {
        $styleClass = ($debugInfo == 2) ? "m-fpc-flush-cache-button-style-small" : "m-fpc-flush-cache-button-style";
        if ($isHit == Mirasvit_Fpc_Model_Configmf::HIT && $this->_config->getDebugButtonConfiguration()) {
            $info .= '<span id="m-fpc-flush-cache-button" class="' . $styleClass . '" onclick="callFpcFlushCache(this)">flush cache</span>';
            $info .= $this->getFpcFlushCacheJs($storage);
        }

        return $info;
    }

    /**
     * @param Mirasvit_Fpc_Model_Storagemf $storage
     * @return string
     */
    protected function getFpcFlushCacheJs($storage)
    {
        $fpcTags = $storage->getCacheTags();
        $cacheId = $storage->getCacheId();
        $script = '<script type="text/javascript">
        function callFpcFlushCache(e)
        {
            var fpcTags = \'' . json_encode($fpcTags) . '\';
            var cacheId = "' . $cacheId . '";
            new Ajax.Request("' . '/fpc/fpc_flushCache/flush' . '" , {
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

    /**
     * @return string
     */
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

    /**
     * @param string $content
     * @param object $container
     * @param int $fromCache
     * @param float $startTime
     * @return void
     */
    public static function appendDebugInformationToBlock(&$content, $container, $fromCache, $startTime)
    {
        $config = new Mirasvit_Fpc_Model_Configmf();

        if (!$config->isDebugHintsEnabled()) {
            return false;
        }

        $hit = 'Cache Hit';
        if (!$fromCache) {
            $hit = 'Cache Miss';
        }

        $hit .= ' ' . round(microtime(true) - $startTime, 3).' s.';

        $blockType = $container->getBlockType();
        $blockName = $container->getBlockName();

        $infoText = $blockType . ' ' . $blockName .' ('.$hit.')'.'<br>'.hash('crc32', $container->getCacheId());
        $info = '<div style="position:absolute; left:0; top:0; padding:2px 5px; background:#faa; color:#333; font:normal 9px Arial;
        text-align:left !important; z-index:998;text-transform:none;">'.$infoText.'</div>';
        $content = '<div style="position:relative; border:1px dotted red; margin:6px 2px; padding:18px 2px 2px 2px; zoom:1;">'.$info.$content.'</div>';
    }

}
