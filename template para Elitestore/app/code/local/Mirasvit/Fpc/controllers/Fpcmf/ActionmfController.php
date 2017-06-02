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



class Mirasvit_Fpc_Fpcmf_ActionmfController extends Mage_Core_Controller_Front_Action
{
    /**
     * @var Mirasvit_Fpc_Helper_Fpcmf_Debugmf
     */
    protected $_debug;

    public function _construct()
    {
        $this->_debug = new Mirasvit_Fpc_Helper_Fpcmf_Debugmf();
    }

    public function processAction()
    {
        $processor = Mage::getSingleton('fpc/requestmf_processormf');
        $storage = Mage::registry('current_storage');
        $content = $storage->getContent();

        foreach ($storage->getBlocksInApp() as $block) {
            $block->applyToContentInApp($content);
        }

        $this->_debug->appendDebugInformation($content, $storage, Mirasvit_Fpc_Model_Configmf::HIT);
        Mirasvit_Fpc_Model_Logmf::log($storage, Mirasvit_Fpc_Model_Configmf::HIT);

        $this->getResponse()
            ->setHeader('Fpc-Cache-Id', $storage->getCacheId())
            ->appendBody($content)
            ;
    }
}
