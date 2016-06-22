<?php

class Magestore_orderrequest_Block_Adminhtml_orderrequest_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('orderrequest_form', array('legend'=>Mage::helper('orderrequest')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('orderrequest')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('orderrequest')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('orderrequest')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('orderrequest')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('orderrequest')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('orderrequest')->__('Content'),
          'title'     => Mage::helper('orderrequest')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getoOrderrequestData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getOrderrequestData());
          Mage::getSingleton('adminhtml/session')->setOrderrequestData(null);
      } elseif ( Mage::registry('orderrequest_data') ) {
          $form->setValues(Mage::registry('orderrequest_data')->getData());
      }
      return parent::_prepareForm();
  }
}