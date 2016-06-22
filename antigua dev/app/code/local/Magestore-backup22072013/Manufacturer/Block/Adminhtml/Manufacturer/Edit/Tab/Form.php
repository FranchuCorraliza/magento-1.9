<?php

class Magestore_Manufacturer_Block_Adminhtml_Manufacturer_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  var $_checkeds = array();
  var $_disableds = array();
  protected function _prepareForm()
  {  
	  $manufacturer_data = array();
	  
	  if ( Mage::getSingleton('adminhtml/session')->getManufacturerData() )
      {
          $manufacturer_data = Mage::getSingleton('adminhtml/session')->getManufacturerData();
      } elseif ( Mage::registry('manufacturer_data') ) {
          $manufacturer_data = Mage::registry('manufacturer_data')->getData();		  
      }
	  
	  $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('manufacturer_form', array('legend'=>Mage::helper('manufacturer')->__('Manufacturer information')));
      $this->setStatus($manufacturer_data);	  
	  $store_id = $manufacturer_data['store_id'];
	
	  $fieldset->addField('name', 'text', array(
          'label'     => Mage::helper('manufacturer')->__('Admin Name'),
          'name'      => 'name',
		  'readonly'  => '',
      ));
	  
	  $fieldset->addField('old_name', 'hidden', array(
          'label'     => Mage::helper('manufacturer')->__('Name'),
          'required'  => false,
          'name'      => 'old_name',
      ));
	  
	  $fieldset->addField('name_store', 'text', array(
          'label'     => Mage::helper('manufacturer')->__('Store Manufacturer Name'),    
		  'readonly'  => '',
          'name'      => 'name_store',
		  'disabled'  => $this->_disableds['name_store'],
      ));	  
	  	  
	 if($store_id !=0)
	 $fieldset->addField('label_default_name_store', 'checkbox', array(
          'label'     => Mage::helper('manufacturer')->__('User Default Manufacturer Name'),    
          'name'      => 'label_default_name_store',
		  'checked'   => $this->_checkeds['name_store'],
		  'onclick'   => 'doCheck(\'label_default_name_store\',\'default_name_store\',\'name_store\')',
		  ));	  
		  
	 $fieldset->addField('default_name_store', 'hidden', array(  
          'name'      => 'default_name_store',
		  ));			  
	  
	  $fieldset->addField('store_id', 'select', array(
          'label'     => Mage::helper('manufacturer')->__('Store'),         
          'required'  => true,
          'name'      => 'store_label',
		  'disabled'  => true,
		  'values'    => Mage::helper('manufacturer')->getOptionStore(),
      ));	  
	  
	if($store_id == 0)
	{
	 $fieldset->addField('url_key', 'text', array(
          'label'     => Mage::helper('manufacturer')->__('Url Key'),         
          'required'  => true,
          'name'      => 'url_key',
      ));
	}  
	 
	 $fieldset->addField('page_title', 'text', array(
          'label'     => Mage::helper('manufacturer')->__('Page Title'),         
          'required'  => false,
          'name'      => 'page_title',
		  'disabled'  => $this->_disableds['page_title'],
      ));
	  
	 if($store_id !=0)
	 $fieldset->addField('label_default_page_title', 'checkbox', array(
          'label'     => Mage::helper('manufacturer')->__('User Default Page Title'),    
          'name'      => 'label_default_page_title',
		  'checked'   => $this->_checkeds['page_title'],
		  'onclick'   => 'doCheck(\'label_default_page_title\',\'default_page_title\',\'page_title\')',
		  ));	  	  

	 $fieldset->addField('default_page_title', 'hidden', array(  
          'name'      => 'default_page_title',
		  ));			  		  
		  
 
	  if(isset($manufacturer_data['image']) && $manufacturer_data['image'])
	  {
	  
		  $fieldset->addField('old_image', 'hidden', array(
	          'label'     => Mage::helper('manufacturer')->__('Current Image'),
	          'required'  => false,
	          'name'      => 'old_image',
			  'value'     =>$manufacturer_data['image'],
		  ));
	   }	  
	  
	  $fieldset->addField('image', 'image', array(
		  'label'     => Mage::helper('manufacturer')->__('Image'),
		  'required'  => false,
		  'name'      => 'image',
		  'disabled'  => $this->_disableds['image'],
		
	  ));
	  
	 if($store_id !=0)
	 $fieldset->addField('label_default_image', 'checkbox', array(
          'label'     => Mage::helper('manufacturer')->__('User Default Image'),    
          'name'      => 'label_default_image',
		  'checked'   => $this->_checkeds['image'],
		  'onclick'   => 'doCheck(\'label_default_image\',\'default_image\',\'image\')',
		  ));	

	  $fieldset->addField('default_image', 'hidden', array(  
          'name'      => 'default_image',
		  ));		  
	  	  
	  $fieldset->addField('featured', 'select', array(
          'label'     => Mage::helper('manufacturer')->__('Featured'),
          'name'      => 'featured',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('manufacturer')->__('yes'),
              ),

              array(
                  'value'     => 0,
                  'label'     => Mage::helper('manufacturer')->__('No'),
              ),
          ),
		  'disabled'  => $this->_disableds['featured'],
      ));
	  
	 if($store_id !=0)
	 $fieldset->addField('label_default_featured', 'checkbox', array(
          'label'     => Mage::helper('manufacturer')->__('User Default Featured'),    
          'name'      => 'label_default_featured',
		  'checked'   => $this->_checkeds['featured'],
		  'onclick'   => 'doCheck(\'label_default_featured\',\'default_featured\',\'featured\')',
		  ));	

	$fieldset->addField('default_featured', 'hidden', array(  
          'name'      => 'default_featured',
		  ));		 
	 	  
	  
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('manufacturer')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('manufacturer')->__('Enabled'),
              ),

              array(
                  'value'     => 0,
                  'label'     => Mage::helper('manufacturer')->__('Disabled'),
              ),
          ),
		  'disabled'  => $this->_disableds['status'],
      ));
     
	 if($store_id !=0)
	 $fieldset->addField('label_default_status', 'checkbox', array(
          'label'     => Mage::helper('manufacturer')->__('User Default Status'),    
          'name'      => 'label_default_status',
		  'checked'   => $this->_checkeds['status'],
		  'onclick'   => 'doCheck(\'label_default_status\',\'default_status\',\'status\')',
		  ));	

	$fieldset->addField('default_status', 'hidden', array(  
          'name'      => 'default_status',
		  ));		 

      $fieldset->addField('description_short', 'editor', array(
          'name'      => 'description_short',
          'label'     => Mage::helper('manufacturer')->__('Short Description'),
          'title'     => Mage::helper('manufacturer')->__('Short Description'),
          'style'     => 'width:700px; height:100px;',
          'wysiwyg'   => true,
          'required'  => true,
		  'disabled'  => $this->_disableds['description_short'],
      ));
	  
	 if($store_id !=0)
	 $fieldset->addField('label_default_description_short', 'checkbox', array(
          'label'     => Mage::helper('manufacturer')->__('User Default Short Description'),    
          'name'      => 'label_default_description_short',
		  'checked'   => $this->_checkeds['description_short'],
		  'onclick'   => 'doCheck(\'label_default_description_short\',\'default_description_short\',\'description_short\')',
		  ));	  	  
		  
	 $fieldset->addField('default_description_short', 'hidden', array(  
          'name'      => 'default_description_short',
		  ));	
		  		  
	 
	 
      $fieldset->addField('description', 'editor', array(
          'name'      => 'description',
          'label'     => Mage::helper('manufacturer')->__('Description'),
          'title'     => Mage::helper('manufacturer')->__('Description'),
          'style'     => 'width:700px; height:200px;',
          'wysiwyg'   => true,
          'required'  => true,
		  'disabled'  => $this->_disableds['description'],
      ));
	  
	 if($store_id !=0)
	 $fieldset->addField('label_default_description', 'checkbox', array(
          'label'     => Mage::helper('manufacturer')->__('User Default Description'),    
          'name'      => 'label_default_description',
		  'checked'   => $this->_checkeds['description'],
		  'onclick'   => 'doCheck(\'label_default_description\',\'default_description\',\'description\')',
		  ));	  	  
		  
	 $fieldset->addField('default_description', 'hidden', array(  
          'name'      => 'default_description',
		  ));	
		  
	  $fieldset->addField('meta_keywords', 'editor', array(
          'name'      => 'meta_keywords',
          'label'     => Mage::helper('manufacturer')->__('Meta Keywords'),
          'title'     => Mage::helper('manufacturer')->__('Meta Keywords'),
          'style'     => 'width:700px; height:100px;',
          'wysiwyg'   => false,
          'required'  => false,
		  'disabled'  => $this->_disableds['meta_keywords'],
      ));
	  
	 if($store_id !=0)
	 $fieldset->addField('label_default_meta_keywords', 'checkbox', array(
          'label'     => Mage::helper('manufacturer')->__('User Default Meta Keywords'),    
          'name'      => 'label_default_meta_keywords',
		  'checked'   => $this->_checkeds['meta_keywords'],
		  'onclick'   => 'doCheck(\'label_default_meta_keywords\',\'default_meta_keywords\',\'meta_keywords\')',
		  ));	

	$fieldset->addField('default_meta_keywords', 'hidden', array(  
          'name'      => 'default_meta_keywords',
		  ));	
		  
	  $fieldset->addField('meta_description', 'editor', array(
          'name'      => 'meta_description',
          'label'     => Mage::helper('manufacturer')->__('Meta Description'),
          'title'     => Mage::helper('manufacturer')->__('Meta Description'),
          'style'     => 'width:700px; height:100px;',
          'wysiwyg'   => false,
          'required'  => false,
		  'disabled'  => $this->_disableds['meta_description'],
      ));

	 if($store_id !=0)
	 $fieldset->addField('label_default_meta_description', 'checkbox', array(
          'label'     => Mage::helper('manufacturer')->__('User Default Meta Description'),    
          'name'      => 'label_default_meta_description',
		  'checked'   => $this->_checkeds['meta_description'],
		  'onclick'   => 'doCheck(\'label_default_meta_description\',\'default_meta_description\',\'meta_description\')',
		  ));	

	$fieldset->addField('default_meta_description', 'hidden', array(  
          'name'      => 'default_meta_description',
		  ));	
		  		  
      if ( Mage::getSingleton('adminhtml/session')->getManufacturerData() )
      {  
          Mage::getSingleton('adminhtml/session')->setManufacturerData(null);
      } elseif ( Mage::registry('manufacturer_data') ) {
	  
      }
	  
	  if(isset($manufacturer_data['name']) && $manufacturer_data['name'])
	  {
		$manufacturer_data['old_name'] = $manufacturer_data['name'];
	  }
	  
	  if(isset($manufacturer_data['image']) && $manufacturer_data['image'])
	  {
		$manufacturer_data['old_image'] =  $manufacturer_data['image'];
		$manufacturer_data['image'] =  Mage::helper('manufacturer')->getUrlImagePath($manufacturer_data['name']) .'/'. $manufacturer_data['image'];
       // $manufacturer_data['image'] = '<img src="'.		$manufacturer_data['image']  .'" />';	  
	  }	
	 
	  $form->setValues($manufacturer_data);
	  
      return parent::_prepareForm();
  }
  
  function setStatus($data)
  {
	$arrFielName = array(0=>'name_store',1=>'page_title',2=>'image',3=>'featured',4=>'status',5=>'description',6=>'meta_description',7=>'meta_keywords',8=>'description_short',);	
	foreach($arrFielName as $fielName)
	{
		if(isset($data['default_'. $fielName] ) && $data['default_'. $fielName] && $data['store_id'])
		{
			$this->_checkeds[$fielName] = 'checked';
			$this->_disableds[$fielName] = true;
		}
		else
		{
			$this->_checkeds[$fielName] = '';
			$this->_disableds[$fielName] = false;			
		}
	}
  }
}