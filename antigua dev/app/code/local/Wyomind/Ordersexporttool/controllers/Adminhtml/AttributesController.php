<?php

class Wyomind_Ordersexporttool_Adminhtml_AttributesController extends Mage_Adminhtml_Controller_Action {

    protected function _initAction() {

        $this->loadLayout()
                ->_setActiveMenu('sales/ordersexporttool')
                ->_addBreadcrumb($this->__('Orders Export Tool'), ('Orders Export Tool'));

        return $this;
    }

    public function indexAction() {
        $this->_initAction()->renderLayout();
    }

    public function newAction() {
        $this->_forward('edit');
    }
    public function editAction() {
       
    
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('ordersexporttool/attributes')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('ordersexporttool_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('sales/ordersexporttool')->_addBreadcrumb(Mage::helper('ordersexporttool')->__('Orders Export Tool'), ('Orders Export Tool'));
            $this->_addBreadcrumb(Mage::helper('ordersexporttool')->__('Orders Export Tool'), ('Orders Export Tool'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('ordersexporttool/adminhtml_attributes_edit'));
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ordersexporttool')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }
     public function saveAction() {
      
        // check if data sent
        if ($data = $this->getRequest()->getPost()) {

            // init model and set data
            $model = Mage::getModel('ordersexporttool/attributes');

            if ($this->getRequest()->getParam('attribute_id')) {
                $model->load($this->getRequest()->getParam('attribute_id'));
            }
            $model->setData($data);
            // try to save it
            try {

                // save the data
                $model->save();

                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ordersexporttool')->__('The custom attribute has been saved.'));
                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('continue')) {
                    $this->getRequest()->setParam('id', $model->getAttributeId());
                    $this->_forward('edit');
                    return;
                }

                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {

                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // save data in session
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                // redirect to edit form
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }
    
    public function deleteAction() {
      
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model = Mage::getModel('ordersexporttool/attributes');
                $model->setId($id);
                $model->load($id);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ordersexporttool')->__('The custom attribute has been deleted.'));
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                 $this->_redirect('*/*/');
                return;
            }
        }
        // display error message
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ordersexporttool')->__('Unable to find the custom attribute to delete.'));
        // go to grid
        $this->_redirect('*/*/');
    }


}

