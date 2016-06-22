<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */
class Belitsoft_Survey_Block_Adminhtml_Survey_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
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
	 * @return Belitsoft_Survey_Block_Adminhtml_Survey_Edit_Form
	 */
	protected function _prepareForm()
	{
		$survey = Mage::registry('survey');
		$id = $survey->getSurveyId();
		
		$form = new Varien_Data_Form();
		
		$fieldset = $form->addFieldset('base_fieldset',
			array(
				'legend'	=> $this->__('Survey details'), 
				'class'		=> 'fieldset-wide'
			)
		);
		
		$fieldset->addField('survey_name',
			'text',
			array(
				'name'		=> 'survey_name', 
				'label'		=> $this->__('Survey name'), 
				'title'		=> $this->__('Survey name'), 
				'required'	=> true,
			)
		);
		
		$fieldset->addField('is_active',
			'select', 
			array(
				'name'		=> 'is_active', 
				'label'		=> Mage::helper('cms')->__('Status'), 
				'title'		=> $this->__('Survey Status'), 
				'required'	=> true, 
				'options'	=> array (
					'1' => Mage::helper('cms')->__('Enabled'), 
					'0' => Mage::helper('cms')->__('Disabled')
				)
			)
		);

		$fieldset->addField('survey_url_key',
			'text',
			array(
				'name'		=> 'survey_url_key',
				'label'		=> $this->__('URL key'),
				'title'		=> $this->__('URL key'),
			)
		);
		
		$fieldset->addField('category_id',
			'select',
			array(
				'name'		=> 'categories[]', 
				'label'		=> $this->__('Survey category'), 
				'title'		=> $this->__('Survey category'), 
				'required'	=> true, 
				'values'	=> $this->_getCategoriesValuesForForm()
			)
		);
		
		$fieldset->addField('multipage',
			'select', 
			array(
				'name'		=> 'multipage', 
				'label'		=> $this->__('Multipage'), 
				'title'		=> $this->__('Multipage'), 
				'required'	=> true, 
				'options'	=> array (
					'0' => Mage::helper('cms')->__('Disabled'),
					'1' => Mage::helper('cms')->__('Enabled'), 
				)
			)
		);
		
		$fieldset->addField('start_date',
			'date', 
			array(
				'name'		=> 'start_date', 
				'label'		=> $this->__('Start on'), 
				'title'		=> $this->__('Start on'),
				'format'	=> Varien_Date::DATETIME_INTERNAL_FORMAT,
				'time'		=> true,
				'image'		=> $this->getSkinUrl('images/grid-cal.gif'),
				'style'		=> 'width:150px !important;',
			)
		);
		
		$fieldset->addField('expired_date',
			'date', 
			array(
				'name'		=> 'expired_date', 
				'label'		=> $this->__('Expired on'), 
				'title'		=> $this->__('Expired on'),
				'format'	=> Varien_Date::DATETIME_INTERNAL_FORMAT,
				'time'		=> true,
				'image'		=> $this->getSkinUrl('images/grid-cal.gif'),
				'style'		=> 'width:150px !important;',
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
			$survey->setStoreId(Mage::app()->getStore(true)->getId());
		}
		
		$fieldset->addField('survey_description',
			'editor', 
			array(
				'name'		=> 'survey_description', 
				'label'		=> $this->__('Survey Description'), 
				'title'		=> $this->__('Survey Description'), 
				'config'	=> Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
			)
		);
		
		$fieldset->addField('survey_final_page_text',
			'editor', 
			array(
				'name'		=> 'survey_final_page_text', 
				'label'		=> $this->__('Final Page Text'), 
				'title'		=> $this->__('Final Page Text'), 
				'config'	=> Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
			)
		);

		$fieldset->addField('survey_meta_description',
			'textarea',
			array(
				'name'		=> 'survey_meta_description',
				'label'		=> $this->__('Meta Description'),
				'title'		=> $this->__('Meta Description'),
			)
		);

		$fieldset->addField('survey_meta_keywords',
			'textarea',
			array(
				'name'		=> 'survey_meta_keywords',
				'label'		=> $this->__('Meta Keywords'),
				'title'		=> $this->__('Meta Keywords'),
			)
		);
		
		
		$start_date = $survey->getData('start_date');
		if(!$start_date || ($start_date == '0000-00-00 00:00:00')) {
			$survey->setData('start_date', date('Y-m-d 00:00:00', Mage::app()->getLocale()->storeTimeStamp()));
		}
		
		if($id) {
			$fieldset->addField('survey_id',
				'hidden',
				array(
					'name' => 'survey_id'
				)
			);
		} else {
			$survey->setData('survey_final_page_text', $this->__('End of the survey - Thank you for your time.'));
		}

		$form->setValues($survey->getData());
		
/*		if ($survey->getExpiredDate()) {
			$form->getElement('expired_date')->setValue(
				Mage::app()->getLocale()->date($survey->getExpiredDate(), Varien_Date::DATETIME_INTERNAL_FORMAT)
			);
		}
*/		
		
		$form->setUseContainer(true);
		$form->setAction($this->getSaveUrl());
		$form->setId('edit_form');
		$form->setMethod('post');
		
		$this->setForm($form);

		return parent::_prepareForm();
	}

	/**
	 * Helper function to load categories collection
	 *
	 */
	protected function _getCategoriesValuesForForm()
	{
		return Mage::getResourceModel('belitsoft_survey/category_collection')->toOptionArray();
	}
	
	public function getSaveUrl()
	{
		return $this->getUrl('*/*/save');
	}
}
