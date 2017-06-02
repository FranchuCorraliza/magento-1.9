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



class Mirasvit_FpcCrawler_Adminhtml_Fpccrawler_System_GenerateController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/fpc');
    }

    public function preDispatch()
    {
        parent::preDispatch();
        Mage::getDesign()->setTheme('mirasvit');
        return $this;
    }

    public function addUrlsInCrawlerAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function addUrlsInCrawlerStepAction()
    {
        if ($step = $this->getRequest()->getParam('step')) {
            $worker = Mage::getSingleton('fpccrawler/system_addurlsincrawler_worker');
            $worker->setStep($step);
            if ($worker->run()) {
                $this->loadLayout();
                $this->renderLayout();
            }
        }
    }
}