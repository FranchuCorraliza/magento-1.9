<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */
class Belitsoft_Survey_Block_Adminhtml_Category_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Prepares the page layout
	 * Loads the WYSIWYG editor on demand if enabled.
	 * 
	 * @return Belitsoft_Survey_Block_Admin_Edit
	 */
	protected function _prepareLayout()
	{
		$return = parent::_prepareLayout();
		if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
			$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
		}
		return $return;
	}
	
	/**
	 * Preperation of current form
	 *
	 * @return Belitsoft_Survey_Block_Adminhtml_Category_Edit_Form
	 */
	protected function _prepareForm()
	{
		$model = Mage::registry('survey_category');
		
		$form = new Varien_Data_Form();
		
		$fieldset = $form->addFieldset('base_fieldset',
			array(
				'legend'	=> $this->__('Category Details'), 
				'class'		=> 'fieldset-wide'
			)
		);
		
		if ($model->getCategoryId()) {
			$fieldset->addField('category_id',
				'hidden',
				array(
					'name' => 'category_id'
				)
			);
		}
		
		$fieldset->addField('category_name',
			'text',
			array(
				'name'		=> 'category_name', 
				'label'		=> $this->__('Category Name'), 
				'title'		=> $this->__('Category Name'), 
				'required'	=> true,
			)
		);

		$fieldset->addField('category_url_key',
			'text',
			array(
				'name'		=> 'category_url_key',
				'label'		=> $this->__('URL key'),
				'title'		=> $this->__('URL key'),
			)
		);
		
		$fieldset->addField('is_active',
			'select', 
			array(
				'name'		=> 'is_active', 
				'label'		=> Mage::helper('cms')->__('Status'), 
				'title'		=> $this->__('Category Status'), 
				'required'	=> true, 
				'options'	=> array (
					'1' => Mage::helper('cms')->__('Enabled'), 
					'0' => Mage::helper('cms')->__('Disabled')
				)
			)
		);

		$fieldset->addField('only_for_registered',
			'select', 
			array(
				'name'		=> 'only_for_registered', 
				'label'		=> $this->__('Only for registered'), 
				'title'		=> $this->__('Only for registered'), 
				'required'	=> true, 
				'values'	=> Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
			)
		);
       
		$customerGroups = Mage::helper('customer')
			->getGroups()
			->toOptionArray();
		
		$fieldset->addField('customer_group_ids',
			'multiselect',
			array(
				'name'      => 'customer_group_ids[]',
				'label'     => Mage::helper('catalogrule')->__('Customer Groups'),
				'title'     => Mage::helper('catalogrule')->__('Customer Groups'),
				'values'    => $customerGroups,
			)
		); 

		/**
		 * Check is single store mode
		 */
		if (!Mage::app()->isSingleStoreMode()) {
			$fieldset->addField('store_id',
				'multiselect', 
				array(
					'name'		=> 'stores[]', 
					'label'		=> Mage::helper('cms')->__('Store view'), 
					'title'		=> Mage::helper('cms')->__('Store view'), 
					'required'	=> true, 
					'values'	=> Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true)
				)
			);
		} else {
			$fieldset->addField('store_id',
				'hidden',
				array(
					'name'	=> 'stores[]', 
					'value'	=> Mage::app()->getStore(true)->getId()
				)
			);
			$model->setStoreId(Mage::app()->getStore(true)->getId());
		}

		$fieldset->addField('category_description',
			'editor', 
			array(
				'name'		=> 'category_description', 
				'label'		=> $this->__('Category Description'), 
				'title'		=> $this->__('Category Description'), 
				'config'	=> Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
			)
		);

		$fieldset->addField('category_meta_description',
			'textarea',
			array(
				'name'		=> 'category_meta_description',
				'label'		=> $this->__('Meta Description'),
				'title'		=> $this->__('Meta Description'),
			)
		);

		$fieldset->addField('category_meta_keywords',
			'textarea',
			array(
				'name'		=> 'category_meta_keywords',
				'label'		=> $this->__('Meta Keywords'),
				'title'		=> $this->__('Meta Keywords'),
			)
		);

		$form->setValues($model->getData());
		$form->setUseContainer(true);
		$form->setAction($this->getSaveUrl());
		$form->setId('edit_form');
		$form->setMethod('post');
		
		$this->setForm($form);

		return parent::_prepareForm();
	}
		
	public function getSaveUrl()
	{
		return $this->getUrl('*/*/save');
	}
}
