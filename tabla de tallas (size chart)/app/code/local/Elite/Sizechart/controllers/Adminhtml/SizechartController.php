<?php
class Elite_Sizechart_Adminhtml_SizechardController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
        ->_setActiveMenu('sizechard/items')
        ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
        return $this;
    }
    public function indexAction() {
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('sizechard/adminhtml_sizechard'));
        $this->renderLayout();
    }
    public function editAction()
    {
        $sizechardId = $this->getRequest()->getParam('id');
        $sizechardModel = Mage::getModel('sizechard/sizechard')->load($sizechardId);
        if ($sizechardModel->getId() || $sizechardId == 0) {
            Mage::register('sizechard_data', $sizechardModel);
            $this->loadLayout();
            $this->_setActiveMenu('sizechard/items');
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('sizechard/adminhtml_sizechard_edit'))
            ->_addLeft($this->getLayout()->createBlock('sizechard/adminhtml_sizechard_edit_tabs'));
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('sizechard')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }
    public function newAction()
    {
        $this->_forward('edit');
    }
    public function saveAction()
    {
    if ( $this->getRequest()->getPost() ) {
        try {
            $postData = $this->getRequest()->getPost();
            $sizechardModel = Mage::getModel('sizechard/sizechard');
            $sizechardModel->setId($this->getRequest()->getParam('id'))
                ->setTallaje($postData['tallaje'])
                ->setIdequivalente($postData['idequivalente'])
                ->setTalla($postData['talla'])
                ->setCategoria($postData['categoria'])
                ->setStatus($postData['status'])
                ->save();
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully saved'));
            Mage::getSingleton('adminhtml/session')->setsizechardData(false);
            $this->_redirect('*/*/');
            return;
        } catch (Exception $e) {
        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        Mage::getSingleton('adminhtml/session')->setsizechardData($this->getRequest()->getPost());
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
                $sizechardModel = Mage::getModel('sizechard/sizechard');
                $sizechardModel->setId($this->getRequest()->getParam('id'))
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
            $this->getLayout()->createBlock('importedit/adminhtml_sizechard_grid')->toHtml()
        );
    }
}