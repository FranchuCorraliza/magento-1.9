<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */
class Belitsoft_Survey_Adminhtml_SurveyController extends Mage_Adminhtml_Controller_Action
{
	/**
	 * Initialization of current view - add's breadcrumps and the current menu status
	 *
	 * @return Belitsoft_Survey_AdminController
	 */
	protected function _initAction()
	{
		$this->_usedModuleName = 'belitsoft_survey';

		$this->loadLayout()
			->_setActiveMenu('cms/survey')
			->_title(Mage::helper('cms')->__('CMS'))
			->_title($this->__('Survey'))
			->_addBreadcrumb(Mage::helper('cms')->__('CMS'), Mage::helper('cms')->__('CMS'))
			->_addBreadcrumb($this->__('Survey'), $this->__('Survey'));

		return $this;
	}

	/**
	 * Displays overview grid.
	 */
	public function indexAction()
	{
		if ($this->getRequest()->getParam('ajax')) {
			$this->_forward('grid');
			return;
		}

		$this->_initAction()
			->_title($this->__('Manage Surveys'))
			->_addContent($this->getLayout()->createBlock('belitsoft_survey/adminhtml_survey'))
			->renderLayout();
	}

	public function gridAction()
	{
		$this->loadLayout();
		$this->getResponse()->setBody(
			$this->getLayout()->createBlock('belitsoft_survey/adminhtml_survey_grid')->toHtml()
		);
	}

	/**
	 * Displays the new survey form
	 */
	public function newAction()
	{
		$this->_forward('edit');
	}

	/**
	 * Displays the new survey form or the edit survey form.
	 */
	public function editAction()
	{
		$id = $this->getRequest()->getParam('survey_id');
		$survey = Mage::getModel('belitsoft_survey/survey');

		// if current id given then try to load and edit current survey
		if($id) {
			$survey->load($id);
			if(!$survey->getId()) {
				Mage::getSingleton('adminhtml/session')->addError(
					$this->__('This survey does not exist')
				);
				$this->_redirect('*/*/');
				return;
			}
		}

		$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
		if(!empty($data)) {
			$survey->setData($data);
		}

		Mage::register('survey', $survey);

		$title = $id ? $this->__('Edit Survey') : $this->__('New Survey');
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
		// check if data sent
		if($data = $this->getRequest()->getPost()) {
			// init model and set data
			$survey = Mage::getModel('belitsoft_survey/survey');
			$survey->setData($data);

/*	Commented since version 1.2.0 
			$format = Mage::app()->getLocale()->getDateTimeFormat(
				Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM
			);
			if ($this->getRequest()->getParam('expired_date')) {
				$date = Mage::app()->getLocale()->date($this->getRequest()->getParam('expired_date'), $format);
				$time = $date->getTimestamp();
				$survey->setExpiredDate(
					Mage::getModel('core/date')->gmtDate(null, $time)
				);
			} else {
				$survey->setExpiredDate(null);
			}
*/

			// try to save it
			try {
				// save the data
				$survey->save();

				// display success message
				Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Survey was successfully saved'));
				// clear previously saved data from session
				Mage::getSingleton('adminhtml/session')->setFormData(false);
				// check if 'Save and Continue'
				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('survey_id' => $survey->getId()));
					return;
				}
				// go to grid
				$this->_redirect('*/*/index');
				return;

			} catch(Exception $e) {
				// display error message
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				// save data in session
				Mage::getSingleton('adminhtml/session')->setFormData($data);
				// redirect to edit form
				$this->_redirect('*/*/edit', array('survey_id' => $this->getRequest()->getParam('survey_id')));
				return;
			}
		}

		// go to grid
		$this->_redirect('*/*/index');
	}
	
	public function duplicateAction()
	{
		// check if data sent
		if($data = $this->getRequest()->getPost()) {
			// init model and set data
			$survey = Mage::getModel('belitsoft_survey/survey');
			$survey->setData($data);
			
			$oldSurveyId = $survey->getId();
			$survey->setId(null);
			$survey->setIsActive(0);
			$survey->setSurveyUrlKey('');

			// try to save it
			try {
				// save the data
				$survey->save();
				
				Mage::getModel('belitsoft_survey/question')->duplicateSurveyQuestions($oldSurveyId, $survey->getId());

				// display success message
				Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The survey has been duplicated.'));
				// clear previously saved data from session
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				$this->_redirect('*/*/edit', array('survey_id' => $survey->getId()));
				return;

			} catch(Exception $e) {
				// display error message
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				// save data in session
				Mage::getSingleton('adminhtml/session')->setFormData($data);
				// redirect to edit form
				$this->_redirect('*/*/edit', array('survey_id' => $this->getRequest()->getParam('survey_id')));
				return;
			}
		}

		// go to grid
		$this->_redirect('*/*/index');
	}
	

	/**
	 * Action that does the actual delete process and redirects back to overview
	 */
	public function deleteAction()
	{
		// check if we know what should be deleted
		if($id = $this->getRequest()->getParam('survey_id')) {
			try {
				// init model and delete
				$survey = Mage::getModel('belitsoft_survey/survey');
				$survey->load($id);
				$survey->delete();

				// display success message
				Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Survey was successfully deleted'));

				// go to grid
				$this->_redirect('*/*/index');
				return;

			} catch (Exception $e) {
				// display error message
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				// go back to edit form
				$this->_redirect('*/*/edit', array('survey_id' => $id));
				return;
			}
		}

		// display error message
		Mage::getSingleton('adminhtml/session')->addError($this->__('Unable to find a Survey to delete'));

		// go to grid
		$this->_redirect('*/*/index');
	}

	public function massDeleteAction()
	{
		$ids = $this->getRequest()->getParam('surveytable');
		if (!is_array($ids)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('catalog')->__('Please select items.'));
		} else {
			try {
				foreach ($ids as $id) {
					$item = Mage::getModel('belitsoft_survey/survey')->load($id);
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
	 * @return boolean True if user is allowed to edit survey
	 */
	protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('cms/survey/survey');
    }
}
