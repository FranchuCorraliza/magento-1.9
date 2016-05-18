<?php

/**
 * Mageplace Survey
 *
 * @category    Belitsoft
 * @package        Belitsoft_Survey
 * @copyright    Copyright (c) 2012 Mageplace. (http://www.mageplace.com)
 * @license        http://www.mageplace.com/disclaimer.html
 */
class Belitsoft_Survey_SurveyController extends Mage_Core_Controller_Front_Action
{
    protected $_category;
    protected $_survey;
    protected $_questions;
    protected $_sid;
    protected $_aid;

    /**
     * Displays the current survey details view
     */
    public function viewAction()
    {
        $aid = (int)$this->getRequest()->getParam('aid', false);
        if ($aid > 0 && !Mage::getModel('belitsoft_survey/answer')->isOwner($aid)) {
            $this->_forward('noRoute');
            return;
        }

        if ($aid > 0 && !Mage::registry('survey_finished')) {
            Mage::helper('belitsoft_survey')->setViewMode();
            $this->_forward('edit');
            return;
        } else if ($aid > 0 && Mage::registry('survey_finished')) {
            Mage::helper('belitsoft_survey')->setViewMode();
        }

        Mage::getSingleton('belitsoft_survey/session')->clear();

        if ($this->_initSurvey() && $this->_initQuestions()) {
            if (!Mage::registry('survey_finished')) {
                $this->_checkRestricted();
            }

            $this->loadLayout()->renderLayout();
        } else {
            $this->_forward('noRoute');
        }
    }

    /**
     * Displays the current survey details view
     */
    public function editAction()
    {
        $aid         = (int)$this->getRequest()->getParam('aid', false);
        $customer_id = Mage::getSingleton('customer/session')->getCustomerId();

        if (($aid < 1) || !$customer_id) {
            $this->_forward('noRoute');
            return;
        }

        if (!Mage::getModel('belitsoft_survey/answer')->isOwner($aid, $customer_id)) {
            $this->_forward('noRoute');
            return;
        }

        $answers = Mage::getModel('belitsoft_survey/answer')
            ->getCollection()
            ->joinQuestionTable('sort_order AS question_sort_order')
            ->addStartIdFilter($aid)
            ->addCustomerFilter()
            ->setOrder('question_sort_order', 'ASC')
            ->setOrder('question_id', 'ASC')
            ->setOrder('answer_id', 'ASC')
            ->getItems();

        if (empty($answers) || !($answer = end($answers)) || !($answer instanceof Belitsoft_Survey_Model_Answer)) {
            $this->_forward('noRoute');
            return;
        }

        $sid = $answer->getSurveyId();
        $this->getRequest()->setParam('sid', $sid);

        Mage::getSingleton('belitsoft_survey/session')->clear();

        if (!Mage::helper('belitsoft_survey')->isViewMode()) {
            Mage::helper('belitsoft_survey')->setEditMode();
        }

        if ($this->_initSurvey() && $this->_initQuestions()) {
            $customer_answers = $this->_prepareAnswers($answers);
            $this->_initSession($aid, $customer_answers);
            if ($this->_survey->getMultipage()) {
                $this->_redirectUrl(Mage::helper('belitsoft_survey')->getSurveyUrl($sid, 'question', $sid));
            } else {
                $this->loadLayout()->renderLayout();
            }
        } else {
            $this->_forward('noRoute');
        }
    }

    /**
     * Save user answers
     */
    public function finishAction()
    {
        Mage::unregister('survey_finished');
        Mage::register('survey_finished', true);

        $post = $this->getRequest()->getPost();
#		Mage::log(__METHOD__.': next survey question data:'.print_r($post, true));

        $questions = $this->_getAnswers($post);
        if ($questions) {
            $mode     = Mage::getSingleton('belitsoft_survey/session')->getMode();
            $start_id = Mage::getSingleton('belitsoft_survey/session')->getStartId();
            $this->_saveAnswers($questions, $start_id, $mode);
        }

        Mage::getSingleton('belitsoft_survey/session')->clear();

        $this->_setRestricted();

        if (!empty($post['isAjax']) || $this->getRequest()->getParam('isAjax')) {
            if ($this->_survey->getId()) {
                $html = $this->_survey->getData('survey_final_page_text');
                if ($this->getRequest()->getParam('strip_tags')) {
                    $html = strip_tags($html);
                }
                echo $html;
            }
            die;
        } else {
            $this->_forward('view');
        }
    }


