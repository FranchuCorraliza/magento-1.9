<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */
class Belitsoft_Survey_Block_Adminhtml_Question_Edit_Fields_Pickone extends Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset_Element
{
	public function __construct()
	{
		$this->setTemplate('survey/question/edit/fields/pickone.phtml');
	}
	
	public function getQuestionType()
	{
		return 'pickone';
	}

	public function getFieldId()
	{
		return $this->getParentBlock()->getFieldId();
	}
	
	public function getFieldTopId()
	{
		return $this->getParentBlock()->getFieldTopId();
	}
	
	public function getFieldName()
	{
		return $this->getParentBlock()->getFieldName();
	}

	public function getAddButtonHtml()
	{
		return $this->getParentBlock()->getAddButtonHtml();
	}

	public function getDeleteButtonHtml()
	{
		return $this->getParentBlock()->getDeleteButtonHtml();
	}
	
	public function getFieldTextString()
	{
		return 'field_text';
	}
	
	public function getSortOrderString()
	{
		return 'sort_order';
	}
	
	public function getFieldOptionField()
	{
		$field = new Varien_Data_Form_Element_Text();
		$field->setName($this->getFieldName().'[{{index}}]['.$this->getFieldTextString().']')
			->setId($this->getFieldId().'_{{index}}_'.$this->getFieldTextString())
			->setStyle('width:300px!important;')
			->setForm(new Varien_Data_Form())
			->setAfterElementHtml($this->getIdHiddenField().$this->getDeleteHiddenField());
		
		return $this->toJSTmplHtml($field);
	}
	
	public function getFieldSortOrderField()
	{
		$field = new Varien_Data_Form_Element_Text();
		$field->setName($this->getFieldName().'[{{index}}]['.$this->getSortOrderString().']')
			->setId($this->getFieldId().'_{{index}}_'.$this->getSortOrderString())
			->setClass('validate-digits')
//			->setStyle('width:30px!important;')
			->setForm(new Varien_Data_Form());
		
		return $this->toJSTmplHtml($field);
	}
	
	public function getDeleteHiddenField()
	{
		$hidden = new Varien_Data_Form_Element_Hidden();
		$hidden->setName($this->getFieldName().'[{{index}}][delete]')
			->addClass('delete')
			->setNoSpan(true)
			->setForm(new Varien_Data_Form());

		return $this->toJSTmplHtml($hidden);
	}
		
	public function getIdHiddenField()
	{
		$hidden = new Varien_Data_Form_Element_Hidden();
		$hidden->setName($this->getFieldName().'[{{index}}][field_id]')
			->setId($this->getFieldId().'_{{index}}_field_id')
			->setNoSpan(true)
			->setForm(new Varien_Data_Form());
		
		return $this->toJSTmplHtml($hidden);
	}
	
	public function toJSTmplHtml($el)
	{
		return str_replace(array("\r","\n"), '', $el->toHtml());
	}
}
