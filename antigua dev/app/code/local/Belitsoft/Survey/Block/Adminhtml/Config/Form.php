<?php
/**
 * Mageplace Survey
 *
 * @category	Belitsoft
 * @package		Belitsoft_Survey
 * @copyright	Copyright (c) 2012 Mageplace. (http://www.mageplace.com)
 * @license		http://www.mageplace.com/disclaimer.html
 */

class Belitsoft_Survey_Block_Adminhtml_Config_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();

		$configData = $this->getConfigData();

		$general_fieldset = $form->addFieldset('general_fieldset', array(
			'legend'	=> $this->__('General settings')
		));
		
		$general_fieldset->addField('survey_url_prefix',
			'text',
			array(
				'name'		=> 'survey_url_prefix',
				'label'		=> $this->__('Survey URL Prefix'),
				'title'		=> $this->__('Survey URL Prefix'),
				'style'		=> 'width:200px!important;',
				'value'		=> @$configData['url_prefix'],
			)
		);
		
		$general_fieldset->addField('survey_enable_user_check',
			'select',
			array(
				'label'		=> $this->__('Enable user check for multiple answers'),
				'title'		=> $this->__('Enable user check for multiple answers'),
				'name'		=> 'survey_enable_user_check',
				'values'	=> Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
				'value'		=> (empty($configData['enable_user_check']) ? 0 : 1),
			)
		);
		
		$general_fieldset->addField('survey_cookie_lifetime',
			'text',
			array(
				'name'		=> 'survey_cookie_lifetime',
				'label'		=> $this->__('Cookie Lifetime'),
				'title'		=> $this->__('Cookie Lifetime'),
				'class'		=> 'validate-digits',
				'style'		=> 'width:30px!important;',
				'note'      => $this->__("Insert 0 for infinite lifetime"),
				'after_element_html' => '(days)',
				'value'		=> intval(@$configData['cookie_lifetime']),
			)
		);
		
		$graphic_fieldset = $form->addFieldset('graphic_fieldset', array(
			'legend'	=> $this->__('Results graphic settings')
		));
		
		$graphic_fieldset->addField('survey_graphic_type',
			'select',
			array(
				'label'		=> $this->__('Graphic Type'),
				'title'		=> $this->__('Graphic Type'),
				'name'		=> 'survey_graphic_type',
				'value'		=> (empty($configData['graphic_type']) ? 'Pie' : $configData['graphic_type']),
				'options'	=> array (
					'Pie' => $this->__('Pie'), 
					'Bar' => $this->__('Bar')
				)
			)
		);
		
		
		$pdf_fieldset = $form->addFieldset('pdf_fieldset', array(
			'legend'	=> $this->__('PDF settings')
		));
		
		$pdf_fieldset->addField('survey_pdf_font',
			'select',
			array(
				'label'		=> $this->__('PDF font'),
				'title'		=> $this->__('PDF font'),
				'name'		=> 'survey_pdf_font',
				'value'		=> @$configData['pdf_font'],
				'options'	=> array (
					'freeserif'		=> $this->__('FreeSerif'), 
					'freesans'		=> $this->__('FreeSans'),
					'dejavusans'	=> $this->__('DejaVuSans'),
					'helvetica'		=> $this->__('Helvetica'),
				)
			)
		);
		
		$customer_fieldset = $form->addFieldset('customer_fieldset', array(
			'legend'	=> $this->__('Customer settings')
		));
		
		$customer_fieldset->addField('survey_enable_user_view',
			'select',
			array(
				'label'		=> $this->__('Allow customers view survey result'),
				'title'		=> $this->__('Allow customers view survey result'),
				'name'		=> 'survey_enable_user_view',
				'values'	=> Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
				'value'		=> (empty($configData['enable_user_view']) ? 0 : 1),
			)
		);
		
		$customer_fieldset->addField('survey_enable_user_edit',
			'select',
			array(
				'label'		=> $this->__('Allow customers edit taken survey'),
				'title'		=> $this->__('Allow customers edit taken survey'),
				'name'		=> 'survey_enable_user_edit',
				'values'	=> Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
				'value'		=> (empty($configData['enable_user_edit']) ? 0 : 1),
			)
		);
		
		$meta_fieldset = $form->addFieldset('meta_fieldset', array(
			'legend'	=> $this->__('Metadata Information')
		));

		$meta_fieldset->addField('survey_meta_description',
			'textarea',
			array(
				'name'		=> 'survey_meta_description',
				'label'		=> $this->__('Meta Description'),
				'title'		=> $this->__('Meta Description'),
				'value'		=> @$configData['meta_description'],
			)
		);

		$meta_fieldset->addField('survey_meta_keywords',
			'textarea',
			array(
				'name'		=> 'survey_meta_keywords',
				'label'		=> $this->__('Meta Keywords'),
				'title'		=> $this->__('Meta Keywords'),
				'value'		=> @$configData['meta_keywords'],
			)
		);
		
		$form->setUseContainer(true);
		$form->setId('edit_form');
		$form->setMethod('post');
		$form->setAction($this->getSaveUrl());

		$this->setForm($form);
	}
	
	public function getConfigData()
	{
		return Mage::registry('survey_config_data');
	}

	public function getSaveUrl()
	{
		return $this->getUrl('*/*/save');
	}
}
