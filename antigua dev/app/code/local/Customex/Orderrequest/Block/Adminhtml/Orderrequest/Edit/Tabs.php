<?php

class Customex_Orderrequest_Block_Adminhtml_Orderrequest_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('orderrequest_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('orderrequest')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('orderrequest')->__('Item Information'),
          'title'     => Mage::helper('orderrequest')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('orderrequest/adminhtml_orderrequest_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}