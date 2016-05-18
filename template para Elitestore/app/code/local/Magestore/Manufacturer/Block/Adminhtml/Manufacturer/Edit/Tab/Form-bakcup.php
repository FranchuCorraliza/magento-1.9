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
//añadimos los campos que necesitamos para las lineas

	//1

if(isset($manufacturer_data['imagelinea1']) && $manufacturer_data['imagelinea1'])
	  {
	  
		  $fieldset->addField('old_imagelinea1', 'hidden', array(
	          'label'     => Mage::helper('manufacturer')->__('Current Image linea 1'),
	          'required'  => false,
	          'name'      => 'old_imagelinea1',
			  'value'     =>$manufacturer_data['imagelinea1'],
		  ));
	   }	  
	  
	  $fieldset->addField('imagelinea1', 'image', array(
		  'label'     => Mage::helper('manufacturer')->__('Image line 1'),
		  'required'  => false,
		  'name'      => 'imagelinea1',
		  'disabled'  => $this->_disableds['imagelinea1'],
		
	  ));
	  
	 if($store_id !=0)
	 $fieldset->addField('label_default_imagelinea1', 'checkbox', array(
          'label'     => Mage::helper('manufacturer')->__('User Default image line 1'),    
          'name'      => 'label_default_imagelinea1',
		  'checked'   => $this->_checkeds['imagelinea1'],
		  'onclick'   => 'doCheck(\'label_default_imagelinea1\',\'default_imagelinea1\',\'imagelinea1\')',
		  ));

//2


if(isset($manufacturer_data['imagelinea2']) && $manufacturer_data['imagelinea2'])
	  {
	  
		  $fieldset->addField('old_imagelinea2', 'hidden', array(
	          'label'     => Mage::helper('manufacturer')->__('Current Image linea 2'),
	          'required'  => false,
	          'name'      => 'old_imagelinea2',
			  'value'     =>$manufacturer_data['imagelinea2'],
		  ));
	   }	  
	  
	  $fieldset->addField('imagelinea2', 'image', array(
		  'label'     => Mage::helper('manufacturer')->__('Image line 2'),
		  'required'  => false,
		  'name'      => 'imagelinea2',
		  'disabled'  => $this->_disableds['imagelinea2'],
		
	  ));
	  
	 if($store_id !=0)
	 $fieldset->addField('label_default_imagelinea2', 'checkbox', array(
          'label'     => Mage::helper('manufacturer')->__('User Default image line 2'),    
          'name'      => 'label_default_imagelinea2',
		  'checked'   => $this->_checkeds['imagelinea2'],
		  'onclick'   => 'doCheck(\'label_default_imagelinea2\',\'default_imagelinea2\',\'imagelinea2\')',
		  ));



//3


	if(isset($manufacturer_data['imagelinea3']) && $manufacturer_data['imagelinea3'])
	  {
	  
		  $fieldset->addField('old_imagelinea3', 'hidden', array(
	          'label'     => Mage::helper('manufacturer')->__('Current Image linea 3'),
	          'required'  => false,
	          'name'      => 'old_imagelinea3',
			  'value'     =>$manufacturer_data['imagelinea3'],
		  ));
	   }	  
	  
	  $fieldset->addField('imagelinea3', 'image', array(
		  'label'     => Mage::helper('manufacturer')->__('Image line 3'),
		  'required'  => false,
		  'name'      => 'imagelinea3',
		  'disabled'  => $this->_disableds['imagelinea3'],
		
	  ));
	  
	 if($store_id !=0)
	 $fieldset->addField('label_default_imagelinea3', 'checkbox', array(
          'label'     => Mage::helper('manufacturer')->__('User Default image line 3'),    
          'name'      => 'label_default_imagelinea3',
		  'checked'   => $this->_checkeds['imagelinea3'],
		  'onclick'   => 'doCheck(\'label_default_imagelinea3\',\'default_imagelinea3\',\'imagelinea3\')',
		  ));


//4


