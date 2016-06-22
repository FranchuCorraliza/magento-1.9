<?php
/**
 * Mageplace Survey
 *
 * @category    Belitsoft
 * @package        Belitsoft_Survey
 * @copyright    Copyright (c) 2012 Mageplace. (http://www.mageplace.com)
 * @license        http://www.mageplace.com/disclaimer.html
 */

/**
 * @method Belitsoft_Survey_Block_Frontend_Questions setIsAjax
 * @method mixed getIsAjax
 */
class Belitsoft_Survey_Block_Frontend_Questions extends Belitsoft_Survey_Block_Frontend_Abstract
{
    protected $_isAjax        = false;
    protected $_isReturnAlert = true;

    /**
     * Function to gather the current survey
     *
     * @return Belitsoft_Survey_Model_Survey The current survey
     */
    public function getSurvey()
    {
        $survey = $this->getData('survey');
        if (is_null($survey)) {
            $survey = Mage::registry('survey_current_survey');
            $this->setData('survey', $survey);
        }

        return $survey;
    }

    /**
     * Function to gather the current question collection
     *
     * @return array The current question array
     */
    public function getQuestions()
    {
        $questions = $this->getData('questions');
        if (is_null($questions)) {
            $questions = Mage::registry('survey_questions');
            $this->setData('questions', $questions);
        }

        return $questions;
    }

    public function getQuestionHtml($question)
    {
        Mage::unregister('survey_current_question');
        Mage::register('survey_current_question', $question);

        return $this->getChild('survey_question')->toHtml();
    }

    public function getSurveyContinueFormActionUrl()
    {
        if (Mage::helper('belitsoft_survey')->isViewMode() && ($aid = Mage::getSingleton('belitsoft_survey/session')->getStartId())) {
            return Mage::helper('belitsoft_survey')->getSurveyUrl($this->getSurvey(), 'finish', $aid);
        }

        return Mage::helper('belitsoft_survey')->getSurveyUrl($this->getSurvey(), 'finish');
    }

    public function getFormId()
    {
        return 'survey-questions';
    }

    public function isAjax()
    {
        if ($this->hasData('is_ajax')) {
            return (bool)$this->getIsAjax();
        }

        return $this->_isAjax;
    }

    public function isReturnAlert()
    {
        if ($this->hasData('is_return_alert')) {
            return (bool)$this->getData('is_return_alert');
        }

        return $this->_isReturnAlert;
    }
}