<?php

class Wyomind_Ordersexporttool_Block_Adminhtml_Profiles_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {

        parent::__construct();

        $this->_objectId = 'file_id';
        $this->_controller = 'adminhtml_profiles';
        $this->_blockGroup = 'ordersexporttool';


        if (Mage::registry('ordersexporttool_data')->getFileId()) {
            $this->_addButton('copy', array(
                'label' => Mage::helper('adminhtml')->__('Duplicate'),
                'onclick' => "$('file_id').remove(); editForm.submit();",
                'class' => 'add',
            ));


            /* $this->_addButton('export', array(
              'label'   => Mage::helper('adminhtml')->__('Export template'),
              'onclick' => "$('feed_id').export(); editForm.submit();",
              'class'   => 'go',
              )); */
            $this->_addButton('generate', array(
                'label' => Mage::helper('adminhtml')->__('Generate'),
                'onclick' => "$('generate').value=1; editForm.submit();",
                'class' => 'save',
            ));
            $this->_removeButton('save');
             $this->_removeButton('reset');
            $this->_addButton('save', array(
                'label' => Mage::helper('adminhtml')->__('Save'),
                'onclick' => "$('continue').value=1; editForm.submit();",
                'class' => 'save',
            ));
        }
    }

    public function getHeaderText() {
        if (Mage::registry('ordersexporttool_data') && Mage::registry('ordersexporttool_data')->getFileId()) {
            return Mage::helper('ordersexporttool')->__("Edit profile  '%s'", $this->htmlEscape(Mage::registry('ordersexporttool_data')->getFile_name()));
        } else {
            return Mage::helper('ordersexporttool')->__('New profile');
        }
    }

}