if(isset($manufacturer_data['imagelinea4']) && $manufacturer_data['imagelinea4'])
	  {
	  
		  $fieldset->addField('old_imagelinea4', 'hidden', array(
	          'label'     => Mage::helper('manufacturer')->__('Current Image linea 4'),
	          'required'  => false,
	          'name'      => 'old_imagelinea4',
			  'value'     =>$manufacturer_data['imagelinea4'],
		  ));
	   }	  
	  
	  $fieldset->addField('imagelinea4', 'image', array(
		  'label'     => Mage::helper('manufacturer')->__('Image line 4'),
		  'required'  => false,
		  'name'      => 'imagelinea4',
		  'disabled'  => $this->_disableds['imagelinea4'],
		
	  ));
	  
	 if($store_id !=0)
	 $fieldset->addField('label_default_imagelinea4', 'checkbox', array(
          'label'     => Mage::helper('manufacturer')->__('User Default image line 4'),    
          'name'      => 'label_default_imagelinea4',
		  'checked'   => $this->_checkeds['imagelinea4'],
		  'onclick'   => 'doCheck(\'label_default_imagelinea4\',\'default_imagelinea4\',\'imagelinea4\')',
		  ));
//5
	if(isset($manufacturer_data['imageRunway']) && $manufacturer_data['imageRunway'])
	  {
	  
		  $fieldset->addField('old_imageRunway', 'hidden', array(
	          'label'     => Mage::helper('manufacturer')->__('Current Image Runway'),
	          'required'  => false,
	          'name'      => 'old_imageRunway',
			  'value'     =>$manufacturer_data['imageRunway'],
		  ));
	   }	  
	  
	  $fieldset->addField('imageRunway', 'image', array(
		  'label'     => Mage::helper('manufacturer')->__('Image Runway'),
		  'required'  => false,
		  'name'      => 'imageRunway',
		  'disabled'  => $this->_disableds['imageRunway'],
		
	  ));
	  
	 if($store_id !=0)
	 $fieldset->addField('label_default_imageRunway', 'checkbox', array(
          'label'     => Mage::helper('manufacturer')->__('User Default image Runway'),    
          'name'      => 'label_default_imageRunway',
		  'checked'   => $this->_checkeds['imageRunway'],
		  'onclick'   => 'doCheck(\'label_default_imageRunway\',\'default_imageRunway\',\'imageRunway\')',
		  ));
