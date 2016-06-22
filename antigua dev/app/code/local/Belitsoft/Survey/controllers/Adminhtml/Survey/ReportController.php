<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */

class Belitsoft_Survey_Adminhtml_Survey_ReportController extends Mage_Adminhtml_Controller_Action
{
	const CSV_COLUMNS_DELIMETER = ',';
	const CSV_ROWS_DELIMETER = "\r\n";

	/**
	 * Initialization of current view - add's breadcrumps and the current menu status
	 *
	 * @return Belitsoft_Survey_Adminhtml
	 */
	protected function _initAction()
	{
		$this->_usedModuleName = 'belitsoft_survey';

		$this->loadLayout()
			->_setActiveMenu('report/survey')
			->_title(Mage::helper('reports')->__('Reports'))
			->_title($this->__('Survey'))
			->_addBreadcrumb(Mage::helper('reports')->__('Reports'), Mage::helper('reports')->__('Reports'))
			->_addBreadcrumb($this->__('Survey'), $this->__('Survey'));

		return $this;
	}

	/**
	 * Displays the question overview grid.
	 *
	 */
	public function indexAction()
	{
		$this->_initAction()
			->_addContent($this->getLayout()->createBlock('belitsoft_survey/adminhtml_report'))
			->renderLayout();
	}

	/**
	 * Displays the result form with graphics
	 */
	public function resultsAction()
	{
		$survey_id = (int)$this->getRequest()->getParam('survey_id');
		$isPdf = $this->getRequest()->getBeforeForwardInfo('action_name');

		$survey = Mage::getModel('belitsoft_survey/survey')->load($survey_id);
		if (!$survey->getId()) {
			$this->_getSession()->addError($this->__("Please apply 'Survey Name' filter"));
			$this->_redirect('*/*/');
			return;
		}
		Mage::register('survey_survey', $survey);

		$questions = Mage::getResourceModel('belitsoft_survey/question_collection')
			->addIsActiveFilter()
			->addSurveyIdFilter($survey)
			->setOrder('sort_order', 'ASC')
			->getItems();
		Mage::register('survey_questions', $questions);

		/* @var $graphic Belitsoft_Survey_Model_Graphic */
		try {
			$graphic = Mage::getModel('belitsoft_survey/graphic');
			$graphic->clearOldImages();
		} catch (Exception $e) {
			$this->_getSession()->addError($e->getMessage());
			$this->_redirect('*/*/');
			return;
		}
		Mage::register('survey_graphics', $graphic);

		$rows = array();
		foreach ($questions as $question) {
			try {
				$graphic_for_question = Mage::getModel('belitsoft_survey/graphic_' . $question->getQuestionType());
			} catch (Exception $e) {
				continue;
			}

			if (!$graphic_for_question) {
				continue;
			}

			$is_image = null;
			if ($quest_data = $graphic_for_question->getImage($survey, $question, 1)) {
				$is_image = true;
			} else if ($quest_data = $graphic_for_question->getText($survey, $question)) {
				$is_image = false;
			} else {
				continue;
			}

			$tmp = new stdClass();
			$tmp->title = $question->getQuestionText();
			$tmp->is_image = $is_image;
			if ($is_image === true) {
				if (is_array($quest_data)) {
					foreach ($quest_data as $imgsrc) {
						$tmp2 = clone($tmp);
						$tmp2->data = $imgsrc;
						$rows[] = $tmp2;
					}
				} elseif ($quest_data) {
					$tmp->data = $quest_data;
					$rows[] = $tmp;
				}
			} else {
				$tmp->data = $quest_data;
				$rows[] = $tmp;
			}
		}

		Mage::register('survey_results_graphics', $rows);

		if (!$isPdf) {
			$title = $this->__('Survey Results');

			$this->_initAction()
				->_title($title)
				->_addBreadcrumb($title, $title)
				->_addContent($this->getLayout()->createBlock('belitsoft_survey/adminhtml_report_results'))
				->renderLayout();

		} else {
			$options = array();
			$options['pdf-name'] = (string)Mage::getStoreConfig('general/store_information/name', Mage::app()->getStore());
			$options['site-url'] = (string)Mage::getStoreConfig('web/unsecure/base_url', Mage::app()->getStore());
			$pdf = Mage::getModel('belitsoft_survey/pdf', $options);
			$pdf->makeResults();
			$pdf->getPDF();
		}
	}

