<?php

class Wyomind_Ordersexporttool_Block_Adminhtml_Profiles_Edit_Tab_Ftp extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {

        $form = new Varien_Data_Form();
        $model = Mage::getModel('ordersexporttool/profiles');

        $model->load($this->getRequest()->getParam('id'));

        $this->setForm($form);
        $fieldset = $form->addFieldset('ordersexporttool_form', array('legend' => $this->__('FTP settings')));


        $fieldset->addField('file_ftp_enabled', 'select', array(
            'label' => Mage::helper('ordersexporttool')->__('Enable FTP upload'),
            'name' => 'file_ftp_enabled',
            'required' => true,
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


        $fieldset->addField('file_ftp_host', 'text', array(
            'label' => Mage::helper('ordersexporttool')->__('Host'),
            'name' => 'file_ftp_host',
        ));

        $fieldset->addField('file_ftp_login', 'text', array(
            'label' => Mage::helper('ordersexporttool')->__('Login'),
            'name' => 'file_ftp_login',
        ));
        $fieldset->addField('file_ftp_password', 'password', array(
            'label' => Mage::helper('ordersexporttool')->__('Password'),
            'name' => 'file_ftp_password',
        ));
         $fieldset->addField('file_ftp_dir', 'text', array(
            'label' => Mage::helper('ordersexporttool')->__('Destination directory'),
            'name' => 'file_ftp_dir',
        ));
		
		 $fieldset->addField('file_use_sftp', 'select', array(
            'label' => Mage::helper('ordersexporttool')->__('Use sftp'),
            'name' => 'file_use_sftp',
            'required' => true,
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
		
		 $fieldset->addField('file_ftp_active', 'select', array(
            'label' => Mage::helper('ordersexporttool')->__('Use passive mode'),
            'name' => 'file_ftp_active',
            'required' => true,
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
		
       


        $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
                        ->addFieldMap('file_ftp_enabled', 'file_ftp_enabled')
                        ->addFieldMap('file_ftp_host', 'file_ftp_host')
                        ->addFieldMap('file_ftp_login', 'file_ftp_login')
                        ->addFieldMap('file_ftp_password', 'file_ftp_password')
                        ->addFieldMap('file_ftp_dir', 'file_ftp_dir')
                        ->addFieldMap('file_ftp_active', 'file_ftp_active')
						->addFieldMap('file_use_sftp', 'file_use_sftp')
                        ->addFieldDependence('file_ftp_host', 'file_ftp_enabled', 1)
                        ->addFieldDependence('file_ftp_login', 'file_ftp_enabled', 1)
                        ->addFieldDependence('file_ftp_password', 'file_ftp_enabled', 1)
                        ->addFieldDependence('file_ftp_active', 'file_ftp_enabled', 1)
						->addFieldDependence('file_use_sftp', 'file_ftp_enabled', 1)
						->addFieldDependence('file_ftp_active', 'file_use_sftp', 0)
                        ->addFieldDependence('file_ftp_dir', 'file_ftp_enabled', 1));



        //  $this->setTemplate('ordersexporttool/ftp.phtml');


        if (Mage::registry('ordersexporttool_data'))
            $form->setValues(Mage::registry('ordersexporttool_data')->getData());

        return parent::_prepareForm();
    }

}