//fin de imagenes
			$fieldset->addField('idlinea1', 'text', array(
		          'label'     => Mage::helper('manufacturer')->__('ID linea 1'),         
		          'required'  => false,
		          'name'      => 'idlinea1',
				  'disabled'  => $this->_disableds['idlinea1'],
		      ));
			  
			 if($store_id !=0)
			 $fieldset->addField('label_default_idlinea1', 'checkbox', array(
		          'label'     => Mage::helper('manufacturer')->__('User Default ID linea 1'),    
		          'name'      => 'label_default_idlinea1',
				  'checked'   => $this->_checkeds['idlinea1'],
				  'onclick'   => 'doCheck(\'label_default_idlinea1\',\'default_idlinea1\',\'idlinea1\')',
				  ));	  	  

			 $fieldset->addField('default_idlinea1', 'hidden', array(  
		          'name'      => 'default_idlinea1',
				  ));	  



			$fieldset->addField('idlinea2', 'text', array(
		          'label'     => Mage::helper('manufacturer')->__('ID linea 2'),         
		          'required'  => false,
		          'name'      => 'idlinea2',
				  'disabled'  => $this->_disableds['idlinea2'],
		      ));
			  
			 if($store_id !=0)
			 $fieldset->addField('label_default_idlinea2', 'checkbox', array(
		          'label'     => Mage::helper('manufacturer')->__('User Default ID linea 2'),    
		          'name'      => 'label_default_idlinea2',
				  'checked'   => $this->_checkeds['idlinea2'],
				  'onclick'   => 'doCheck(\'label_default_idlinea2\',\'default_idlinea2\',\'idlinea2\')',
				  ));	  	  

			 $fieldset->addField('default_idlinea2', 'hidden', array(  
		          'name'      => 'default_idlinea2',
				  ));			



			$fieldset->addField('idlinea3', 'text', array(
		          'label'     => Mage::helper('manufacturer')->__('ID linea 3'),         
		          'required'  => false,
		          'name'      => 'idlinea3',
				  'disabled'  => $this->_disableds['idlinea3'],
		      ));
			  
			 if($store_id !=0)
			 $fieldset->addField('label_default_idlinea3', 'checkbox', array(
		          'label'     => Mage::helper('manufacturer')->__('User Default ID linea 3'),    
		          'name'      => 'label_default_idlinea3',
				  'checked'   => $this->_checkeds['idlinea3'],
				  'onclick'   => 'doCheck(\'label_default_idlinea3\',\'default_idlinea3\',\'idlinea3\')',
				  ));	  	  

			 $fieldset->addField('default_idlinea3', 'hidden', array(  
		          'name'      => 'default_idlinea3',
				  ));



		$fieldset->addField('idlinea4', 'text', array(
		          'label'     => Mage::helper('manufacturer')->__('ID linea 4'),         
		          'required'  => false,
		          'name'      => 'idlinea4',
				  'disabled'  => $this->_disableds['idlinea4'],
		      ));
			  
			 if($store_id !=0)
			 $fieldset->addField('label_default_idlinea4', 'checkbox', array(
		          'label'     => Mage::helper('manufacturer')->__('User Default ID linea 4'),    
		          'name'      => 'label_default_idlinea4',
				  'checked'   => $this->_checkeds['idlinea4'],
				  'onclick'   => 'doCheck(\'label_default_idlinea4\',\'default_idlinea4\',\'idlinea4\')',
				  ));	  	  

			 $fieldset->addField('default_idlinea4', 'hidden', array(  
		          'name'      => 'default_idlinea4',
				  ));

		$fieldset->addField('idsubcat', 'text', array(
		          'label'     => Mage::helper('manufacturer')->__('Sub cat outstanding'),         
		          'required'  => false,
		          'name'      => 'idsubcat',
				  'disabled'  => $this->_disableds['idsubcat'],
		      ));
			  
			 if($store_id !=0)
			 $fieldset->addField('label_default_idsubcat', 'checkbox', array(
		          'label'     => Mage::helper('manufacturer')->__('User Default Sub cat outstanding'),    
		          'name'      => 'label_default_idsubcat',
				  'checked'   => $this->_checkeds['idsubcat'],
				  'onclick'   => 'doCheck(\'label_default_idsubcat\',\'default_idsubcat\',\'idsubcat\')',
				  ));	  	  

			 $fieldset->addField('default_idsubcat', 'hidden', array(  
		          'name'      => 'default_idsubcat',
				  ));	
		$fieldset->addField('urlblog', 'text', array(
		          'label'     => Mage::helper('manufacturer')->__('Url Blog'),         
		          'required'  => false,
		          'name'      => 'urlblog',
				  'disabled'  => $this->_disableds['urlblog'],
		      ));
			  
			 if($store_id !=0)
			 $fieldset->addField('label_default_urlblog', 'checkbox', array(
		          'label'     => Mage::helper('manufacturer')->__('User Default Url Blog'),    
		          'name'      => 'label_default_urlblog',
				  'checked'   => $this->_checkeds['urlblog'],
				  'onclick'   => 'doCheck(\'label_default_urlblog\',\'default_urlblog\',\'urlblog\')',
				  ));	  	  

			 $fieldset->addField('default_urlblog', 'hidden', array(  
		          'name'      => 'default_urlblog',
				  ));	

