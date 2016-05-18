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

// Marca destacada Si o No			
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
// Fin Marca destacada	 	  
// Estado de la marca	  
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
// Fin Estado de la marca

// Nuevo Diseñador
      $fieldset->addField('newdesigner', 'select', array(
          'label'     => Mage::helper('manufacturer')->__('New Designer'),
          'name'      => 'newdesigner',
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
		  'disabled'  => $this->_disableds['newdesigner'],
      ));
     
	 if($store_id !=0)
	 $fieldset->addField('label_default_newdesigner', 'checkbox', array(
          'label'     => Mage::helper('manufacturer')->__('User Default New Designer'),    
          'name'      => 'label_default_newdesigner',
		  'checked'   => $this->_checkeds['newdesigner'],
		  'onclick'   => 'doCheck(\'label_default_newdesigner\',\'default_newdesigner\',\'newdesigner\')',
		  ));	

	$fieldset->addField('default_newdesigner', 'hidden', array(  
          'name'      => 'default_newdesigner',
		  ));		 
// Fin Nuevo Diseñador

// Textos de la marca
	
	// Titulo descripcion 1
	$fieldset->addField('titulodesc1', 'text', array(
		          'label'     => Mage::helper('manufacturer')->__('Titulo descripcion bloque 1'),         
		          'required'  => false,
		          'name'      => 'titulodesc1',
				  'disabled'  => $this->_disableds['titulodesc1'],
		      ));
			  
			 if($store_id !=0)
			 $fieldset->addField('label_default_titulodesc1', 'checkbox', array(
		          'label'     => Mage::helper('manufacturer')->__('User Default descripcion bloque 1'),    
		          'name'      => 'label_default_titulodesc1',
				  'checked'   => $this->_checkeds['titulodesc1'],
				  'onclick'   => 'doCheck(\'label_default_titulodesc1\',\'default_titulodesc1\',\'titulodesc1\')',
				  ));	  	  

			 $fieldset->addField('default_titulodesc1', 'hidden', array(  
		          'name'      => 'default_titulodesc1',
				  ));	
	  
	  // Descripcion 1
	  $fieldset->addField('descripcion1', 'editor', array(
				  'name'      => 'descripcion1',
				  'label'     => Mage::helper('manufacturer')->__('Descripcion bloque 1'),
				  'title'     => Mage::helper('manufacturer')->__('Descripcion bloque 1'),
				  'style'     => 'width:700px; height:100px;',
				  'wysiwyg'   => true,
				  'required'  => false,
				  'disabled'  => $this->_disableds['descripcion1'],
      ));

	 if($store_id !=0)
		 $fieldset->addField('label_default_descripcion1', 'checkbox', array(
			  'label'     => Mage::helper('manufacturer')->__('User Default descripcion bloque 1'),    
			  'name'      => 'label_default_descripcion1',
			  'checked'   => $this->_checkeds['descripcion1'],
			  'onclick'   => 'doCheck(\'label_default_descripcion1\',\'default_descripcion1\',\'descripcion1\')',
			  ));	  	  

	 $fieldset->addField('default_descripcion1', 'hidden', array(  
          'name'      => 'default_descripcion1',
		  ));
		  
	//Titulo descripcion 2	  
	$fieldset->addField('titulodesc2', 'text', array(
		          'label'     => Mage::helper('manufacturer')->__('Titulo descripcion bloque 2'),         
		          'required'  => false,
		          'name'      => 'titulodesc2',
				  'disabled'  => $this->_disableds['titulodesc2'],
		      ));
			  
			 if($store_id !=0)
			 $fieldset->addField('label_default_titulodesc2', 'checkbox', array(
		          'label'     => Mage::helper('manufacturer')->__('User Default titulo descripcion bloque 2'),    
		          'name'      => 'label_default_titulodesc2',
				  'checked'   => $this->_checkeds['titulodesc2'],
				  'onclick'   => 'doCheck(\'label_default_titulodesc2\',\'default_titulodesc2\',\'titulodesc2\')',
				  ));	  	  

			 $fieldset->addField('default_titulodesc2', 'hidden', array(  
		          'name'      => 'default_titulodesc2',
				  ));	
	  
	  // Descripcion 2
	  $fieldset->addField('descripcion2', 'editor', array(
				  'name'      => 'descripcion2',
				  'label'     => Mage::helper('manufacturer')->__('Descripcion bloque 2'),
				  'title'     => Mage::helper('manufacturer')->__('Descripcion bloque 2'),
				  'style'     => 'width:700px; height:100px;',
				  'wysiwyg'   => true,
				  'required'  => false,
				  'disabled'  => $this->_disableds['descripcion2'],
      ));

	 if($store_id !=0)
		 $fieldset->addField('label_default_descripcion2', 'checkbox', array(
			  'label'     => Mage::helper('manufacturer')->__('User Default Descripcion bloque 2'),    
			  'name'      => 'label_default_descripcion2',
			  'checked'   => $this->_checkeds['descripcion2'],
			  'onclick'   => 'doCheck(\'label_default_descripcion2\',\'default_descripcion2\',\'descripcion2\')',
			  ));	  	  

	 $fieldset->addField('default_descripcion2', 'hidden', array(  
          'name'      => 'default_descripcion2',
		  ));
	  // The Icons
      $fieldset->addField('theicons', 'editor', array(
          'name'      => 'theicons',
          'label'     => Mage::helper('manufacturer')->__('The Icons'),
          'title'     => Mage::helper('manufacturer')->__('The icons'),
          'style'     => 'width:700px; height:100px;',
          'wysiwyg'   => true,
          'required'  => true,
		  'disabled'  => $this->_disableds['theicons'],
      ));

	 if($store_id !=0)
	 $fieldset->addField('label_default_theicons', 'checkbox', array(
          'label'     => Mage::helper('manufacturer')->__('User Default The Icons'),    
          'name'      => 'label_default_theicons',
		  'checked'   => $this->_checkeds['theicons'],
		  'onclick'   => 'doCheck(\'label_default_theicons\',\'default_theicons\',\'theicons\')',
		  ));	  	  
		  
	 $fieldset->addField('default_theicons', 'hidden', array(  
          'name'      => 'default_theicons',
		  ));	

	 // Descripciones de páginas Internas
	 $fieldset->addField('description', 'editor', array(
          'name'      => 'description',
          'label'     => Mage::helper('manufacturer')->__('Descripciones de paginas internas'),
          'title'     => Mage::helper('manufacturer')->__('Descripciones de paginas internas'),
          'style'     => 'width:700px; height:200px;',
          'wysiwyg'   => true,
          'required'  => true,
		  'disabled'  => $this->_disableds['description'],
      ));
	  
	 if($store_id !=0)
	 $fieldset->addField('label_default_description', 'checkbox', array(
          'label'     => Mage::helper('manufacturer')->__('User Default Descripciones de paginas internas'),    
          'name'      => 'label_default_description',
		  'checked'   => $this->_checkeds['description'],
		  'onclick'   => 'doCheck(\'label_default_description\',\'default_description\',\'description\')',
		  ));	  	  
		  
	 $fieldset->addField('default_description', 'hidden', array(  
          'name'      => 'default_description',
		  ));	

