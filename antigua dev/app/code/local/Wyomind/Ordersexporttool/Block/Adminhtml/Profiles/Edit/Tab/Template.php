<?php

class Wyomind_Ordersexporttool_Block_Adminhtml_Profiles_Edit_Tab_Template extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $model = Mage::getModel('ordersexporttool/profiles');
        $model->load($this->getRequest()->getParam('id'));
        $this->setForm($form);

        $fieldset = $form->addFieldset('ordersexporttool_form', array('legend' => $this->__('File Template')));



        $fieldset->addField('file_include_header', 'select', array(
            'label' => Mage::helper('ordersexporttool')->__('Include header'),
            'required' => true,
            'class' => 'required-entry txt-type refresh',
            'name' => 'file_include_header',
            'id' => 'file_include_header',
            'values' => array(
                array(
                    'value' => 0,
                    'label' => $this->__('no')
                ),
                array(
                    'value' => 1,
                    'label' => $this->__('yes')
                )
            )
        ));




        $fieldset->addField('file_enclose_data', 'select', array(
            'label' => Mage::helper('ordersexporttool')->__('Enclose xml tag content inside CDATA (recommended)'),
            'required' => true,
            'class' => 'required-entry xml-type',
            'name' => 'file_enclose_data',
            'id' => 'file_enclose_data',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => $this->__('yes')
                ),
                array(
                    'value' => 0,
                    'label' => $this->__('no')
                )
            )
        ));

        $fieldset->addField('file_extra_header', 'textarea', array(
            'label' => Mage::helper('ordersexporttool')->__('Extra header'),
            'class' => 'txt-type refresh not-required',
            'name' => 'file_extra_header',
            'style' => 'height:60px;width:500px',
        ));

        $fieldset->addField('file_header', 'textarea', array(
            'label' => Mage::helper('ordersexporttool')->__('Header pattern'),
            'class' => 'refresh',
            'name' => 'file_header',
            'required' => true,
            'style' => 'height:120px;width:500px',
        ));

        $fieldset->addField('file_body', 'textarea', array(
            'label' => Mage::helper('ordersexporttool')->__('Order pattern'),
            'class' => 'refresh',
            'required' => true,
            'name' => 'file_body',
            'style' => 'height:300px;width:500px',
        ));

        $fieldset->addField('file_footer', 'textarea', array(
            'label' => Mage::helper('ordersexporttool')->__('Footer pattern'),
            'class' => 'xml-type refresh',
            'required' => true,
            'id' => 'file_footer',
            'name' => 'file_footer',
            'style' => 'height:60px;width:500px',
        ));



        $fieldset->addField('file_separator', 'select', array(
            'label' => Mage::helper('ordersexporttool')->__('Fields delimiter'),
            'class' => 'txt-type refresh required-entry',
            'id' => 'file_separator',
            'required' => true,
            'name' => 'file_separator',
            'style' => '',
            'values' => array(
                array(
                    'value' => ';',
                    'label' => ';'
                ),
                array(
                    'value' => ',',
                    'label' => ','
                ),
                array(
                    'value' => '|',
                    'label' => '|'
                ),
                array(
                    'value' => '\t',
                    'label' => '\tab'
                ),
                array(
                    'value' => '[|]',
                    'label' => '[|]'
                ),
            )
        ));
        $fieldset->addField('file_protector', 'select', array(
            'label' => Mage::helper('ordersexporttool')->__('Fields enclosure'),
            'class' => 'txt-type refresh not-required',
            'maxlength' => 1,
            'name' => 'file_protector',
            'values' => array(
                array(
                    'value' => '"',
                    'label' => '"'
                ),
                array(
                    'value' => "'",
                    'label' => "'"
                ),
                array(
                    'value' => "",
                    'label' => Mage::helper('ordersexporttool')->__('none'),
                ),
            )
        ));





        if (Mage::getSingleton('adminhtml/session')->getOrdersexporttoolData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getOrdersexporttoolData());
            Mage::getSingleton('adminhtml/session')->setOrdersexporttoolData(null);
        } elseif (Mage::registry('ordersexporttool_data')) {
            $form->setValues(Mage::registry('ordersexporttool_data')->getData());
        }

        $fieldset->addField('sample_url', 'hidden', array(
            'id' => 'preview_path',
            'value' => $this->getUrl('*/*/sample', array('file_id' => $this->getRequest()->getParam('id'), 'real_time_preview' => 1))
        ));
        return parent::_prepareForm();
    }

}
