<?php

/**
 * Mageplace Survey
 *
 * @category    Belitsoft
 * @package        Belitsoft_Survey
 * @copyright    Copyright (c) 2012 Mageplace. (http://www.mageplace.com)
 * @license        http://www.mageplace.com/disclaimer.html
 */
class Belitsoft_Survey_Helper_Community extends Mage_Core_Helper_Abstract
{
    const URL_PREFIX     = 'survey';
    const URL_MANAGE_URL = 'manage';

    protected $_url_search_replace = array(
        ' '  => '-',
        '.'  => '_',
        '/'  => '-',
        ';'  => '-',
        ':'  => '-',
        '='  => '-',
        '?'  => '-',
        '__' => '_'
    );

    /**
     * Returns config data
     *
     * @param string $field Requested field
     * @return array config Configuration information
     */
    public function getConfigData($field)
    {
        $path   = 'survey/config/' . $field;
        $config = Mage::getStoreConfig($path, Mage::app()->getStore());
        return $config;
    }

    public function getIntroText($text, $length = 100)
    {
        $introtext    = $this->cleanText($text);
        $new_line_pos = mb_strpos($introtext, "\n");
        if ($new_line_pos) {
            $introtext = mb_substr($introtext, 0, $new_line_pos);
        }

        if (mb_strlen($introtext) > $length) {
            $introtext = mb_substr($introtext, 0, $length);

            $last_space_pos = mb_strrpos($introtext, ' ');
            if ($last_space_pos) {
                $introtext = mb_substr($introtext, 0, $last_space_pos);
            }

            $introtext .= '...';
        }

        return $introtext;
    }

    public function encodeForUrl($text)
    {
        return
            urlencode(
                trim(
                    str_replace(
                        array_keys($this->_url_search_replace),
                        array_values($this->_url_search_replace),
                        strtolower($text)
                    ), ' _'
                )
            );
    }


    public function isEditMode()
    {
        if (!is_null(Mage::registry('survey_edit_mode'))) {
            return Mage::registry('survey_edit_mode');
        } else if (!is_null(Mage::getSingleton('belitsoft_survey/session')->isEditMode())) {
            $this->setEditMode(Mage::getSingleton('belitsoft_survey/session')->isEditMode());
            return Mage::getSingleton('belitsoft_survey/session')->isEditMode();
        }

        return false;
    }

    public function setEditMode($is_edit_mode = true)
    {
        Mage::unregister('survey_edit_mode');
        Mage::register('survey_edit_mode', $is_edit_mode);
    }

    public function isViewMode()
    {
        if (!is_null(Mage::registry('survey_view_mode'))) {
            return Mage::registry('survey_view_mode');
        } else if (!is_null(Mage::getSingleton('belitsoft_survey/session')->isViewMode())) {
            $this->setViewMode(Mage::getSingleton('belitsoft_survey/session')->isViewMode());
            return Mage::getSingleton('belitsoft_survey/session')->isViewMode();
        }

        return false;
    }

    public function setViewMode($is_view_mode = true)
    {
        Mage::unregister('survey_view_mode');
        Mage::register('survey_view_mode', $is_view_mode);
    }

    public function isCustomerCanEdit()
    {
        static $canEdit = null;

        if (is_null($canEdit)) {
            $canEdit = Mage::getModel('belitsoft_survey/config')->getConfigData('enable_user_edit');
        }

        return $canEdit;
    }

    public function isCustomerCanView()
    {
        static $canView = null;

        if (is_null($canView)) {
            $canView = Mage::getModel('belitsoft_survey/config')->getConfigData('enable_user_view') || Mage::getModel('belitsoft_survey/config')->getConfigData('enable_user_edit');
        }

        return $canView;
    }