// Fin Descripciones

//añadimos los campos que necesitamos para las lineas de diseño ids e imagenes

	//1
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
		  
	 $fieldset->addField('default_imagelinea1', 'hidden', array(  
          'name'      => 'default_imagelinea1',
		  ));		
		  

//2
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
	  
	  $fieldset->addField('default_imagelinea2', 'hidden', array(  
          'name'      => 'default_imagelinea2',
		  ));	


//3
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
	  $fieldset->addField('default_imagelinea3', 'hidden', array(  
          'name'      => 'default_imagelinea3',
		  ));

//4
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
     $fieldset->addField('default_imagelinea4', 'hidden', array(  
          'name'      => 'default_imagelinea4',
		  ));	
		  
//5
	if(isset($manufacturer_data['imagerunway']) && $manufacturer_data['imagerunway'])
	  {
	  
		  $fieldset->addField('old_imagerunway', 'hidden', array(
	          'label'     => Mage::helper('manufacturer')->__('Current Image Runway'),
	          'required'  => false,
	          'name'      => 'old_imagerunway',
			  'value'     =>$manufacturer_data['imagerunway'],
		  ));
	   }	  
	  
	  $fieldset->addField('imagerunway', 'image', array(
		  'label'     => Mage::helper('manufacturer')->__('Image Runway'),
		  'required'  => false,
		  'name'      => 'imagerunway',
		  'disabled'  => $this->_disableds['imagerunway'],
		
	  ));
	  
	 if($store_id !=0)
	 $fieldset->addField('label_default_imagerunway', 'checkbox', array(
          'label'     => Mage::helper('manufacturer')->__('User Default image Runway'),    
          'name'      => 'label_default_imagerunway',
		  'checked'   => $this->_checkeds['imagerunway'],
		  'onclick'   => 'doCheck(\'label_default_imagerunway\',\'default_imagerunway\',\'imagerunway\')',
		  ));
	 $fieldset->addField('default_imagerunway', 'hidden', array(  
          'name'      => 'default_imagerunway',
		  ));	
