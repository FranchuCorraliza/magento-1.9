<?php

class Magestore_Auction_Adminhtml_AuctionController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('auction/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}
	
    public function listbidAction()
    {
		$gridBlock = $this->getLayout()->createBlock('auction/adminhtml_auction_grid')
            ->setGridUrl($this->getUrl('*/*/gridOnly', array('_current' => true, 'id'=>$this->getRequest()->getParam('id'))));
			
        $serializerBlock = '{}';
		
        $this->_outputBlocks($gridBlock, $serializerBlock);
    }	
	
    protected function _outputBlocks()
    {
        $blocks = func_get_args();
        $output = $this->getLayout()->createBlock('adminhtml/text_list');
        foreach ($blocks as $block) {
            $output->insert($block, '', true);
        }
        $this->getResponse()->setBody($output->toHtml());
    }	
	
    public function gridOnlyAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('auction/adminhtml_auction_grid')
                ->toHtml()
        );
    }	

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('auction/auction')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('auction_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('auction/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('auction/adminhtml_auction_edit'))
				->_addLeft($this->getLayout()->createBlock('auction/adminhtml_auction_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('auction')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('auction/auction');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $auctionBidIds = $this->getRequest()->getParam('auction');
		$auction_id = '';
        if(!is_array($auctionBidIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($auctionBidIds as $auctionBidId) {
                    $auctionBid = Mage::getModel('auction/auction')->load($auctionBidId);
					
					$auction_id = $auctionBid->getProductauctionId();
					
                    $auctionBid->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($auctionBidIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/adminhtml_productauction/detail',array('id'=>$auction_id));
    }
	
    public function massStatusAction()
    {
        $auctionBidIds = $this->getRequest()->getParam('auction');
		$auction_id = '';
        if(!is_array($auctionBidIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($auctionBidIds as $auctionBidId) {
                    $auctionBid = Mage::getSingleton('auction/auction')
                        ->load($auctionBidId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($auctionBidIds))
                );
				
				$auction_id = $auctionBid->getProductauctionId();
            
			} catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/adminhtml_productauction/detail',array('id'=>$auction_id));
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'auctionbid.csv';
        $content    = $this->getLayout()->createBlock('auction/adminhtml_auction_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'auctionbid.xml';
        $content    = $this->getLayout()->createBlock('auction/adminhtml_auction_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}