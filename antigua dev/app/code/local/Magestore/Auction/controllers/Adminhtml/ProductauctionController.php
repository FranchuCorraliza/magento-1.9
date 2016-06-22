<?php

class Magestore_Auction_Adminhtml_ProductauctionController extends Mage_Adminhtml_Controller_Action {

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('auction/productauction')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Product-Auction Manager'), Mage::helper('adminhtml')->__('Product-Auction Manager'));

        return $this;
    }

    public function indexAction() {
        Mage::helper('auction')->updateAuctionStatus();
        //$this->_title($this->__('Auction'))->_title($this->__('Manage Auctions'));
        $this->_initAction()
                ->renderLayout();
    }

    public function editAction() {
        Mage::getSingleton('core/session')->setData('is_search', false);
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('auction/productauction')
                ->setId($id)
                ->setStoreId($this->getRequest()->getParam('store'))
                ->loadByStore();

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('productauction_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('auction/productauction');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('ProductAuction Manager'), Mage::helper('adminhtml')->__('ProductAuction Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Add ProductAuction'), Mage::helper('adminhtml')->__('Add ProductAuction'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('auction/adminhtml_productauction_edit'))
                    ->_addLeft($this->getLayout()->createBlock('auction/adminhtml_productauction_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('auction')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {
        $this->editAction();
    }

    public function detailAction() {
        $id = $this->getRequest()->getParam('id');

        Mage::helper('auction')->autoUpdateBidStatus($id);

        $model = Mage::getModel('auction/productauction')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('productauction_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('auction/productauction');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('ProductAuction Manager'), Mage::helper('adminhtml')->__('ProductAuction Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Add ProductAuction'), Mage::helper('adminhtml')->__('Add ProductAuction'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('auction/adminhtml_productauction_bid'))
                    ->_addLeft($this->getLayout()->createBlock('auction/adminhtml_productauction_edit_bidtabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('auction')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function changeproductAction() {
        $product_id = $this->getRequest()->getParam('product_id');
        if ($product_id) {
            $product = Mage::getModel('catalog/product')->load($product_id);
            $product_name = $product->getName();
            $product_name = str_replace('"', '', $product_name);
            $product_name = str_replace("'", '', $product_name);
            $html = '<input type="hidden" id="newproduct_name" href="' . $this->getUrl('adminhtml/catalog_product/edit', array('id' => $product_id)) . '" name="newproduct_name" value="' . $product_name . '" >';
            $this->getResponse()->setHeader('Content-type', 'application/x-json');
            $this->getResponse()->setBody($html);
        }
    }

    public function importAction() {
        $this->_redirect('*/adminhtml_productauction/index', array());
        $this->loadLayout();
        $this->_setActiveMenu('auction/productauction');
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Import Auctions'), Mage::helper('adminhtml')->__('Import Auctions'));
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->_addContent($this->getLayout()->createBlock('*/adminhtml_productauction_import'));
        $this->renderLayout();
    }

    public function importPostAction() {
        if (isset($_FILES['filecsv'])) {
            try {
                $fileName = $_FILES['filecsv']['tmp_name'];
                $Object = new Varien_File_Csv();
                $dataFile = $Object->getData($fileName);
                $fields = array();
                $auctionData = array();
                $auction = Mage::getModel('auction/productauction');
                $count = 0;
                if (count($dataFile))
                    foreach ($dataFile as $col => $row) {
                        if ($col == 1) {
                            if (count($row))
                                foreach ($row as $index => $cell)
                                    $fields[$index] = (string) $cell;
                        }elseif ($col > 1) {
                            if (count($row))
                                foreach ($row as $index => $cell)
                                    if (isset($fields[$index])) {
                                        $auctionData[$fields[$index]] = $cell;
                                    }
                            if ($auction->import($auctionData))
                                $count++;
                            $auctionData = array();
                        }
                    }
                //	if($count){
                Mage::getSingleton('core/session')->addSuccess(Mage::helper('auction')->__('Imported success %s Auction(s)', $count));
                //	}
                $this->_redirect('*/*/index');

                return;
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError($e->getMessage());
                $this->_redirect('*/*/import');
                return;
            }
        }
        Mage::getSingleton('core/session')->addError(Mage::helper('auction')->__('No uploaded files'));
        $this->_redirect('*/*/import');
        return;
    }

    public function autobidlistAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function watcherlistAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function winnerlistAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function listproductAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('auction.edit.tab.product')
                ->setProduct($this->getRequest()->getPost('aproduct', null));
        $this->renderLayout();
    }

    public function listproductGridAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('auction.edit.tab.product')
                ->setProduct($this->getRequest()->getPost('aproduct', null));
        $this->renderLayout();
    }

    public function saveAction() {

        if ($data = $this->getRequest()->getPost()) {
            // check change status from processing to disable or not start
            if ($this->getRequest()->getParam('id') != null && !$this->checkStatus($this->getRequest()->getParam('id'), $this->getRequest()->getParam('status'))) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('auction')->__('Failure: this auction has bidder'));
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
            // end check change status

            if (isset($data['candidate_product_id']) && $data['candidate_product_id']) {
                $data['product_id'] = $data['candidate_product_id'];
            }

            if (isset($data['product_name']) && $data['product_name'] == '') {
                unset($data['product_name']);
            }

            $model = Mage::getModel('auction/productauction');
            $model->setData($data)
                    ->setStoreId($this->getRequest()->getParam('store'))
                    ->setId($this->getRequest()->getParam('id'));

            try {
                if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
                    $model->setCreatedTime(now())
                            ->setUpdateTime(now());
                } else {
                    $model->setUpdateTime(now());
                }

                if ($model->getStoreId()) {
                    $valueModel = Mage::getModel('auction/value')->loadByAuctionStore($model->getId(), $model->getStoreId());
                    $valueId = $valueModel->getId();
                    $valueModel->setData($model->getData())
                            ->setId($valueId)
                            ->save();
                } else {
                    $model->save();
                    $valueModel = Mage::getModel('auction/value');

                    $stores = Mage::app()->getStores();
                    foreach ($stores as $store) {
                        $valueModel->loadByAuctionStore($model->getId(), $store->getId());
                        $valueId = $valueModel->getId();
                        $valueModel->setData($model->getData())
                                ->setId($valueId)
                                ->setStoreId($store->getId())
                                ->setIsApplied(1)
                                ->save();
                    }
                }

                $status = $model->getStatus();
                if (($status == 5) || ($status == 6)) {
                    Mage::helper('auction')->setStautsAution($status, $model->getId());
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('auction')->__('Auction was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                Mage::helper('auction')->updateAuctionStatus($model->getId());
                if ($data['allow_buyout'] == 2 && ($status != 2 || $status != 6)) {
                    $timestamp = Mage::getModel('core/date')->timestamp(time());
                    $starttime = strtotime($model->getStartDate() . ' ' . $model->getStartTime());
                    $daytobuy = strtotime($model->getEndDate() . ' ' . $model->getEndTime()) + ($model->getDayToBuy() * 24 * 3600);
                    if ($timestamp >= $starttime && $timestamp <= $daytobuy) {
                        $quoteId = array();
                        $quotes = Mage::getModel('sales/quote')->getCollection()->addFieldToFilter('is_active', 1);
                        $quotes->getSelect()->join(array('a' => Mage::getSingleton('core/resource')->getTableName('sales/quote_item')), 'main_table.entity_id = a.quote_id', 'a.item_id');
                        $quotes->addFieldToFilter('a.product_id', $data["product_id"]);
                        if (count($quotes) > 0) {
                            foreach ($quotes as $quot) {
                                $quoteId[] = $quot->getId();
                            }
                            $quotes = Mage::getModel('sales/quote')->getCollection()->addFieldToFilter('is_active', 1)->addFieldToFilter('entity_id', array('in' => array($quoteId)));
                            foreach ($quotes as $quote) {
                                $items = $quote->getAllItems();
                                foreach ($items as $item) {
                                    $product = $item->getProduct();
                                    if ($product->getId() == $data["product_id"]) {
                                        $bidId = $item->getOptionByCode('bid_id');
                                        if ($bidId == null || $bidId->getValue() <= 0) {
                                            $quote->removeItem($item->getId())->save();
                                            $quote->collectTotals();
                                            $quote->save();
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId(), 'store' => $this->getRequest()->getParam('store')));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('auction')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
    }

    public function duplicateAction() {
        $id = $this->getRequest()->getParam('id');

        if (!$id) {
            $this->_redirect('*/*/index', array());
            return;
        }

        $auction = Mage::getModel('auction/productauction')->load($id);

        $auction->setId(null);
        $auction->setProductId($auction->getProductId());
        $auction->setProductName($auction->getProductName());
        $auction->setStatus(2);

        try {
            $auction->save();
            Mage::getSingleton('core/session')->addSuccess(Mage::helper('auction')->__('Auction was successfully duplicated'));
            $this->_redirect('*/*/edit', array('id' => $auction->getId()));
            return;
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
            $this->_redirect('*/*/edit', array('id' => $auction->getId()));
            return;
        }
    }

    public function cancelAction() {
        $id = $this->getRequest()->getParam('id');

        if (!$id) {
            $this->_redirect('*/*/index', array());
            return;
        }

        $auction = Mage::getModel('auction/productauction')->load($id);

        try {

            if ($auction->getStatus() <= 4) {
                $auction->setStatus(2);
                $auction->save();
                Mage::getSingleton('core/session')->addSuccess(Mage::helper('auction')->__('Auction was successfully canceled'));
            } else {
                Mage::getSingleton('core/session')->addError(Mage::helper('auction')->__('Auction can\'t canceled'));
            }


            $this->_redirect('*/*/edit', array('id' => $auction->getId()));
            return;
        } catch (Exception $e) {

            Mage::getSingleton('core/session')->addError($e->getMessage());
            $this->_redirect('*/*/edit', array('id' => $auction->getId()));
            return;
        }
    }

    public function deleteAction() {
        $auctionid = $this->getRequest()->getParam('id');
        if ($auctionid > 0) {
            try {
                // check change status from processing to disable or not start
                if (!$this->checkStatus($this->getRequest()->getParam('id'))) {
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('auction')->__('Failure: this auction has bidder'));
                    $this->_redirect('*/*/edit', array('id' => $auctionid));
                    return;
                }
                // end check change status

                $model = Mage::getModel('auction/productauction');

                $model->setId($auctionid)
                        ->delete();
                $autobids = Mage::getModel('auction/autobid')->getCollection()
                        ->addFieldToFilter('productauction_id', $auctionid);
                foreach ($autobids as $autobid) {
                    $autobid->delete();
                }
                $watchers = Mage::getModel('auction/watcher')->getCollection()
                        ->addFieldToFilter('productauction_id', $auctionid);
                foreach ($watchers as $watcher) {
                    $watcher->delete();
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $auctionid));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $failure = 0;
        $success = 0;

        $auctionIds = $this->getRequest()->getParam('productauction');
        if (!is_array($auctionIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($auctionIds as $auctionId) {
                    $auction = Mage::getModel('auction/productauction')->load($auctionId);
                    if (!$this->checkStatus($auction->getId())) {
                        $failure++;
                    } else {
                        $autobids = Mage::getModel('auction/autobid')->getCollection()
                                ->addFieldToFilter('productauction_id', $auction->getId());
                        $watchers = Mage::getModel('auction/watcher')->getCollection()
                                ->addFieldToFilter('productauction_id', $auction->getId());
                        $auction->delete();
                        foreach ($autobids as $autobid) {
                            $autobid->delete();
                        }
                        foreach ($watchers as $watcher) {
                            $watcher->delete();
                        }
                        $success++;
                    }
                }
                if ($success > 0)
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                            Mage::helper('adminhtml')->__(
                                    'Total of %d record(s) were successfully deleted', $success
                            )
                    );

                if ($failure > 0)
                    Mage::getSingleton('adminhtml/session')->addError(
                            Mage::helper('adminhtml')->__(
                                    'Total of %d record(s) were delete failure', $failure
                            )
                    );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction() {
        $failure = 0;
        $success = 0;
        $auctionIds = $this->getRequest()->getParam('productauction');
        if (!is_array($auctionIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            $status = $this->getRequest()->getParam('status');
            try {
                foreach ($auctionIds as $auctionId) {
                    $auction = Mage::getSingleton('auction/productauction')
                            ->load($auctionId)
                    ;
                    if (!$this->checkMassStatus($auction, $status)) {
                        $failure++;
                    } else {
                        if ($status == 5 && $auction->getStatus() == 4) {
                            $time = Mage::getModel('core/date')->timestamp(time());
                            $auction->setData('end_date', date('Y/m/d', $time))
                                    ->setData('end_time', date('H:i:s', $time))
                                    ->setIsMassupdate(true)
                                    ->save();
                        } else {
                            $auction->setStatus($status);
                            $auction->setIsMassupdate(true);
                            $auction->save();
                        }
                        $success++;
                    }
                }
                if ($success > 0) {
                    $this->_getSession()->addSuccess(
                            $this->__('Total of %d record(s) were successfully updated', $success)
                    );
                }
                if ($failure > 0)
                    $this->_getSession()->addError(
                            $this->__('Total of %d record(s) were updated failure', $failure)
                    );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
    
    public function massFeaturedAction(){
        $failure = 0;
        $success = 0;
        $auctionIds = $this->getRequest()->getParam('productauction');
        if (!is_array($auctionIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            $featured = $this->getRequest()->getParam('featured');
            try {
                foreach ($auctionIds as $auctionId) {
                    $auction = Mage::getSingleton('auction/productauction')
                            ->load($auctionId)
                    ;
                    if ($auction->getStatus()==5 || $auction->getStatus()==6 || $auction->getFeatured()==$featured) {
                        $failure++;
                    } else {
                        $auction->setFeatured($featured);
                        $auction->setIsMassupdate(true);
                        $auction->save();
                        $success++;
                    }
                }
                if ($success > 0) {
                    $this->_getSession()->addSuccess(
                            $this->__('Total of %d record(s) were successfully updated', $success)
                    );
                }
                if ($failure > 0)
                    $this->_getSession()->addError(
                            $this->__('Total of %d record(s) were updated failure', $failure)
                    );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function exportCsvAction() {
        $fileName = 'auction.csv';
        $content = $this->getLayout()->createBlock('auction/adminhtml_productauction_grid')
                ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction() {
        $fileName = 'auction.xml';
        $content = $this->getLayout()->createBlock('auction/adminhtml_productauction_grid')
                ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportWinnerCsvAction() {
        $fileName = 'winner.csv';
        $content = $this->getLayout()->createBlock('auction/adminhtml_productauction_edit_tab_bidwinner')
                ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportWinnerXmlAction() {
        $fileName = 'winner.xml';
        $content = $this->getLayout()->createBlock('auction/adminhtml_productauction_edit_tab_bidwinner')
                ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType = 'application/octet-stream') {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK', '');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }

    public function checkMassStatus($productauction, $status = null) {
        $check_autobid = Mage::getModel('auction/autobid')->getCollection()
                ->addFieldToFilter('productauction_id', $productauction->getId());

        $check_auction = Mage::getModel('auction/auction')->getCollection()
                ->addFieldToFilter('productauction_id', $productauction->getId());

        if ($status != null) {
            if ($productauction->getStatus() == $status) {
                return false;
            }
            if ($productauction->getStatus() == 5 || $productauction->getStatus() == 6) {
                return false;
            }
            if ($productauction->getStatus() == 4 && ($status == 1 || $status == 3)) {
                return false;
            }
            if ($productauction->getStatus() == 4 && ($status == 2 || $status == 3) && (count($check_autobid) > 0 || count($check_auction) > 0 ))
                return false;
            else
                return true;
        } else {
            if ($productauction->getStatus() == 4 && (count($check_autobid) > 0 || count($check_auction) > 0 ))
                return false;
            else
                return true;
        }
    }

    public function checkStatus($production_id, $status = null) {
        $productauction = Mage::getModel('auction/productauction')->load($production_id);

        $check_autobid = Mage::getModel('auction/autobid')->getCollection()
                ->addFieldToFilter('productauction_id', $production_id);

        $check_auction = Mage::getModel('auction/auction')->getCollection()
                ->addFieldToFilter('productauction_id', $production_id);

        if ($status != null) {
            if ($productauction->getStatus() == 4 && ($status == 2 || $status == 3) && (count($check_autobid) > 0 || count($check_auction) > 0 ))
                return false;
            else
                return true;
        } else {
            if ($productauction->getStatus() == 4 && (count($check_autobid) > 0 || count($check_auction) > 0 ))
                return false;
            else
                return true;
        }
    }

}
