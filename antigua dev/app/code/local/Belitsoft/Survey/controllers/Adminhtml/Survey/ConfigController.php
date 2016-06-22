<?php

class Belitsoft_Survey_Adminhtml_Survey_ConfigController extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction()
	{
		$this->loadLayout()
			->_setActiveMenu('cms/survey/config')
			->_title(Mage::helper('cms')->__('CMS'))
			->_title($this->__('Survey'))
			->_addBreadcrumb(Mage::helper('cms')->__('CMS'), Mage::helper('cms')->__('CMS'))
			->_addBreadcrumb($this->__('Survey'), $this->__('Survey'))
			->_addBreadcrumb($this->__('Survey Settings'), $this->__('Survey Settings'));

		return $this;
	}

	public function indexAction()
	{
		try {
			$config_data = array();
			$collection = Mage::getResourceSingleton('belitsoft_survey/config_collection')->load();
			foreach($collection as $attribute) {
				$data = $attribute->getData();
				$config_data[$data['name']] = $data['value'];
			}

			Mage::register('survey_config_data', $config_data);

			$this->_initAction()
				->_title($this->__('Survey Settings'))
				->_addContent($this->getLayout()->createBlock('belitsoft_survey/adminhtml_config'))
				->renderLayout();

		} catch(Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('adminhtml/index');
		}
	}

	public function saveAction()
	{
		foreach($this->getRequest()->getPost() as $name=>$value) {
			if(strpos($name, 'survey_') === 0) {
				$name = str_replace('survey_', '', $name);
				Mage::getModel('belitsoft_survey/config')->setConfigData($name, $value)->save();
			}
		}

		$this->_getSession()->addSuccess($this->__('The settings have been saved.'));
        $this->_redirectSuccess($this->getUrl('*/*/index'));
	}
}