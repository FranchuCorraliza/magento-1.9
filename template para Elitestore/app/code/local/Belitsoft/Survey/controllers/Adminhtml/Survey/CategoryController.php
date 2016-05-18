<?php

/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */
class Belitsoft_Survey_Adminhtml_Survey_CategoryController extends Mage_Adminhtml_Controller_Action
{
	/**
	 * Initialization of current view - add's breadcrumps and the current menu status
	 *
	 * @return Belitsoft_Survey_Adminhtml
	 */
	protected function _initAction()
	{
		$this->_usedModuleName = 'belitsoft_survey';

		$this->loadLayout()
			->_setActiveMenu('cms/survey/category')
			->_title(Mage::helper('cms')->__('CMS'))
			->_title($this->__('Survey'))
			->_addBreadcrumb(Mage::helper('cms')->__('CMS'), Mage::helper('cms')->__('CMS'))
			->_addBreadcrumb($this->__('Survey'), $this->__('Survey'));

		return $this;
	}

	/**
	 * Displays the categories overview grid.
	 *
	 */
	public function indexAction()
	{
		if ($this->getRequest()->getParam('ajax')) {
			$this->_forward('grid');
			return;
		}

		$this->_initAction()
			->_title($this->__('Manage Categories'))
			->_addContent($this->getLayout()->createBlock('belitsoft_survey/adminhtml_category'))
			->renderLayout();
	}

	public function gridAction()
	{
		$this->loadLayout();
		$this->getResponse()->setBody(
			$this->getLayout()->createBlock('belitsoft_survey/adminhtml_category_grid')->toHtml()
		);
	}

	/**
	 * Displays the new category form
	 */
	public function newAction()
	{
		$this->_forward('edit');
	}

	/**
	 * Displays the new category form or the edit category form.
	 */
	public function editAction()
	{
		$id    = $this->getRequest()->getParam('category_id');
		$model = Mage::getModel('belitsoft_survey/category');

		if ($id) {
			$model->load($id);
			if (!$model->getId()) {
				$this->_getSession()->addError($this->__('This category does not exist'));
				$this->_redirect('*/*/');
				return;
			}
		}

		$data = $this->_getSession()->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register('survey_category', $model);

		$title = $id ? $this->__('Edit Category') : $this->__('New Category');
		$this->_initAction()
			->_title($title)
			->_addBreadcrumb($title, $title)
//			->_addContent($this->getLayout()->createBlock(''))
			->renderLayout();
	}

	/**
	 * Action that does the actual saving process and redirects back to overview
	 */
	public function saveAction()
	{
		if ($data = $this->getRequest()->getPost()) {
			$model = Mage::getModel('belitsoft_survey/category');
			$model->setData($data);

			try {
				$model->save();

				$this->_getSession()->addSuccess($this->__('Category was successfully saved'));
				$this->_getSession()->setFormData(false);
				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('category_id' => $model->getId()));
					return;
				}
			} catch (Exception $e) {
				$this->_getSession()->addException($e, $e->getMessage());
				$this->_getSession()->setFormData($data);
				$this->_redirect('*/*/edit', array('category_id' => $this->getRequest()->getParam('category_id')));
				return;
			}
		}

		$this->_redirect('*/*/index');
	}

	/**
	 * Action that does the actual delete process and redirects back to overview
	 */
	public function deleteAction()
	{
		if ($id = $this->getRequest()->getParam('category_id')) {
			try {
				$model = Mage::getModel('belitsoft_survey/category');
				$model->load($id);
				$model->delete();

				$this->_getSession()->addSuccess($this->__('Category was successfully deleted'));
				$this->_redirect('*/*/');
				return;

			} catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('category_id' => $id));
				return;
			}
		}

		$this->_getSession()->addError($this->__('Unable to find a Category to delete'));

		$this->_redirect('*/*/index');
	}

	public function massDeleteAction()
	{
		$ids = $this->getRequest()->getParam('categorytable');
		if (!is_array($ids)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('catalog')->__('Please select items.'));
		} else {
			try {
				foreach ($ids as $id) {
					$item = Mage::getModel('belitsoft_survey/category')->load($id);
					$item->delete();
				}

				Mage::getSingleton('adminhtml/session')->addSuccess(
					Mage::helper('adminhtml')->__('Total of %d record(s) were deleted', count($ids))
				);
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}

		$this->_redirect('*/*/index');
	}

	/**
	 * Simple access control
	 *
	 * @return boolean True if user is allowed to edit categories
	 */
	protected function _isAllowed()
	{
		return Mage::getSingleton('admin/session')->isAllowed('cms/survey/category');
	}
}