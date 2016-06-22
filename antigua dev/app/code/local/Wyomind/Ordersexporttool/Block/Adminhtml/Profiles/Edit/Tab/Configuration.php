<?php

class Wyomind_Ordersexporttool_Block_Adminhtml_Profiles_Edit_Tab_Configuration extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $model = Mage::getModel('ordersexporttool/profiles');
        $model->load($this->getRequest()->getParam('id'));
        $this->setForm($form);

        $fieldset = $form->addFieldset('ordersexporttool_form_1', array('legend' => $this->__('File configuration')));

        (isset($_GET['debug'])) ? $type = 'text' : $type = 'hidden';

        if ($this->getRequest()->getParam('id')) {
            $fieldset->addField('file_id', $type, array(
                'name' => 'file_id',
            ));
        }

        $fieldset->addField('file_scheduled_task', $type, array(
            'name' => 'file_scheduled_task',
            'value' => $model->getCronExpr()
        ));

        $fieldset->addField('file_attributes', 'hidden', array(
            'name' => 'file_attributes',
            'value' => $model->getFile_attributes()
        ));



        $fieldset->addField('file_name', 'text', array(
            'label' => Mage::helper('ordersexporttool')->__('File name'),
            'class' => 'required-entry refresh',
            'required' => true,
            'name' => 'file_name',
            'id' => 'file_name',
        ));


        $fieldset->addField('file_path', 'text', array(
            'label' => Mage::helper('ordersexporttool')->__('File directory'),
            'name' => 'file_path',
            'required' => true,
            'value' => $model->getFilePath()
        ));


        $fieldset->addField('file_encoding', 'select', array(
            'label' => Mage::helper('ordersexporttool')->__('Encoding type'),
            'required' => true,
            'class' => 'required-entry',
            'name' => 'file_encoding',
            'id' => 'file_encoding',
            'values' => array(
                array(
                    'value' => 'UTF-8',
                    'label' => 'UTF-8'
                ),
                array(
                    'value' => 'Windows-1252',
                    'label' => 'Windows-1252 (ANSI)'
                ),
            )
        ));

        $fieldset->addField('file_type', 'select', array(
            'label' => Mage::helper('ordersexporttool')->__('File type'),
            'required' => true,
            'class' => 'required-entry',
            'name' => 'file_type',
            'id' => 'file_type',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => 'xml'
                ),
                array(
                    'value' => 2,
                    'label' => 'txt'
                ),
                array(
                    'value' => 3,
                    'label' => 'csv'
                ),
                array(
                    'value' => 4,
                    'label' => 'tsv'
                ),
                array(
                    'value' => 5,
                    'label' => 'din'
                )
            )
        ));


        ($model->getFileName()) ? $fn = $model->getFileName() : $fn = "filename";
        switch ($model->getFileType()) {
            case 1 :
                $ext = ".xml";
                break;
            case 2 :
                $ext = ".txt";
                break;
            case 3 :
                $ext = ".csv";
                break;

            case 4 :
                $ext = ".tsv";
                break;
            case 5 :
                $ext = ".din";
                break;
            default: $ext = ".ext";
        }
        $fieldset->addField('file_date_format', 'select', array(
            'label' => Mage::helper('ordersexporttool')->__('File name format '),
            'name' => 'file_date_format',
            'values' => array(
                array(
                    'value' => '{f}',
                    'label' => $this->__($fn) . $ext
                ),
                array(
                    'value' => 'Y-m-d-{f}',
                    'label' => $this->__(Mage::getSingleton('core/date')->date('Y-m-d') . '-' . $fn . $ext)
                ),
                array(
                    'value' => 'Y-m-d-H-i-s-{f}',
                    'label' => $this->__(Mage::getSingleton('core/date')->date('Y-m-d-H-i-s') . '-' . $fn . $ext)
                ),
                array(
                    'value' => '{f}-Y-m-d',
                    'label' => $this->__($fn . '-' . Mage::getSingleton('core/date')->date('Y-m-d') . $ext)
                ),
                array(
                    'value' => '{f}-Y-m-d-H-i-s',
                    'label' => $this->__($fn . '-' . Mage::getSingleton('core/date')->date('Y-m-d-H-i-s') . $ext)
                ),
                array(
                    'value' => 'Y-m-d H-i-s',
                    'label' => $this->__(Mage::getSingleton('core/date')->date('Y-m-d H-i-s') . $ext)
                ),
            )
        ));

        $fieldset->addField('file_repeat_for_each', 'select', array(
            'label' => Mage::helper('ordersexporttool')->__('Export each order in a distinct file'),
            'name' => 'file_repeat_for_each',
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

        $fieldset->addField('file_repeat_for_each_increment', 'select', array(
            'label' => Mage::helper('ordersexporttool')->__('File name suffix '),
            'required' => true,
            'class' => 'required-entry',
            'name' => 'file_repeat_for_each_increment',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => 'order #'
                ),
                array(
                    'value' => 2,
                    'label' => 'Magento internal order ID'
                ),
                array(
                    'value' => 3,
                    'label' => 'Module internal auto-increment'
                )
            )
        ));

        $fieldset->addField('file_incremential_column', 'select', array(
            'label' => Mage::helper('ordersexporttool')->__('Add a counter as the 1st column'),
            'name' => 'file_incremential_column',
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
        $fieldset->addField('file_incremential_column_name', 'text', array(
            'label' => Mage::helper('ordersexporttool')->__('Increment column header'),
            'name' => 'file_incremential_column_name',
            'class' => '',
        ));

        $fieldset = $form->addFieldset('ordersexporttool_form_3', array('legend' => $this->__('Product filters')));




        $fieldset->addField('file_product_type', 'checkboxes', array(
            'label' => 'Product type to export',
            'name' => 'file_product_type[]',
            'values' => array(
                array('value' => 'simple', 'label' => 'Simple, Virtual, Downloadable products'),
                array('value' => 'configurable', 'label' => 'Configurable products'),
                array('value' => 'grouped_parent', 'label' => 'Grouped products'),
                // array('value' => 'grouped_children', 'label' => '<span style="color: #666666;font-style: italic;">Children of grouped products</span>'),
                array('value' => 'bundle_parent', 'label' => 'Bundle products (main product)'),
                array('value' => 'bundle_children', 'label' => '<span style="color: #666666;font-style: italic;">Children of bundle products</span>'),
            ),
            'onchange' => "",
            'disabled' => false,
        ));



        $fieldset = $form->addFieldset('ordersexporttool_form_2', array('legend' => $this->__('Orders filters')));

        $fieldset->addField('file_store_id', 'multiselect', array(
            'label' => $this->__('Export from Store View'),
            'title' => $this->__('Export from Store View'),
            'name' => 'file_store_id',
            'class' => 'required-entry',
            'required' => true,
            'value' => $model->getFileStoreId(),
            'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm()
        ));

        $fieldset->addField('file_last_exported_id', 'text', array(
            'label' => Mage::helper('ordersexporttool')->__('Start with order #'),
            'name' => 'file_last_exported_id',
        ));

        /* $fieldset->addField('file_first_exported_id', 'text', array(
          'label' => Mage::helper('ordersexporttool')->__('Ending with order #'),
          'name' => 'file_first_exported_id',

          )); */
        $fieldset->addField('file_automatically_update_last_order_id', 'select', array(
            'label' => Mage::helper('ordersexporttool')->__('Register the last exported order #'),
            'name' => 'file_automatically_update_last_order_id',
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

        $fieldset->addField('file_flag', 'select', array(
            'label' => Mage::helper('ordersexporttool')->__('Flag each exported order'),
            'name' => 'file_flag',
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
        $fieldset->addField('file_single_export', 'select', array(
            'label' => Mage::helper('ordersexporttool')->__('Export only unmarked orders '),
            'name' => 'file_single_export',
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
        $fieldset->addField('file_update_status', 'select', array(
            'label' => Mage::helper('ordersexporttool')->__('Update the order status'),
            'name' => 'file_update_status',
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

        foreach (array_merge(Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates(), Mage::getSingleton('sales/order_config')->getInvisibleOnFrontStates()) as $key => $state) {
            $options = array();
            foreach (Mage::getSingleton('sales/order_config')->getStateStatuses($state) as $k => $s) {
                $options[] = array("value" => $state . "-" . $k, "label" => $s);
            }
            $values[] = array('value' => $options, 'label' => $state);
        }
        $fieldset->addField('file_update_status_to', 'select', array(
            'label' => Mage::helper('ordersexporttool')->__('New order status'),
            'name' => 'file_update_status_to',
            'values' => $values
        ));
        $fieldset->addField('file_update_status_message', 'text', array(
            'label' => Mage::helper('ordersexporttool')->__('Message in the order history'),
            'name' => 'file_update_status_message',
            'class' => '',
        ));




        if (version_compare(Mage::getVersion(), '1.3.0', '>')) {


            $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
                            ->addFieldMap('file_flag', 'file_flag')
                            ->addFieldMap('file_single_export', 'file_single_export')
                            ->addFieldDependence('file_single_export', 'file_flag', 1)
                            ->addFieldMap('file_repeat_for_each', 'file_repeat_for_each')
                            ->addFieldMap('file_repeat_for_each_increment', 'file_repeat_for_each_increment')
                            // ->addFieldMap('file_order_by_field', 'file_order_by_field')
                            //->addFieldMap('file_order_by', 'file_order_by')
                            ->addFieldMap('file_incremential_column', 'file_incremential_column')
                            ->addFieldMap('file_incremential_column_name', 'file_incremential_column_name')
                            ->addFieldDependence('file_repeat_for_each_increment', 'file_repeat_for_each', 1)
                            //->addFieldDependence('file_order_by_field', 'file_order_by', 1)
                            ->addFieldMap('file_type', 'file_type')
                            ->addFieldDependence('file_incremential_column_name', 'file_incremential_column', 1)
                            ->addFieldMap('file_update_status', 'file_update_status')
                            ->addFieldMap('file_update_status_to', 'file_update_status_to')
                            ->addFieldMap('file_update_status_message', 'file_update_status_message')
                            ->addFieldDependence('file_update_status_to', 'file_update_status', 1)
                            ->addFieldDependence('file_update_status_message', 'file_update_status', 1)
            );
        }


        $fieldset->addField('generate', $type, array(
            'name' => 'generate',
            'value' => ''
        ));
        $fieldset->addField('continue', $type, array(
            'name' => 'continue',
            'value' => ''
        ));
        $fieldset->addField('copy', $type, array(
            'name' => 'copy',
            'value' => ''
        ));

        if (Mage::getSingleton('adminhtml/session')->getOrdersexporttoolData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getOrdersexporttoolData());
            Mage::getSingleton('adminhtml/session')->setOrdersexporttoolData(null);
        } elseif (Mage::registry('ordersexporttool_data')) {

            Mage::registry('ordersexporttool_data')->setFileProductType(explode(',', $model->getFileProductType()));

            $form->setValues(Mage::registry('ordersexporttool_data')->getData());
        }

        $fieldset->addField('sample_url', $type, array(
            'id' => 'preview_path',
            'value' => $this->getUrl('*/*/sample', array('file_id' => $this->getRequest()->getParam('id'), 'real_time_preview' => 1))
        ));
        $fieldset->addField('library_url', $type, array(
            'id' => 'library_path',
            'value' => $this->getUrl('*/*/library', array('file_id' => $this->getRequest()->getParam('id'), 'real_time_preview' => 1))
        ));
        return parent::_prepareForm();
    }

}
