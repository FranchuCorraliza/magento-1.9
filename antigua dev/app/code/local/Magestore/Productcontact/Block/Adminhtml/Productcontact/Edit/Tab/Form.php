<?php

class Magestore_Productcontact_Block_Adminhtml_Productcontact_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('productcontact_form', array('legend'=>Mage::helper('productcontact')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('productcontact')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('productcontact')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('productcontact')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('productcontact')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('productcontact')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('productcontact')->__('Content'),
          'title'     => Mage::helper('productcontact')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getProductcontactData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getProductcontactData());
          Mage::getSingleton('adminhtml/session')->setProductcontactData(null);
      } elseif ( Mage::registry('productcontact_data') ) {
          $form->setValues(Mage::registry('productcontact_data')->getData());
      }
      return parent::_prepareForm();
  }
}