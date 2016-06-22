<?php

class Customex_Orderrequest_Block_Adminhtml_Orderrequest_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'orderrequest';
        $this->_controller = 'adminhtml_orderrequest';
        
        $this->_updateButton('save', 'label', Mage::helper('orderrequest')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('productcontact')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('orderrequest_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'orderrequest_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'orderrequest_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('orderrequest_data') && Mage::registry('orderrequest_data')->getId() ) {
            return Mage::helper('orderrequest')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('orderrequest_data')->getTitle()));
        } else {
            return Mage::helper('orderrequest')->__('Add Item');
        }
    }
}