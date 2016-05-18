<?php

/**
 * Amasty Survey
 *
 * @category    Belitsoft
 * @package     Belitsoft_Survey
 * @copyright   Copyright (c) 2015 Amasty. (http://www.amasty.com)
 */
class Belitsoft_Survey_Block_Frontend_Detail_Separate extends Belitsoft_Survey_Block_Frontend_Detail
{
    protected $_sid;
    protected $_survey;
    protected $_category;
    protected $_questions;
    protected $_canDisplay = false;

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        Mage::getSingleton('belitsoft_survey/session')->clear();

        if ($surveyId = $this->getData('survey_id')) {
            $this->setSurveyId($surveyId);
        }
    }

    public function setSurveyId($surveyId)
    {
        $this->getRequest()->setParam('sid', $surveyId);

        $this->_canDisplay = $this->_initSurvey()
            && !Mage::helper('belitsoft_survey')->checkRestricted()
            && $this->_initCategory()
            && $this->_initQuestions();

        return $this;
    }

    protected function _initSurvey()
    {
        $this->_sid = (int)$this->getRequest()->getParam('sid', false);
        if ($this->_sid < 1) {
            return false;
        }

        $this->_survey = Mage::getModel('belitsoft_survey/survey')->load($this->_sid);
        if (!$this->_survey->getId()) {
            return false;
        }

        if (!Mage::helper('belitsoft_survey')->canShowSurvey($this->_survey)) {
            return false;
        }

        Mage::unregister('survey_current_survey');
        Mage::register('survey_current_survey', $this->_survey);

        return true;
    }

    protected function _initCategory()
    {
        $categoryIds = $this->getSurvey()->getCategoryId();
        if (empty($categoryIds[0])) {
            return false;
        }

        $cid = (int)$categoryIds[0];
        if ($cid < 1) {
            return false;
        }

        $this->_category = Mage::getModel('belitsoft_survey/category')->load($cid);
        if (!$this->_category->getId()) {
            return false;
        }

        if (!Mage::helper('belitsoft_survey')->canShowCategory($this->_category)) {
            return false;
        }

        Mage::unregister('survey_current_category');
        Mage::register('survey_current_category', $this->_category);

        return true;
    }

    protected function _initQuestions()
    {
        $this->_questions = Mage::getResourceSingleton('belitsoft_survey/question_collection')
            ->addIsActiveFilter()
            ->addSurveyIdFilter($this->_survey)
            ->setOrder('sort_order', 'ASC')
            ->setOrder('creation_date', 'ASC')
            ->load()
            ->getItems();

        Mage::unregister('survey_questions');
        Mage::register('survey_questions', $this->_questions);

        return is_array($this->_questions) && count($this->_questions) > 0;
    }

    public function getSurvey()
    {
        if (is_object($this->_survey)) {
            return $this->_survey;
        }

        return parent::getSurvey();
    }

    public function getCategory()
    {
        if (is_object($this->_category)) {
            return $this->_category;
        }

        return parent::getCategory();
    }

    protected function _toHtml()
    {
        if ($this->_canDisplay && is_object($this->getSurvey()) && $this->getSurvey()->getId() > 0) {
            if (!isset($this->_children['survey_questions'])) {
                $layout = $this->getLayout();

                $questionPickone  = $layout->createBlock('belitsoft_survey/frontend_question_pickone', 'survey_question_pickone')
                    ->setTemplate('survey/question/pickone.phtml');
                $questionPickmany = $layout->createBlock('belitsoft_survey/frontend_question_pickmany', 'survey_question_pickmany')
                    ->setTemplate('survey/question/pickmany.phtml');
                $questionRanking  = $layout->createBlock('belitsoft_survey/frontend_question_ranking', 'survey_question_ranking')
                    ->setTemplate('survey/question/ranking.phtml');
                $questionDefault  = $layout->createBlock('belitsoft_survey/frontend_question_default', 'survey_question_default')
                    ->setTemplate('survey/question/default.phtml');

                $question = $layout->createBlock('belitsoft_survey/frontend_question', 'survey_question')
                    ->setTemplate('survey/question.phtml')
                    ->setChild('survey_question_pickone', $questionPickone)
                    ->setChild('survey_question_pickmany', $questionPickmany)
                    ->setChild('survey_question_ranking', $questionRanking)
                    ->setChild('survey_question_default', $questionDefault);


                $questions = $layout->createBlock('belitsoft_survey/frontend_questions', 'survey_questions')
                    ->setTemplate('survey/questions.phtml')
                    ->setIsAjax(null === $this->_getData('is_ajax') ? true : $this->_getData('is_ajax'))
                    ->setIsReturnAlert(null === $this->_getData('is_return_alert') ? true : $this->_getData('is_return_alert'))
                    ->setChild('survey_question', $question);


                $this->setChild('survey_questions', $questions);
            }

            return parent::_toHtml();
        }

        return '';
    }
}