//fin de añadir los campos que necesitamos para las lineas
	  $fieldset->addField('default_image', 'hidden', array(  
          'name'      => 'default_image',
		  ));		 
	  $fieldset->addField('default_imagelinea1', 'hidden', array(  
          'name'      => 'default_imagelinea1',
		  ));	 
	  $fieldset->addField('default_imagelinea2', 'hidden', array(  
          'name'      => 'default_imagelinea2',
		  ));	
	  $fieldset->addField('default_imagelinea3', 'hidden', array(  
          'name'      => 'default_imagelinea3',
		  ));	
	  $fieldset->addField('default_imagelinea4', 'hidden', array(  
          'name'      => 'default_imagelinea4',
		  ));	
	  $fieldset->addField('default_imageRunway', 'hidden', array(  
          'name'      => 'default_imageRunway',
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
          'label'     => Mage::helper('manufacturer')->__('The New Season'),
          'title'     => Mage::helper('manufacturer')->__('The New Season'),
          'style'     => 'width:700px; height:100px;',
          'wysiwyg'   => true,
          'required'  => true,
		  'disabled'  => $this->_disableds['description_short'],
      ));

	 if($store_id !=0)
	 $fieldset->addField('label_default_description_short', 'checkbox', array(
          'label'     => Mage::helper('manufacturer')->__('User Default The New Season'),    
          'name'      => 'label_default_description_short',
		  'checked'   => $this->_checkeds['description_short'],
		  'onclick'   => 'doCheck(\'label_default_description_short\',\'default_description_short\',\'description_short\')',
		  ));	  	  
		  
	 $fieldset->addField('default_description_short', 'hidden', array(  
          'name'      => 'default_description_short',
		  ));	

	  
	  $fieldset->addField('description_short2', 'editor', array(
          'name'      => 'description_short2',
          'label'     => Mage::helper('manufacturer')->__('The Icons'),
          'title'     => Mage::helper('manufacturer')->__('The Icons'),
          'style'     => 'width:700px; height:100px;',
          'wysiwyg'   => true,
          'required'  => false,
		  'disabled'  => $this->_disableds['description_short2'],
      ));

	 if($store_id !=0)
	 $fieldset->addField('label_default_description_short2', 'checkbox', array(
          'label'     => Mage::helper('manufacturer')->__('User Default The Icons'),    
          'name'      => 'label_default_description_short2',
		  'checked'   => $this->_checkeds['description_short2'],
		  'onclick'   => 'doCheck(\'label_default_description_short\',\'default_description_short\',\'description_short\')',
		  ));	  	  

	 $fieldset->addField('default_description_short2', 'hidden', array(  
          'name'      => 'default_description_short2',
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
	  if(isset($manufacturer_data['imagelinea1']) && $manufacturer_data['imagelinea1'])
	  {
		$manufacturer_data['old_imagelinea1'] =  $manufacturer_data['imagelinea1'];
		$manufacturer_data['imagelinea1'] =  Mage::helper('manufacturer')->getUrlImagePath($manufacturer_data['name']) .'/'. $manufacturer_data['imagelinea1'];
       // $manufacturer_data['image'] = '<img src="'.		$manufacturer_data['image']  .'" />';	  
	  }	
	  //nuevas imagenes
	  if(isset($manufacturer_data['imagelinea2']) && $manufacturer_data['imagelinea2'])
	  {
		$manufacturer_data['old_imagelinea2'] =  $manufacturer_data['imagelinea2'];
		$manufacturer_data['imagelinea2'] =  Mage::helper('manufacturer')->getUrlImagePath($manufacturer_data['name']) .'/'. $manufacturer_data['imagelinea2'];
       // $manufacturer_data['image'] = '<img src="'.		$manufacturer_data['image']  .'" />';	  
	  }	
	  if(isset($manufacturer_data['imagelinea3']) && $manufacturer_data['imagelinea3'])
	  {
		$manufacturer_data['old_imagelinea3'] =  $manufacturer_data['imagelinea3'];
		$manufacturer_data['imagelinea3'] =  Mage::helper('manufacturer')->getUrlImagePath($manufacturer_data['name']) .'/'. $manufacturer_data['imagelinea3'];
       // $manufacturer_data['image'] = '<img src="'.		$manufacturer_data['image']  .'" />';	  
	  }	
	  if(isset($manufacturer_data['imagelinea4']) && $manufacturer_data['imagelinea4'])
	  {
		$manufacturer_data['old_imagelinea4'] =  $manufacturer_data['imagelinea4'];
		$manufacturer_data['imagelinea4'] =  Mage::helper('manufacturer')->getUrlImagePath($manufacturer_data['name']) .'/'. $manufacturer_data['imagelinea4'];
       // $manufacturer_data['image'] = '<img src="'.		$manufacturer_data['image']  .'" />';	  
	  }	
	  if(isset($manufacturer_data['imageRunway']) && $manufacturer_data['imageRunway'])
	  {
		$manufacturer_data['old_imageRunway'] =  $manufacturer_data['imageRunway'];
		$manufacturer_data['imageRunway'] =  Mage::helper('manufacturer')->getUrlImagePath($manufacturer_data['name']) .'/'. $manufacturer_data['imageRunway'];
       // $manufacturer_data['image'] = '<img src="'.		$manufacturer_data['image']  .'" />';	  
	  }	
	 
	  $form->setValues($manufacturer_data);
	  
      return parent::_prepareForm();
  }
  
  function setStatus($data)
  {
	$arrFielName = array(0=>'name_store',1=>'page_title',2=>'image',3=>'idlinea1',4=>'idlinea2',5=>'idlinea3',6=>'idlinea4',7=>'idsubcat',8=>'featured',9=>'status',10=>'description',11=>'meta_description',12=>'meta_keywords',13=>'description_short',14=>'description_short2', 15=>'imagelinea1', 16=>'imagelinea2', 17=>'imagelinea3', 18=>'imagelinea4', 19=>'imageRunway', 20=>'urlblog');	
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