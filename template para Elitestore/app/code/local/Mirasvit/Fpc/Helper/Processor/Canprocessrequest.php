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



class Mirasvit_Fpc_Helper_Processor_Canprocessrequest extends Mage_Core_Helper_Abstract
{
    /**
     * @var Mirasvit_Fpc_Helper_Request
     */
    protected $_requestHelper;

    /**
     * @var Mirasvit_Fpc_Model_Config
     */
    protected $_config;

    public function __construct()
    {
        $this->_requestHelper = Mage::helper('fpc/request');
        $this->_config = Mage::getSingleton('fpc/config');
    }

    /**
     * Check if this request is allowed for process
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @return bool
     */
    public function canProcessRequest($request = null)
    {
        $result = Mage::app()->useCache('fpc');

        $response = Mage::app()->getResponse();
        if ($response->getHttpResponseCode() != 200) {
            $result = false;
        }

        if ($this->_requestHelper->isRedirect()) {
            $result = false;
        }

        if ($request && strtolower($request->getActionName()) == 'noroute') {
            $result = false;
        }

        if ($request && Mage::helper('mstcore')->isModuleInstalled('Fishpig_NoBots')) {
            if (($bot = Mage::helper('nobots')->getBot(false)) !== false) {
                if ($bot->isBanned()) {
                    $result = false;
                }
            }
        }

        if ($request && $this->isTbDevelopAllowed()) {
            $result = false;
        }

        $freeHddSpace = Mage::helper('fpc')->showFreeHddSpace(false, true);
        if ($freeHddSpace !== false
            && $freeHddSpace <= Mirasvit_Fpc_Model_Config::ALLOW_HDD_FREE_SPACE
        ) {
            $result = false;
        }

        if ($result) {
            $result = $this->_requestHelper->isIgnoredParams();
        }

        if ($result) {
            $result = !(count($_POST) > 0);
        }

        if ($result) {
            $result = Mage::app()->getStore()->getId() != 0;
        }

        if ($result) {
            $result = $this->_config->getCacheEnabled(Mage::app()->getStore()->getId());
        }

        if ($result && $this->_requestHelper->isIgnoredPage()) {
            $result = false;
        }

        if ($request) {
            $action =  Mage::helper('fpc')->getFullActionCode();
            if (!count($this->_config->getCacheableActions())) {
               $result = false;
            }
            if ($result) {
                $result = in_array($action, $this->_config->getCacheableActions());
            }
        }

        if ($result && isset($_GET)) {
            $maxDepth = $this->_config->getMaxDepth();
            $result = count($_GET) <= $maxDepth;
        }

        return $result;
    }

    public function isTbDevelopAllowed()
    {
        if (Mage::helper('mstcore')->isModuleInstalled('TB_Develop')
            && Mage::helper('develop')->isAllowed()) {
                return true;
        }

        return false;
    }
}