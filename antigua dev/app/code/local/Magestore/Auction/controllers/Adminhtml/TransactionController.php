<?php

class Magestore_Auction_Adminhtml_TransactionController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('auction/transaction')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Transaction-Auction Manager'), Mage::helper('adminhtml')->__('Transaction-Auction Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}
	
	public function viewtransactionAction() {
		
		$this->_initAction();
		$this->renderLayout();
		
	}

	public function exportCsvAction()
    {
        $fileName   = 'transaction.csv';
        $content    = $this->getLayout()->createBlock('auction/adminhtml_transaction_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'transaction.xml';
        $content    = $this->getLayout()->createBlock('auction/adminhtml_transaction_grid')
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