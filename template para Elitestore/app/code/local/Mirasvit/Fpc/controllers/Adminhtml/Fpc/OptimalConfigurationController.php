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



class Mirasvit_Fpc_Adminhtml_Fpc_OptimalConfigurationController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/fpc');
    }

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('system')
            ->_addBreadcrumb(Mage::helper('fpc')->__('Full Page Cache'), Mage::helper('fpc')->__('Full Page Cache'));

        return $this;
    }

    public function indexAction()
    {
        Mage::helper('fpc')->showFreeHddSpace(false, false);
        Mage::helper('fpc')->showExtensionDisabledInfo();
        Mage::helper('fpc')->showCronStatusError();

        $this->_initAction();

        $this->_addContent($this->getLayout()->createBlock('fpc/adminhtml_optimalConfiguration')->setTemplate('fpc/optimalconfiguration.phtml'));

        $this->renderLayout();
    }
}
