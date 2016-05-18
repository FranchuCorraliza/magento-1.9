<?php

/**
 * Amasty Survey
 *
 * @category    Belitsoft
 * @package     Belitsoft_Survey
 * @copyright   Copyright (c) 2015 Amasty. (http://www.amasty.com)
 */
class Belitsoft_Survey_Block_Frontend_Catdetail_Separate extends Belitsoft_Survey_Block_Frontend_Catdetail
{
    protected $_canDisplay = false;

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        Mage::getSingleton('belitsoft_survey/session')->clear();

        if ($categoryId = $this->getData('category_id')) {
            $this->setCategoryId($categoryId);
        }
    }

    public function setCategoryId($categoryId)
    {
        $this->_canDisplay = $this->_init($categoryId);

        return $this;
    }

    /**
     * Initialize requested category object and survey collection
     *
     * @param $categoryId
     * @return Belitsoft_Survey_Model_Category
     */
    protected function _init($categoryId)
    {
        if(Mage::registry('survey_collection') instanceof Varien_Data_Collection) {
            return true;
        }

        if (!$categoryId) {
            return false;
        }

        $category = Mage::getModel('belitsoft_survey/category')->load($categoryId);

        if (!Mage::helper('belitsoft_survey')->canShowCategory($category)) {
            return false;
        }

        Mage::register('survey_current_category', $category);

        $surveyCollection = Mage::getResourceSingleton('belitsoft_survey/survey_collection')
            ->addCategoryFilter($categoryId)
            ->addIsActiveFilter()
            ->addStartDateFilter()
            ->addExpiredDateFilter()
            ->addStoreFilter(Mage::app()->getStore())
            ->addCustomerGroupFilter();

        Mage::register('survey_collection', $surveyCollection);

        return $surveyCollection instanceof Varien_Data_Collection && $surveyCollection->getSize() > 0;
    }

    protected function _toHtml()
    {
        if ($this->_canDisplay) {
            if (!isset($this->_children['survey_list'])) {
                $this->setChild('survey_list', $this->getLayout()->createBlock('belitsoft_survey/frontend_list', 'survey_list')
                    ->setTemplate('survey/list.phtml'));
            }

            return parent::_toHtml();
        }

        return '';
    }
}