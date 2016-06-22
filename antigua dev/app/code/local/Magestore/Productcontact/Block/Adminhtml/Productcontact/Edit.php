<?php

class Magestore_Productcontact_Block_Adminhtml_Productcontact_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'productcontact';
        $this->_controller = 'adminhtml_productcontact';
        
        $this->_updateButton('save', 'label', Mage::helper('productcontact')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('productcontact')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('productcontact_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'productcontact_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'productcontact_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('productcontact_data') && Mage::registry('productcontact_data')->getId() ) {
            return Mage::helper('productcontact')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('productcontact_data')->getTitle()));
        } else {
            return Mage::helper('productcontact')->__('Add Item');
        }
    }
}