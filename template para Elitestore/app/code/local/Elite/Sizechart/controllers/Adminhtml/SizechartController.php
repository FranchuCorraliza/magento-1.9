<?php
class Elite_Sizechart_Adminhtml_SizechartController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
        ->_setActiveMenu('sizechart/items')
        ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
        return $this;
    }
    public function indexAction() {
        $this->_initAction();
        $this->renderLayout();
    }
    public function editAction()
    {
		
        $sizechartId = $this->getRequest()->getParam('id');
		$sizechartModel = Mage::getModel('sizechart/sizechart')->load($sizechartId);
        
		if ($sizechartModel->getId()) {
			Mage::register('sizechart_data', $sizechartModel);
            $this->loadLayout();
            $this->_setActiveMenu('sizechart/items');
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('sizechart/adminhtml_sizechart_edit'));
            $this->_addLeft($this->getLayout()->createBlock('sizechart/adminhtml_sizechart_edit_tabs'));
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('sizechart')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
		
    }
    public function newAction()
    {
        
        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("sizechart/sizechart")->load($id);
		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}
		Mage::register("sizechart_data", $model);
		$this->loadLayout();
		$this->_setActiveMenu("sizechart/items");
		$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));
        $this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
		$this->_addContent($this->getLayout()->createBlock("sizechart/adminhtml_sizechart_edit"));
		$this->_addLeft($this->getLayout()->createBlock("sizechart/adminhtml_sizechart_edit_tabs"));
		$this->renderLayout();
    }
    public function saveAction()
    {
	Mage::log('Iyo que dise cabesa?',null,'EliteSizechart.log');
    if ( $this->getRequest()->getPost() ) {
        try {
            $postData = $this->getRequest()->getPost();
            $sizechartModel = Mage::getModel('sizechart/sizechart');
            $sizechartModel->setId($this->getRequest()->getParam('id'))
                ->setTallaje($postData['tallaje'])
                ->setIdequivalente($postData['idequivalente'])
                ->setTalla($postData['talla'])
                ->setCategoria($postData['categoria'])
                ->setStatus($postData['status'])
                ->save();
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully saved'));
            Mage::getSingleton('adminhtml/session')->setsizechartData(false);
            $this->_redirect('*/*/');
            return;
        } catch (Exception $e) {
        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        Mage::getSingleton('adminhtml/session')->setsizechartData($this->getRequest()->getPost());
        $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
        return;
        }
        }
        $this->_redirect('*/*/');
    }
    public function deleteAction()
    {
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $sizechartModel = Mage::getModel('sizechart/sizechart');
                $sizechartModel->setId($this->getRequest()->getParam('id'))
                ->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } 
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }
    /**
    * Product grid for AJAX request.
    * Sort and filter result for example.
    */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('importedit/adminhtml_sizechart_grid')->toHtml()
        );
    }
}