    /**
     * Displays the current question
     */
    public function questionAction()
    {
        try {
#			Mage::log(__METHOD__.': start');

            if ($this->_initSurvey(true)) {
                if (Mage::getSingleton('belitsoft_survey/session')->isFinished()) {
#					Mage::log(Mage::getSingleton('belitsoft_survey/session'));
                    Mage::register('survey_finished', true);
                    $this->_setRestricted();
                    $this->loadLayout()->renderLayout();
                    return;
                }

                //Initialize questions array
                $this->_initQuestions();

                if (!Mage::getSingleton('belitsoft_survey/session')->isStarted()) {
#					Mage::log(__METHOD__.': start survey');
                    Mage::getSingleton('belitsoft_survey/session')->startSurvey($this->_survey, $this->_questions);
                } else {
                    $sess_stage      = Mage::getSingleton('belitsoft_survey/session')->getSurveyStage();
                    $stage           = (int)$this->getRequest()->getParam('stage', $sess_stage);
                    $count_questions = Mage::getSingleton('belitsoft_survey/session')->countQuestions();
                    if ((($count_questions + 1) < $stage) || ($stage < 1)) {
                        $stage = $sess_stage;
                    }

                    $post = $this->getRequest()->getPost();
                    unset($post['stage'], $post['curr_stage']);

                    if (($count_questions + 1) == $stage) {
                        $start_id  = Mage::getSingleton('belitsoft_survey/session')->getStartId();
                        $mode      = Mage::getSingleton('belitsoft_survey/session')->getMode();
                        $questions = Mage::getSingleton('belitsoft_survey/session')->finishSurvey($post);
                        $this->_saveAnswers($questions, $start_id, $mode);
                        $this->_setRestricted();
                        Mage::register('survey_finished', true);
                    } else if ($sess_stage != $stage) {
                        if ($stage < $sess_stage) {
#							Mage::log(__METHOD__.': prev survey question');
                            Mage::getSingleton('belitsoft_survey/session')->prevSurveyQuestion();
                        } else {
#							Mage::log(__METHOD__.': next survey question');
#							Mage::log(__METHOD__.': next survey question data:'.print_r($post, true));
                            Mage::getSingleton('belitsoft_survey/session')->nextSurveyQuestion($post);
                        }
                    }
                }

                $this->_initCurrentQuestion();
                $this->_initButtons();
                $this->_initStage();
                $this->_initSelectedOptions();

#				Mage::log(Mage::getSingleton('belitsoft_survey/session'));
                $this->loadLayout()->renderLayout();

            } else {
                throw new Exception('Session has expired');
            }

#			Mage::log(__METHOD__.': end');

        } catch (Exception $e) {
            Mage::log($e);
            Mage::logException($e);

            $this->_redirectUrl(Mage::helper('belitsoft_survey')->getSurveyMainPageUrl());
        }
    }

    /**
     * Initialize survey category object
     *
     * @return Belitsoft_Survey_Model_Category
     */
    protected function _initCategory($category_id)
    {
        $this->_category = Mage::getModel('belitsoft_survey/category')->load($category_id);

        if (!Mage::helper('belitsoft_survey')->canShowCategory($this->_category)) {
            return false;
        }

        Mage::unregister('survey_current_category');
        Mage::register('survey_current_category', $this->_category);

        return true;
    }

    /**
     * Initialize requested survey object
     *
     * @param  boolean $session Get/set survey object from/in session
     * @return Belitsoft_Survey_Model_Survey
     */
    protected function _initSurvey($from_session = false)
    {
        $this->_sid = (int)$this->getRequest()->getParam('sid', false);
        if (!$from_session || !Mage::getSingleton('belitsoft_survey/session')->isStarted()) {
            if ($this->_sid < 1) {
                return false;
            }

            $this->_survey = Mage::getModel('belitsoft_survey/survey')->load($this->_sid);

            if (!Mage::helper('belitsoft_survey')->canShowSurvey($this->_survey)) {
                return false;
            }

        } else if ($from_session && Mage::getSingleton('belitsoft_survey/session')->isStarted()) {
            $survey = Mage::getSingleton('belitsoft_survey/session')->getSurvey();

            if ($survey instanceof Belitsoft_Survey_Model_Survey) {
                $this->_survey = $survey;
            } else {
                return false;
            }
        }

        if (($cid = $this->getRequest()->getParam('cid'))
            || (($categoryIds = $this->_survey->getCategoryId()) && !empty($categoryIds[0]) && ($cid = $categoryIds[0]))
        ) {
            if (!$this->_initCategory($cid)) {
                return false;
            }
        } else {
            return false;
        }

        Mage::unregister('survey_current_survey');
        Mage::register('survey_current_survey', $this->_survey);

        return $this->_survey;
    }

