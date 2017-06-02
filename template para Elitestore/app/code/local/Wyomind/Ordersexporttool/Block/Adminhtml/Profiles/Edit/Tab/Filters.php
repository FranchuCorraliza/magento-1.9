<?php

class Wyomind_Ordersexporttool_Block_Adminhtml_Profiles_Edit_Tab_Filters extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $model = Mage::getModel('ordersexporttool/profiles');

        $model->load($this->getRequest()->getParam('id'));

        $this->setForm($form);
        $fieldset = $form->addFieldset('ordersexporttool_form', array('legend' => $this->__('Filters')));


        $this->setTemplate('ordersexporttool/filters.phtml');


        if (Mage::registry('ordersexporttool_data'))
            $form->setValues(Mage::registry('ordersexporttool_data')->getData());

        return parent::_prepareForm();
    }

}

