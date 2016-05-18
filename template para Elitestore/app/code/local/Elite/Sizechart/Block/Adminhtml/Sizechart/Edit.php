<?php
class Elite_Sizechart_Block_Adminhtml_Sizechart_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'sizechart';
        $this->_controller = 'adminhtml_sizechart';
        $this->_updateButton('save', 'label', Mage::helper('sizechart')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('sizechart')->__('Delete Item'));
    }
    public function getHeaderText()
    {
        if( Mage::registry('sizechart_data') && Mage::registry('sizechart_data')->getId() ) {
            return Mage::helper('sizechart')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('sizechart_data')->getTitle()));
        } else {
            return Mage::helper('sizechart')->__('Add Item');
        }
    }
}