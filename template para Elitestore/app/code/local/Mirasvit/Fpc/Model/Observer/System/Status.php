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



class Mirasvit_Fpc_Model_Observer_System_Status extends Varien_Debug
{
    /**
     * @var Mirasvit_Fpc_Helper_Data
     */
    protected $_fpcHelper;

    public function __construct()
    {
        $this->_fpcHelper = Mage::helper('fpc');
    }

    /**
     * Check for fpc section in admin panel
     */
    public function checkStatus()
    {
        if (($request = Mage::app()->getRequest())
            && (Mage::app()->getRequest()->getParam('section') == 'fpc'
                || Mage::app()->getRequest()->getParam('section') == 'fpccrawler')
            && !$request->isPost()
        ) {
            $isShowOptimalConfigurationInfo = true;
            $cronStatus = $this->_fpcHelper->showCronStatusError();
            $freeHddSpace = $this->_fpcHelper->showFreeHddSpace(false, false);

            if ((is_string($freeHddSpace) && strlen($freeHddSpace) > 20)
                || (is_string($cronStatus) && strlen($cronStatus) > 20)) {
                    $isShowOptimalConfigurationInfo = false;
            }

            if ($isShowOptimalConfigurationInfo
                && Mage::getModel('core/variable')->loadByCode(Mirasvit_Fpc_Model_Config::OPTIMAL_CONFIG_MESSAGE)->getValue()) {
                    $this->showOptimalConfigurationInfo();
            }
        }
    }

    /**
     * Show an advice after FPC install
     */
    protected function showOptimalConfigurationInfo()
    {
        $script = '<script type="text/javascript">function callFpcHideMessageController(e)
        {
            var isChecked = (e.checked == true) ? 1 : 0;
            new Ajax.Request("' . Mage::helper('adminhtml')->getUrl('*/fpc_hideMessage/update') . '" , {
                method: "Post",
                parameters: {"checked":isChecked},
                // onComplete: function(transport) {
                //     alert(transport.responseText);
                // }
            });
        } </script>';

        $urlOptimalConfig = Mage::helper("adminhtml")->getUrl('*/fpc_optimalConfiguration/index/');
        $urlCrawlerConfig = Mage::helper("adminhtml")->getUrl('*/system_config/edit/section/fpccrawler');
        $fpcVersion = Mage::helper('fpc/version')->getCurrentFpcVersion();

        $documetation = 'More info about the FPC extension you can learn follow the link <a target="_blank" href="https://mirasvit.com/doc/fpc/'
        . $fpcVersion . '/">"Getting Started"</a>';

        $info = '<div>It is a good idea check "Suggest Optimal Configuration" ( <a target="_blank" href="'
            . $urlOptimalConfig
            . '">System->Full Page Cache->Suggest Optimal Configuration</a> ) and push "Generate crawler urls" in "Full Page Cache Crawler" ( <a target="_blank" href="'
            . $urlCrawlerConfig
            .  '">System->Full Page Cache->Crawler Settings</a> )'
            ."<br/>"
            . $documetation
            ."<br/>"
            . 'Don\'t show again <input onchange="callFpcHideMessageController(this)" type="checkbox" name="fpc_hide_message" class="massaction-checkbox"></div>'
            . $script
            ;

        Mage::getSingleton('adminhtml/session')->addNotice($info);
    }
}
