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
 * @package   Fast Asynchronous Re-indexing
 * @version   1.1.6
 * @build     285
 * @copyright Copyright (C) 2014 Mirasvit (http://mirasvit.com/)
 */


require_once 'Mage/Index/controllers/Adminhtml/ProcessController.php';

class Mirasvit_AsyncIndex_Adminhtml_ProcessController extends Mage_Index_Adminhtml_ProcessController
{
    public function reindexProcessAction()
    {
        parent::reindexProcessAction();

        if (Mage::getSingleton('asyncindex/config')->isFullReindexAllowed()) {
            $process = $this->_initProcess();
            $this->_getSession()->clear();
            $this->_getSession()->addSuccess(
                Mage::helper('index')->__('Task for reindex %s index is added to queue.
                    Reindex will be done in background by cron.', $process->getIndexer()->getName())
            );
        }
    }

    public function massReindexAction()
    {
        if (!Mage::getSingleton('asyncindex/config')->isFullReindexAllowed()) {
            return parent::massReindexAction();
        }

        $session    = $this->_getSession();
        $indexer    = Mage::getSingleton('index/indexer');
        $processIds = $this->getRequest()->getParam('process');
        if (empty($processIds) || !is_array($processIds)) {
            $session->addError(Mage::helper('index')->__('Please select Indexes'));
        } else {
            try {
                foreach ($processIds as $processId) {
                    /* @var $process Mage_Index_Model_Process */
                    $process = $indexer->getProcessById($processId);
                    if ($process) {
                        $process->reindexEverything();
                    }
                }
                $count = count($processIds);
                $session->addSuccess(
                    Mage::helper('index')->__('Total of %d index(es) are added to queue.
                        Reindex will be done in background by cron.', $count)
                );
            } catch (Mage_Core_Exception $e) {
                $session->addError($e->getMessage());
            } catch (Exception $e) {
                $session->addException($e, Mage::helper('index')->__('Cannot initialize the indexer process.'));
            }
        }

        $this->_redirect('*/*/list');
    }
}