    /**
     * Initialize requested questions collection
     *
     * @param  boolean $session Get/set survey object from/in session
     * @return array
     */
    protected function _initQuestions($from_session = false)
    {
        if (!$from_session || !Mage::getSingleton('belitsoft_survey/session')->isStarted()) {
            $this->_questions = Mage::getResourceSingleton('belitsoft_survey/question_collection')
                ->addIsActiveFilter()
                ->addSurveyIdFilter($this->_survey)
                ->setOrder('sort_order', 'ASC')
                ->setOrder('creation_date', 'ASC')
                ->load()
                ->getItems();

        } else if ($from_session && Mage::getSingleton('belitsoft_survey/session')->isStarted()) {
            $questions = Mage::getSingleton('belitsoft_survey/session')->getQuestions();
            if (is_array($questions) && count($questions)) {
                $this->_questions = $questions;
            } else {
                return false;
            }
        }

        $this->_updateQuestions();

        return $this->_questions;
    }

    protected function _updateQuestions($questions = null)
    {
        Mage::unregister('survey_questions');
        Mage::register('survey_questions', ($questions ? $questions : $this->_questions));

        return $this;
    }

    /**
     * Initialize requested questions collection
     *
     * @param  boolean $session Get/set survey object from/in session
     * @return array
     */
    protected function _initSession($aid, $customer_answers)
    {
        $session = Mage::getSingleton('belitsoft_survey/session')
            ->startSurvey($this->_survey, $this->_questions);

        if (Mage::helper('belitsoft_survey')->isEditMode()) {
            $session->setEditMode($aid);
        } else {
            $session->setViewMode($aid);
        }

        $session->initFromArray($customer_answers);

        return $session;
    }

    protected function _prepareAnswers($answers)
    {
        $customer_answers = array();
        if (empty($this->_questions) || !is_array($this->_questions)) {
            return $customer_answers;
        }

        $answers_like_post = array();
        foreach ($answers as $answer) {
            $answers_like_post[$answer->getQuestionId()][] = $answer;
        }

        foreach ($this->_questions as $key => $question) {
            if (empty($answers_like_post[$key]) || !is_array($answers_like_post[$key])) {
                continue;
            }
            $answer_model   = $this->getAnswerModel($question);
            $aswer_with_key = $answer_model->getAswerWithKey($question, $answers_like_post[$key]);
            if (!empty($aswer_with_key)) {
                $customer_answers[$key] = $aswer_with_key;
                $this->_questions[$key]->setUserData($aswer_with_key);
            }
        }

        $this->_updateQuestions();

        return $customer_answers;
    }

    protected function _checkRestricted()
    {
        $restricted = Mage::helper('belitsoft_survey')->checkRestricted();

        Mage::unregister('survey_restricted');
        Mage::register('survey_restricted', $restricted);

        return $restricted;
    }

    protected function _setRestricted()
    {
        $survey            = Mage::registry('survey_current_survey');
        $enable_user_check = Mage::getModel('belitsoft_survey/config')->getConfigData('enable_user_check');

        if ($enable_user_check) {
            $cookie_lifetime = intval(Mage::getModel('belitsoft_survey/config')->getConfigData('cookie_lifetime'));
            if (!$cookie_lifetime) {
                $cookie_lifetime = 365 * 100;
            }
            setcookie(md5('belitsoft_survey_' . $survey->getId()), md5(time()), time() + 60 * 60 * 24 * $cookie_lifetime, '/');
        }
    }