	/**
	 * Display a report
	 */
	public function reportAction()
	{
		$start_id = (int)$this->getRequest()->getParam('start_id');
		$isPdf = $this->getRequest()->getBeforeForwardInfo('action_name');

		$answers = Mage::getResourceModel('belitsoft_survey/answer_collection')
			->addStartIdFilter($start_id)
			->setOrder('answer_id', 'ASC')
			->getItems();

		reset($answers);
		if (empty($answers) || !($first_answer = current($answers)) || !($first_answer instanceof Belitsoft_Survey_Model_Answer) || !$first_answer->getSurveyId()) {
			$this->_getSession()->addError($this->__("Select answers for report"));
			$this->_redirect('*/*/');
			return;
		}
		Mage::register('survey_answers', $answers);
		Mage::register('survey_first_answer', $first_answer);

		$question_answers = array();
		foreach ($answers as $answer) {
			$question_answers[$answer->getQuestionId()][] = $answer;
		}

		$survey = Mage::getModel('belitsoft_survey/survey')->load($first_answer->getSurveyId());
		if (!$survey->getId()) {
			$this->_getSession()->addError($this->__("Please apply 'Survey Name' filter"));
			$this->_redirect('*/*/');
			return;
		}
		Mage::register('survey_survey', $survey);

		$questions = Mage::getResourceModel('belitsoft_survey/question_collection')
			->addIsActiveFilter()
			->addSurveyIdFilter($survey)
			->setOrder('sort_order', 'ASC')
			->getItems();

		foreach ($questions as $key => $question) {
			if (array_key_exists($question->getId(), $question_answers)) {
				$questions[$key]->setData('answers', $question_answers[$question->getId()]);
			}
		}

		Mage::register('survey_questions', $questions);

		if (!$isPdf) {
			$title = $this->__('Survey Report');

			$this->_initAction()
				->_title($title)
				->_addBreadcrumb($title, $title)
				->_addContent($this->getLayout()->createBlock('belitsoft_survey/adminhtml_report_report'))
				->renderLayout();

		} else {
			$options = array();
			$options['pdf-name'] = (string)Mage::getStoreConfig('general/store_information/name', Mage::app()->getStore());
			$options['site-url'] = (string)Mage::getStoreConfig('web/unsecure/base_url', Mage::app()->getStore());
			$pdf = Mage::getModel('belitsoft_survey/pdf', $options);
			$pdf->makeReport();
			$pdf->getPDF();
		}
	}