    /**
     * Check if a category can be shown
     *
     * @param  Belitsoft_Survey_Model_Category|int $category
     * @return boolean
     */
    public function canShowCategory($category)
    {
        if (!($category instanceof Belitsoft_Survey_Model_Category) && ($category = intval($category))) {
            $category = Mage::getModel('belitsoft_survey/category')->load($category);
        }

        if (!$category->getId()) {
            return false;
        }

        if (!$category->getIsActive()) {
            return false;
        }

        if (!in_array(0, $category->getStoreId()) && !in_array(Mage::app()->getStore()->getId(), $category->getStoreId())) {
            return false;
        }

        if ($category->getOnlyForRegistered() && !Mage::helper('customer')->isLoggedIn()) {
            return false;
        } else if ($category->getOnlyForRegistered() && Mage::helper('customer')->isLoggedIn()) {
            if (!in_array(Mage::helper('customer')->getCustomer()->getGroupId(), $category->getCustomerGroupIds())) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if a survey can be shown
     *
     * @param  Belitsoft_Survey_Model_Survey|int $survey
     * @return boolean
     */
    public function canShowSurvey($survey)
    {
        if (is_int($survey)) {
            $survey = Mage::getModel('belitsoft_survey/survey')->load($survey);
        }

        if (!($survey instanceof Belitsoft_Survey_Model_Survey)) {
            return false;
        }

        if (!$survey->getId()) {
            return false;
        }

        if (!$survey->getIsActive()) {
            return false;
        }

        if (!in_array(0, $survey->getStoreId()) && !in_array(Mage::app()->getStore()->getId(), $survey->getStoreId())) {
            return false;
        }

        if (!$this->isEditMode() && !$this->isViewMode()) {
            if ($survey->getStartDate() && (strtotime($survey->getStartDate()) >= Mage::app()->getLocale()->storeTimeStamp())) {
                return false;
            }

            if ($survey->getExpiredDate() && (strtotime($survey->getExpiredDate()) <= Mage::app()->getLocale()->storeTimeStamp())) {
                return false;
            }

            if ($survey->getOnlyForRegistered() && !Mage::helper('customer')->isLoggedIn()) {
                return false;
            } else if ($survey->getOnlyForRegistered() && Mage::helper('customer')->isLoggedIn()) {
                if (!in_array(Mage::helper('customer')->getCustomer()->getGroupId(), $survey->getCustomerGroupIds())) {
                    return false;
                }
            }
        }


        return true;
    }

    /**
     * Check if a question can be shown
     *
     * @param  Belitsoft_Survey_Model_Question|int $question
     * @param  Belitsoft_Survey_Model_Survey $survey
     * @return boolean
     */
    public function canShowQuestion($question, Belitsoft_Survey_Model_Survey $survey = null)
    {
        if (is_int($question)) {
            $question = Mage::getModel('belitsoft_survey/question')->load($question);
        }

        if (!($question instanceof Belitsoft_Survey_Model_Question)) {
            return false;
        }

        if (!$question->getId()) {
            return false;
        }

        if (!$question->getIsActive()) {
            return false;
        }

        if (!is_null($survey) && ($question->getData('survey_id', 0) != $survey->getId())) {
            return false;
        }

        return true;
    }

    /**
     * Number of surveys in a category
     *
     * @param  Belitsoft_Survey_Model_Category|int $category
     * @return int
     */
    public function numberSurveysInCategory($category)
    {
        static $survey_count = array();

        if ($category instanceof Belitsoft_Survey_Model_Category) {
            $category = $category->getId();
        }

        if (empty($survey_count) || !array_key_exists($category, $survey_count)) {
            Mage::getResourceSingleton('belitsoft_survey/survey_collection')->reset();

            $survey_count[$category] = Mage::getResourceSingleton('belitsoft_survey/survey_collection')
                ->addCategoryFilter($category)
                ->addIsActiveFilter()
                ->addStartDateFilter()
                ->addExpiredDateFilter()
                ->addStoreFilter(Mage::app()->getStore())
                ->addCustomerGroupFilter()
                ->getSize();
        }

        return intval(@$survey_count[$category]);
    }

    /**
     * Number of questions in a survey
     *
     * @param  Belitsoft_Survey_Model_Survey|int $survey
     * @return int
     */
    public function numberQuestionsInSurvey($survey)
    {
        static $quest_count = array();

        if ($survey instanceof Belitsoft_Survey_Model_Survey) {
            $survey = $survey->getId();
        }

        if (empty($quest_count) || !array_key_exists($survey, $quest_count)) {
            Mage::getResourceSingleton('belitsoft_survey/question_collection')->reset();

            $quest_count[$survey] = Mage::getResourceSingleton('belitsoft_survey/question_collection')
                ->addIsActiveFilter()
                ->addSurveyIdFilter($survey)
                ->getSize();
        }

        return intval(@$quest_count[$survey]);
    }

    public function getSurveyMainPageUrl()
    {
        return Mage::getUrl($this->getUrlPrefix());
    }

    public function getSurveyManagePage()
    {
        return $this->getUrlPrefix() . '/' . self::URL_MANAGE_URL;
    }

    public function getSurveyManagePageUrl($params = array())
    {
        return Mage::getUrl($this->getSurveyManagePage(), $params);
    }

    /**
     * Retrieve page direct URL
     *
     * @param Belitsoft_Survey_Model_Category|string $category
     * @return string
     */
    public function getCategoryUrl($category = null)
    {
        if (!($category instanceof Belitsoft_Survey_Model_Category)) {
            $category = Mage::getModel('belitsoft_survey/category')->load(intval($category));
        }

        if ($category->getCategoryUrlKey()) {
            return Mage::getUrl($this->getUrlPrefix() . '/' . $category->getCategoryUrlKey());
        } else {
            return Mage::getUrl($this->getUrlPrefix() . '/category/view', array('cid' => intval($category->getId())));
        }
    }

    /**
     * Retrieve page direct URL
     *
     * @param Belitsoft_Survey_Model_Survey|string $survey
     * @return string
     */
    public function getSurveyUrl($survey = null, $action = 'view', $aid = null)
    {
        if (!($survey instanceof Belitsoft_Survey_Model_Survey)) {
            $survey = Mage::getModel('belitsoft_survey/survey')->load(intval($survey));
        }

        $aid = intval($aid);

        if ($survey->getSurveyUrlKey()) {
            $category       = Mage::getModel('belitsoft_survey/category')->load($survey->getCategoryId());
            $categoryUrlKey = $category->getCategoryUrlKey();

            $params = array(
                '_direct' => $this->getUrlPrefix()
                    . '/' . ($categoryUrlKey ? $categoryUrlKey : $category->getCategoryId())
                    . '/' . $survey->getSurveyUrlKey()
                    . ($action != 'view' || $aid > 0 ? '/' . $action : '')
                    . ($aid > 0 ? '/' . $aid : '')
            );

            return Mage::getUrl('', $params);

        } else {
            $params = array('sid' => $survey->getId());
            if ($aid) {
                $params['aid'] = $aid;
            }

            return Mage::getUrl($this->getUrlPrefix() . '/survey/' . $action, $params);
        }
    }

    public function getUrlPrefix()
    {
        static $url_prefix = null;

        if (is_null($url_prefix)) {
            $url_prefix = Mage::getModel('belitsoft_survey/config')->getConfigData('url_prefix');
            if (!$url_prefix) {
                $url_prefix = self::URL_PREFIX;
            }
        }

        return $url_prefix;
    }

    public function getAnswerModel($question_type)
    {
        static $questions = array();

        if ($question_type instanceof Belitsoft_Survey_Model_Question) {
            $question_type = $question_type->getQuestionType();
        }

        if (!array_key_exists($question_type, $questions)) {
            $question_type = (string)$question_type;

            try {
                $answer_model = Mage::getModel('belitsoft_survey/answer_' . $question_type);
            } catch (Exception $e) {
                $answer_model = Mage::getModel('belitsoft_survey/answer');
            }

            if (!$answer_model) {
                $answer_model = Mage::getModel('belitsoft_survey/answer');
            }

            $questions[$question_type] = $answer_model;
        }

        return $questions[$question_type];
    }

    protected function _getHtmlTranslationTable()
    {
        $trans = get_html_translation_table(HTML_ENTITIES);

        $trans[chr(32)]  = '&nbsp;';    // Non-breaking space
        $trans[chr(130)] = '&sbquo;';    // Single Low-9 Quotation Mark
        $trans[chr(131)] = '&fnof;';    // Latin Small Letter F With Hook
        $trans[chr(132)] = '&bdquo;';    // Double Low-9 Quotation Mark
        $trans[chr(133)] = '&hellip;';    // Horizontal Ellipsis
        $trans[chr(134)] = '&dagger;';    // Dagger
        $trans[chr(135)] = '&Dagger;';    // Double Dagger
        $trans[chr(136)] = '&circ;';    // Modifier Letter Circumflex Accent
        $trans[chr(137)] = '&permil;';    // Per Mille Sign
        $trans[chr(138)] = '&Scaron;';    // Latin Capital Letter S With Caron
        $trans[chr(139)] = '&lsaquo;';    // Single Left-Pointing Angle Quotation Mark
        $trans[chr(140)] = '&OElig;';    // Latin Capital Ligature OE
        $trans[chr(145)] = '&lsquo;';    // Left Single Quotation Mark
        $trans[chr(146)] = '&rsquo;';    // Right Single Quotation Mark
        $trans[chr(147)] = '&ldquo;';    // Left Double Quotation Mark
        $trans[chr(148)] = '&rdquo;';    // Right Double Quotation Mark
        $trans[chr(149)] = '&bull;';    // Bullet
        $trans[chr(150)] = '&ndash;';    // En Dash
        $trans[chr(151)] = '&mdash;';    // Em Dash
        $trans[chr(152)] = '&tilde;';    // Small Tilde
        $trans[chr(153)] = '&trade;';    // Trade Mark Sign
        $trans[chr(154)] = '&scaron;';    // Latin Small Letter S With Caron
        $trans[chr(155)] = '&rsaquo;';    // Single Right-Pointing Angle Quotation Mark
        $trans[chr(156)] = '&oelig;';    // Latin Small Ligature OE
        $trans[chr(159)] = '&Yuml;';    // Latin Capital Letter Y With Diaeresis

        return $trans;
    }

    public function decodeHTML($string)
    {
        $string = strtr($string, array_flip($this->_getHtmlTranslationTable()));
        $string = preg_replace("/&#([0-9]+);/me", "chr('\\1')", $string);
        return $string;
    }

    public function cleanText($text)
    {
        $text = strip_tags($text);
        $text = str_replace("\t", '', $text);
        //$text = trim($this->decodeHTML($text));

        return $text;
    }

    public function checkRestricted()
    {
        if (Mage::helper('belitsoft_survey')->isEditMode() || Mage::helper('belitsoft_survey')->isViewMode()) {
            return false;
        }

        $restricted = false;
        if (Mage::getModel('belitsoft_survey/config')->getConfigData('enable_user_check')) {
            $survey = Mage::registry('survey_current_survey');

            $restricted = (bool)Mage::getModel('core/cookie')->get(md5('belitsoft_survey_' . $survey->getId()));
            if (!$restricted && Mage::getSingleton('customer/session')->isLoggedIn()) {
                $restricted = Mage::getModel('belitsoft_survey/answer')->hasCustomerSurveyAswers($survey->getId());
            }
        }

        return $restricted;
    }
}