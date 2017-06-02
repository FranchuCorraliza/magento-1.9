<?php

class Wyomind_Ordersexporttool_Block_Adminhtml_Attributes_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {

        parent::__construct();

        $this->_objectId = 'attribute_id';
        $this->_controller = 'adminhtml_attributes';
        $this->_blockGroup = 'ordersexporttool';


        if (Mage::registry('ordersexporttool_data')->getAttributeId()) {
           
            $this->_addButton('continue', array(
                'label' => Mage::helper('adminhtml')->__('Save & Continue'),
                'onclick' => "$('continue').value=1; editForm.submit();",
                'class' => 'add',
            ));
            
        }
    }

    public function getHeaderText() {
        if (Mage::registry('ordersexporttool_data') && Mage::registry('ordersexporttool_data')->getFileId()) {
            return Mage::helper('ordersexporttool')->__("Edit custom attribute  '%s'", $this->htmlEscape(Mage::registry('ordersexporttool_data')->getFile_name()));
        } else {
            return Mage::helper('ordersexporttool')->__('New custom attribute');
        }
    }

}