	public function csvResultsAction()
	{
		$survey_id = (int)$this->getRequest()->getParam('survey_id');

		$survey = Mage::getModel('belitsoft_survey/survey')->load($survey_id);
		if (!$survey->getId()) {
			$this->_getSession()->addError($this->__("Please apply 'Survey Name' filter"));
			$this->_redirect('*/*/');
			return;
		}

		$questions = Mage::getResourceModel('belitsoft_survey/question_collection')
			->addIsActiveFilter()
			->addSurveyIdFilter($survey)
			->setOrder('sort_order', 'ASC')
			->getItems();
		$all = array();
		foreach ($questions as $question) {
			$questionType = $question->getQuestionType();
			if (!in_array($questionType, array('pickone', 'pickmany', 'ranking'))) {
				continue;
			}

			$answer_model = Mage::getModel('belitsoft_survey/answer');
			$maxval = $answer_model->getCountStartId(0, $question->getQuestionId());
			if ($questionType == 'pickone' || $questionType == 'pickmany') {
				$answers = $answer_model->getAllAnswers($question->getSurveyId(), $question->getQuestionId());
				$fields = Mage::getResourceModel('belitsoft_survey/field_collection')
					->addQuestionFilter($question)
					->setOrder('sort_order', 'ASC')
					->getItems();

				$rows = array();
				$results = array_count_values($answers);
				foreach ($fields as $field) {
					$columns = array();
					$columns[] = trim(strip_tags($question->getQuestionText()));
					$columns[] = trim(strip_tags($field->getFieldText()));
					/*$columns[] = (isset($results[$field->getId()]) ? intval((100 * $results[$field->getId()]) / $maxval) : 0); /*percents*/
					$columns[] = (isset($results[$field->getId()]) ? intval($results[$field->getId()]) : 0); /*number*/

					$rows[] = implode(self::CSV_COLUMNS_DELIMETER, $columns);
				}
			} else {
				$rankingQuestions = Mage::getResourceModel('belitsoft_survey/field_collection')
					->addQuestionFilter($question)
					->addFieldRankFilter(1)
					->setOrder('sort_order', 'ASC')
					->getItems();

				$fields = Mage::getResourceModel('belitsoft_survey/field_collection')
					->addQuestionFilter($question)
					->addFieldRankFilter(0)
					->setOrder('sort_order', 'ASC')
					->getItems();

				$rows = array();
				foreach ($rankingQuestions as $ranking) {
					$columns = null;

					$answer_field = $answer_model->getAllAnswers($question->getSurveyId(), $question->getQuestionId(), $ranking->getFieldId());
					$j = count($answer_field);
					for ($ii = $j; $ii < $maxval; $ii++) {
						$answer_field[] = 0;
					}

					$results = array_count_values($answer_field);
					foreach ($fields as $field) {
						$columns = array();
						$columns[] = trim(strip_tags($question->getQuestionText()));
						$columns[] = trim(strip_tags($ranking->getFieldText())) . ' - ' . trim(strip_tags($field->getFieldText()));
						/*$columns[] = (isset($results[$field->getId()])? intval((100*$results[$field->getId()])/$maxval): 0); /*percents*/
						$columns[] = (isset($results[$field->getId()]) ? intval($results[$field->getId()]) : 0); /*number*/

						$rows[] = implode(self::CSV_COLUMNS_DELIMETER, $columns);
					}
				}
			}

			$all[] = implode(self::CSV_ROWS_DELIMETER, $rows);
		}

		$content = implode(self::CSV_ROWS_DELIMETER, $all);

		@ob_end_clean();
		header("Content-type: text/csv");
		header("Content-Length: " . strlen(ltrim($content)));
		header("Content-Disposition: attachment; filename=" . $survey->getSurveyName() . ".csv");
		echo $content;
		exit;
	}

	/**
	 * Get report PDF
	 */
	public function pdfAction()
	{
		$start_id = (int)$this->getRequest()->getParam('start_id');
		$survey_id = (int)$this->getRequest()->getParam('survey_id');

		if ($start_id) {
			$this->_forward('report');
		} else if ($survey_id) {
			$this->_forward('results');
		} else {
			$this->_forward('noRoute');
		}
	}

	/**
	 * Action that does the actual delete process and redirects back to overview
	 */
	public function deleteAction()
	{
		if ($start_id = $this->getRequest()->getParam('start_id')) {
			try {
				$model = Mage::getModel('belitsoft_survey/answer');
				$model->deleteAnswers($start_id);

				$this->_getSession()->addSuccess($this->__('Report was successfully deleted'));
				$this->_redirect('*/*/');
				return;

			} catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
				$this->_redirect('*/*/report', array('start_id' => $start_id));
				return;
			}
		}

		$this->_getSession()->addError($this->__('Unable to find a Answer to delete'));

		$this->_redirect('*/*/');
	}

	/**
	 * Simple access control
	 *
	 * @return boolean True if user is allowed to edit questions
	 */
	protected function _isAllowed()
	{
		return Mage::getSingleton('admin/session')->isAllowed('cms/survey/report');
	}
}