    /**
     * Initialize current requested question object
     *
     * @return Belitsoft_Survey_Model_Question
     */
    protected function _initCurrentQuestion()
    {
        $this->_question = Mage::getSingleton('belitsoft_survey/session')->getCurrentQuestion();
        Mage::register('survey_current_question', $this->_question);

        return $this->_question;
    }

    /**
     * Initialize current buttons
     *
     * @return Belitsoft_Survey_Model_Question
     */
    protected function _initButtons()
    {
        Mage::register('survey_prev_button_show', Mage::getSingleton('belitsoft_survey/session')->getPrevButtonShow());
        Mage::register('survey_next_button_show', Mage::getSingleton('belitsoft_survey/session')->getNextButtonShow());
    }

    /**
     * Initialize current stage
     *
     * @return Belitsoft_Survey_Model_Question
     */
    protected function _initStage()
    {
        Mage::register('survey_current_stage', Mage::getSingleton('belitsoft_survey/session')->getSurveyStage());
    }

    /**
     * Initialize current stage
     *
     * @return Belitsoft_Survey_Model_Question
     */
    protected function _initSelectedOptions()
    {
        Mage::register('survey_question_selected_options', Mage::getSingleton('belitsoft_survey/session')->getSurveyStage());
    }


    /**
     * Get user answers
     *
     * @param  array $post Array of users answers
     * @return array Array of Belitsoft_Survey_Model_Question objects
     */
    protected function _getAnswers($post)
    {
        $questions = array();
        if ($this->_initSurvey()) {
            $questions = (array)$this->_initQuestions();
            foreach ($questions as $key => $question) {
                $answer_model = $this->getAnswerModel($question);
                $answer_key   = $answer_model->getUserDataKey($question);
                if (is_array($answer_key)) {
                    $user_data = array();
                    foreach ($answer_key as $answer_k) {
                        if (array_key_exists($answer_k, $post)) {
                            $user_data[$answer_k] = $post[$answer_k];
                        }
                    }
                    $questions[$key]->setData('user_data', $user_data);

                } elseif (is_string($answer_key)) {
                    if (array_key_exists($answer_key, $post)) {
                        $questions[$key]->setData('user_data', array($answer_key => $post[$answer_key]));
                    }
                }
            }
        }

        return $questions;
    }

    /**
     * Save user answers
     *
     * @param  array $questions Array of Belitsoft_Survey_Model_Question objects
     * @return void
     */
    protected function _saveAnswers($questions, $start_id = null, $mode = Belitsoft_Survey_Model_Session::MODE_NEW)
    {
        if (Mage::getSingleton('belitsoft_survey/session')->isViewMode()) {
            return;
        }

        $customer_id = Mage::getSingleton('customer/session')->getCustomerId();

        if (($mode == Belitsoft_Survey_Model_Session::MODE_NEW) || ($start_id < 1)) {
            $start_id = Mage::getModel('belitsoft_survey/answer')->getMaxStartId() + 1;
        } else if (($mode == Belitsoft_Survey_Model_Session::MODE_EDIT) && ($start_id > 0)) {
            Mage::getModel('belitsoft_survey/answer')->deleteAnswers($start_id);
        }

        foreach ($questions as $question) {
            /* @var $question Belitsoft_Survey_Model_Question */
            if (!$question->getUserData()) {
                continue;
            }

            $answer_model = $this->getAnswerModel($question);
            $question->setStartId($start_id);
            $question->setCustomerId($customer_id);
            $answer_model->saveAnswer($question);
        }
    }

    protected function getAnswerModel($question_type)
    {
        if ($question_type instanceof Belitsoft_Survey_Model_Question) {
            $question_type = $question_type->getQuestionType();
        }

        $question_type = (string)$question_type;

        try {
            $answer_model = Mage::getModel('belitsoft_survey/answer_' . $question_type);
        } catch (Exception $e) {
            $answer_model = Mage::getModel('belitsoft_survey/answer');
        }

        if (!$answer_model) {
            $answer_model = Mage::getModel('belitsoft_survey/answer');
        }

        return $answer_model;
    }

    protected function getQuestionType($question_type)
    {
        if ($question_type instanceof Belitsoft_Survey_Model_Question) {
            $question_type = $question_type->getQuestionType();
        }

        $question_type = (string)$question_type;

        if (!$question_type || ($question_type == 'shortanswer')) {
            return 'default';
        }

        return $question_type;
    }
}
