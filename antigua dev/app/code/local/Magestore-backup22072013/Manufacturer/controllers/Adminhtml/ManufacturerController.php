<?php

class Magestore_Manufacturer_Adminhtml_ManufacturerController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		Mage::helper('manufacturer')->autoUpdateManufacturerFormCatalog();
		$this->loadLayout()
			->_setActiveMenu('manufacturer/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Manufacturer Manager'), Mage::helper('adminhtml')->__('Manufacturer Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {

		$id     = $this->getRequest()->getParam('id');
		$storeID =  $this->getRequest()->getParam('store');
		if(isset($_SESSION['old_store_id']))
			$old_store_id = $_SESSION['old_store_id'];
		else
			$old_store_id = 0;
		
		if($old_store_id ==0 )
			$model = Mage::getResourceModel('manufacturer/manufacturer')->getStoreManufacturer($id,$storeID);
		else
			$model = Mage::getResourceModel('manufacturer/manufacturer')->getStoreManufacturer($id,$old_store_id);			
		$this->getRequest()->setParam('id',$model->getId());
		$this->getRequest()->setParam('store',$storeID);
		$_SESSION['old_store_id'] = 0;
	
		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('manufacturer_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('manufacturer/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('manufacturer/adminhtml_manufacturer_edit'))
				->_addLeft($this->getLayout()->createBlock('manufacturer/adminhtml_manufacturer_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('manufacturer')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('manufacturer')->__('Not add'));
		$this->_redirect('*/*/');		
	}
 
	public function saveAction() {

		if ($data = $this->getRequest()->getPost()) {
			
			if(isset($data['image']['delete']))
			{
				Mage::helper('manufacturer')->deleteImageFile($data['name'],$data['old_image']);
				unset($data['old_image']);
			}
			
			$data['image'] = "";
			
			if(isset($_FILES['image']))
				$data['image'] = Mage::helper('manufacturer')->uploadManufacturerImage($data['name'],$_FILES['image']);
			
			if(!$data['image'] && isset($data['old_image']))
			{
				$data['image'] = $data['old_image'];
			}
	  				  			
			$model = Mage::getModel('manufacturer/manufacturer');	
			if($this->getRequest()->getParam('id'))
			{
				$model->load($this->getRequest()->getParam('id'));
				$data['store_id'] = $model->getData('store_id');
				$data['name_store'] = $model->getData('name_store');
				if($data['default_page_title'] && $data['store_id'])
					$data['page_title'] = $model->getData('page_title');
	
				$_SESSION['old_store_id']= $data['store_id'];
				
			}	
			
			if(isset($data['url_key']))
				$data['url_key'] = Mage::helper('manufacturer')->refineUrlKey($data['url_key']);
			
			
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}	
				
				$model->save();
				
				//update url_key for only Admin Manufacturer
				if($this->getRequest()->getParam('id') AND ($data['store_id']==0))
					$model->updateUrlKey();
				//update catalog
				if(! $this->getRequest()->getParam('id'))
					Mage::helper('manufacturer')->updateDataManufacturerStoresFormAdminStore($model);
				else
					Mage::helper('manufacturer')->updateManufacturerToCatalog($model);	
					
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('manufacturer')->__('Item was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('manufacturer')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Not delete !'));
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $manufacturerIds = $this->getRequest()->getParam('manufacturer');
        if(!is_array($manufacturerIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($manufacturerIds as $manufacturerId) {
                    $manufacturer = Mage::getModel('manufacturer/manufacturer')->load($manufacturerId);
                    $manufacturer->deleteStore();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($manufacturerIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        $manufacturerIds = $this->getRequest()->getParam('manufacturer');
        if(!is_array($manufacturerIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($manufacturerIds as $manufacturerId) {
                    $manufacturer = Mage::getSingleton('manufacturer/manufacturer')
                        ->load($manufacturerId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($manufacturerIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'manufacturer.csv';
        $content    = $this->getLayout()->createBlock('manufacturer/adminhtml_manufacturer_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'manufacturer.xml';
        $content    = $this->getLayout()->createBlock('manufacturer/adminhtml_manufacturer_grid')
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