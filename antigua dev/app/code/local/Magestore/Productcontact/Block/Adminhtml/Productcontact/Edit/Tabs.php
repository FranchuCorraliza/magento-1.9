<?php

class Magestore_Productcontact_Block_Adminhtml_Productcontact_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('productcontact_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('productcontact')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('productcontact')->__('Item Information'),
          'title'     => Mage::helper('productcontact')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('productcontact/adminhtml_productcontact_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}