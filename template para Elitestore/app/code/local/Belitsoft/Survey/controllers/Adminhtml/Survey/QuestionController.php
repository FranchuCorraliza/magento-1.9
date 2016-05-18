<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */

class Belitsoft_Survey_Adminhtml_Survey_QuestionController extends Mage_Adminhtml_Controller_Action
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
			->_setActiveMenu('cms/survey/question')
			->_title(Mage::helper('cms')->__('CMS'))
			->_title($this->__('Survey'))
			->_addBreadcrumb(Mage::helper('cms')->__('CMS'), Mage::helper('cms')->__('CMS'))
			->_addBreadcrumb($this->__('Survey'), $this->__('Survey'));

		return $this;
	}

	/**
	 * Displays the question overview grid.
	 *
	 */
	public function indexAction()
	{
		if ($this->getRequest()->getParam('ajax')) {
			$this->_forward('grid');
			return;
		}

		$this->_initAction()
			->_title($this->__('Manage Questions'))
			->_addContent($this->getLayout()->createBlock('belitsoft_survey/adminhtml_question'))
			->renderLayout();
	}

	public function gridAction()
	{
		$this->loadLayout();
		$this->getResponse()->setBody(
			$this->getLayout()->createBlock('belitsoft_survey/adminhtml_question_grid')->toHtml()
		);
	}

	/**
	 * Displays the new question form
	 */
	public function newAction()
	{
		$this->_forward('edit');
	}

	/**
	 * Displays the new question form or the edit question form.
	 */
	public function editAction()
	{
		$id = $this->getRequest()->getParam('question_id');
		$model = Mage::getModel('belitsoft_survey/question');

		if($id) {
			$model->load($id);
			if (!$model->getId()) {
				$this->_getSession()->addError($this->__('This question does not exist'));
				$this->_redirect('*/*/index');
				return;
			}
		}

		$data = $this->_getSession()->getFormData(true);
		if(!empty($data)) {
			$model->setData($data);
		}

		Mage::register('survey_question', $model);

		$title = $id ? $this->__('Edit Question') : $this->__('New Question');
		$this->_initAction()
			->_title($title)
			->_addBreadcrumb($title, $title)
			->renderLayout();
	}

	/**
	 * Action that does the actual saving process and redirects back to overview
	 */
	public function saveAction()
	{
		if($data = $this->getRequest()->getPost()) {
			$model = Mage::getModel('belitsoft_survey/question');
			$model->setData($data);
			try {
				$model->save();

				$this->_getSession()->addSuccess($this->__('Question was successfully saved'));
				$this->_getSession()->setFormData(false);
				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array ('question_id' => $model->getId()));
					return;
				}
			} catch (Exception $e) {
				$this->_getSession()->addException($e, $e->getMessage());
				$this->_getSession()->setFormData($data);
				$this->_redirect('*/*/edit', array ('question_id' => $this->getRequest()->getParam('question_id')));
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
		if($id = $this->getRequest()->getParam('question_id')) {
			try {
				$model = Mage::getModel('belitsoft_survey/question');
				$model->load($id);
				$model->delete();

				$this->_getSession()->addSuccess($this->__('Question was successfully deleted'));
				$this->_redirect('*/*/index');
				return;

			} catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
				$this->_redirect('*/*/edit', array ('question_id' => $id));
				return;
			}
		}

		$this->_getSession()->addError($this->__('Unable to find a Question to delete'));

		$this->_redirect('*/*/index');
	}

	public function massDeleteAction()
	{
		$ids = $this->getRequest()->getParam('questiontable');
		if (!is_array($ids)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('catalog')->__('Please select items.'));
		} else {
			try {
				foreach ($ids as $id) {
					$item = Mage::getModel('belitsoft_survey/question')->load($id);
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
	 * Ajax action for get question field options
	 */
	public function loadFieldAction()
	{
		try {
			$block = $this->getLayout()
				->createBlock(
					'belitsoft_survey/adminhtml_question_edit_fields',
					'',
					array(
						'question_type' => $this->getRequest()->getParam('question_type')
					)
				);

			$this->getResponse()->setBody($block->toHtml());
		} catch (Exception $e) {
			// just need to output text with error
			$this->_getSession()->addError($e->getMessage());
		}
	}


	/**
	 * Simple access control
	 *
	 * @return boolean True if user is allowed to edit questions
	 */
	protected function _isAllowed()
	{
		return Mage::getSingleton('admin/session')->isAllowed('cms/survey/question');
	}
}
