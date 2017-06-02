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



class Mirasvit_Fpc_Adminhtml_Fpc_HideMessageController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/fpc');
    }

    public function updateAction()
    {
        if (Mage::app()->getRequest()->getParam('checked')) {
            $this->setVariableValue(0);
        } else {
            $this->setVariableValue(1);
        }
    }

    protected function setVariableValue($value) {
        Mage::getModel('core/variable')
            ->loadByCode(Mirasvit_Fpc_Model_Config::OPTIMAL_CONFIG_MESSAGE)
            ->setCode(Mirasvit_Fpc_Model_Config::OPTIMAL_CONFIG_MESSAGE)
            ->setName('Show FPC Optimal Config Message')
            ->setPlainValue($value)
            ->save();
    }
}
