<?php
class Elite_Sizechart_Block_Adminhtml_Sizechart_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('sizechart_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('sizechart')->__('News Information'));
    }
    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
        'label' => Mage::helper('sizechart')->__('Item Information'),
        'title' => Mage::helper('sizechart')->__('Item Information'),
        'content' => $this->getLayout()->createBlock('sizechart/adminhtml_sizechart_edit_tab_form')->toHtml(),
        ));
        return parent::_beforeToHtml();
    }
}