//5
    if(isset($manufacturer_data['imagemanufacturer2']) && $manufacturer_data['imagemanufacturer2'])
      {
      
          $fieldset->addField('old_imagemanufacturer2', 'hidden', array(
              'label'     => Mage::helper('manufacturer')->__('Current Image logo'),
              'required'  => false,
              'name'      => 'old_imagemanufacturer2',
              'value'     =>$manufacturer_data['imagemanufacturer2'],
          ));
       }      
      
      $fieldset->addField('imagemanufacturer2', 'image', array(
          'label'     => Mage::helper('manufacturer')->__('Image logo'),
          'required'  => false,
          'name'      => 'imagemanufacturer2',
          'disabled'  => $this->_disableds['imagemanufacturer2'],
        
      ));
      
     if($store_id !=0)
     $fieldset->addField('label_default_imagemanufacturer2', 'checkbox', array(
          'label'     => Mage::helper('manufacturer')->__('User Default image Logo'),    
          'name'      => 'label_default_imagemanufacturer2',
          'checked'   => $this->_checkeds['imagemanufacturer2'],
          'onclick'   => 'doCheck(\'label_default_imagemanufacturer2\',\'default_imagemanufacturer2\',\'imagemanufacturer2\')',
          ));
     $fieldset->addField('default_imagemanufacturer2', 'hidden', array(  
          'name'      => 'default_imagemanufacturer2',
          ));
//fin de lineas de diseño

//Genero de la marca
		
		$fieldset->addField('genero', 'text', array(
		          'label'     => Mage::helper('manufacturer')->__('Genero de la marca'),         
		          'required'  => false,
		          'name'      => 'genero',
				  'disabled'  => $this->_disableds['genero'],
		      ));
			  
			 if($store_id !=0)
			 $fieldset->addField('label_default_genero', 'checkbox', array(
		          'label'     => Mage::helper('manufacturer')->__('User Default Genero de la marca'),    
		          'name'      => 'label_default_genero',
				  'checked'   => $this->_checkeds['genero'],
				  'onclick'   => 'doCheck(\'label_default_genero\',\'default_genero\',\'genero\')',
				  ));	  	  

			 $fieldset->addField('default_genero', 'hidden', array(  
		          'name'      => 'default_genero',
				  ));	

//fin genero

