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



class Mirasvit_Fpc_Adminhtml_Fpc_FlushcacheController extends Mage_Adminhtml_Controller_Action
{
	protected function _isAllowed()
	{
	    return Mage::getSingleton('admin/session')->isAllowed('system/fpc');
	}

    public function flushAction()
    {
        $data = $this->getRequest()->getPost();
        if ($data && isset($data['fpc_flushcache'])
            && $data['fpc_flushcache']) {
                $storeTags = array('FPCSTORE_' . $data['fpc_flushcache']);
                Mage::app()->getCache()->clean('matchingAnyTag', $storeTags);
                $storeInfo = $this->getStoreInfo($data['fpc_flushcache']);
                Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('adminhtml')
                    ->__('Full Page Cache cache storage has been flushed for store: ' . $storeInfo));
        } elseif ($data && isset($data['fpc_flushcache'])) {
            Mage::getSingleton('adminhtml/session')->addWarning(Mage::helper('adminhtml')
                ->__('Store is not selected. Cache storage wasn\'t flushed.'));
        }

        $this->_redirectReferer();
    }

    protected function getStoreInfo($storeId) {
        $store = Mage::getModel('core/store')->load($storeId);

        return $store->getName() . ' — '. $store->getBaseUrl() . '&nbsp;&nbsp;&nbsp;( ID: ' . $store->getId() . ')';
    }


}