//Tipologia de la marca
		
		$fieldset->addField('tipologia', 'text', array(
		          'label'     => Mage::helper('manufacturer')->__('Tipologia de la marca'),         
		          'required'  => false,
		          'name'      => 'tipologia',
				  'disabled'  => $this->_disableds['topologia'],
		      ));
			  
			 if($store_id !=0)
			 $fieldset->addField('label_default_tipologia', 'checkbox', array(
		          'label'     => Mage::helper('manufacturer')->__('User Default Tipologia de la marca'),    
		          'name'      => 'label_default_tipologia',
				  'checked'   => $this->_checkeds['tipologia'],
				  'onclick'   => 'doCheck(\'label_default_tipologia\',\'default_tipologia\',\'tipologia\')',
				  ));	  	  

			 $fieldset->addField('default_tipologia', 'hidden', array(  
		          'name'      => 'default_tipologia',
				  ));	

//fin tipologia
	
//Subcategorias destacadas
		
		$fieldset->addField('idsubcat', 'text', array(
		          'label'     => Mage::helper('manufacturer')->__('Subcategorias destacadas'),         
		          'required'  => false,
		          'name'      => 'idsubcat',
				  'disabled'  => $this->_disableds['idsubcat'],
		      ));
			  
			 if($store_id !=0)
			 $fieldset->addField('label_default_idsubcat', 'checkbox', array(
		          'label'     => Mage::helper('manufacturer')->__('User Default Subcategorias destacadas'),    
		          'name'      => 'label_default_idsubcat',
				  'checked'   => $this->_checkeds['idsubcat'],
				  'onclick'   => 'doCheck(\'label_default_idsubcat\',\'default_idsubcat\',\'idsubcat\')',
				  ));	  	  

			 $fieldset->addField('default_idsubcat', 'hidden', array(  
		          'name'      => 'default_idsubcat',
				  ));	

//fin subcategorias destacadas

//categoria del blog asociada a la marca				  

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

//fin categoria del blog asociada a la marca




		  
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
	  if(isset($manufacturer_data['imagemanufacturer2']) && $manufacturer_data['imagemanufacturer2'])
      {
        $manufacturer_data['old_imagemanufacturer2'] =  $manufacturer_data['imagemanufacturer2'];
        $manufacturer_data['imagemanufacturer2'] =  Mage::helper('manufacturer')->getUrlImagePath($manufacturer_data['name']) .'/'. $manufacturer_data['imagemanufacturer2'];
       // $manufacturer_data['image'] = '<img src="'.       $manufacturer_data['image']  .'" />';     
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
	  if(isset($manufacturer_data['imagerunway']) && $manufacturer_data['imagerunway'])
	  {
		$manufacturer_data['old_imagerunway'] =  $manufacturer_data['imagerunway'];
		$manufacturer_data['imagerunway'] =  Mage::helper('manufacturer')->getUrlImagePath($manufacturer_data['name']) .'/'. $manufacturer_data['imagerunway'];
       // $manufacturer_data['image'] = '<img src="'.		$manufacturer_data['image']  .'" />';	  
	  }	
	 
	  $form->setValues($manufacturer_data);
	  
      return parent::_prepareForm();
  }
  
  function setStatus($data)
  {
	$arrFielName = array(0=>'image',1=>'featured',2=>'status',3=>'page_title',4=>'theicons',5=>'description',6=>'meta_keywords',7=>'meta_description',8=>'idlinea1',9=>'imagelinea1',10=>'idlinea2',11=>'imagelinea2',12=>'idlinea3',13=>'imagelinea3',14=>'idlinea4', 15=>'imagelinea4', 16=>'imagerunway', 17=>'urlblog', 18=>'idsubcat', 19=>'titulodesc1', 20=>'descripcion1', 21=>'titulodesc2', 22=>'descripcion2', 23=>'genero', 24=>'tipologia', 25=>'newdesigner', 26=>'imagemanufacturer2');	
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