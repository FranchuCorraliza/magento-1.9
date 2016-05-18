<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */
class Belitsoft_Survey_Block_Adminhtml_Question_Edit_Fields_Ranking extends Belitsoft_Survey_Block_Adminhtml_Question_Edit_Fields_Pickone
{
	public function __construct()
	{
		$this->setTemplate('survey/question/edit/fields/ranking.phtml');
	}
	
	protected function _prepareLayout()
	{
		$this->setChild('add_rank_button',
			$this->getLayout()
				->createBlock('adminhtml/widget_button')
				->setData(
					array(
						'label'		=> $this->__('Add New Rank'),
						'class'		=> 'add',
						'id'		=> 'add_new_rank',
						'on_click'	=> 'bitsSurveyRanks.add()'
					)
				)
		);

		$this->setChild('delete_rank_button',
			$this->getLayout()
				->createBlock('adminhtml/widget_button')
				->setData(
					array(
						'label'		=> $this->__('Remove'),
						'class'		=> 'delete delete-product-option',
						'on_click'	=> 'bitsSurveyRanks.remove(event)'
					)
				)
		);
		
		$ranks = array();
		if(Mage::registry('survey_question') && Mage::registry('survey_question')->getId()) {
			$ranks = Mage::registry('survey_question')->getRanks();
		}
		$this->setRanks($ranks);
	}

	public function getRankId()
	{
		return 'survey_rank';
	}
	
	public function getRankTopId()
	{
		return 'survey_rank_top';
	}
	
	public function getRankName()
	{
		return 'ranks';
	}
		
	public function getAddRankButtonHtml()
	{
		return $this->getChildHtml('add_rank_button');
	}
	
	public function getDeleteRankButtonHtml()
	{
		return $this->getChildHtml('delete_rank_button');
	}
	
	public function getRankOptionField()
	{
		$field = new Varien_Data_Form_Element_Text();
		$field->setName($this->getRankName().'[{{index}}]['.$this->getFieldTextString().']')
			->setId($this->getRankId().'_{{index}}_'.$this->getFieldTextString())
			->setStyle('width:300px!important;')
			->setForm(new Varien_Data_Form())
			->setAfterElementHtml($this->getRankIdHiddenField().$this->getRankDeleteHiddenField());
		
		return $this->toJSTmplHtml($field);
	}
	
	public function getRankSortOrderField()
	{
		$field = new Varien_Data_Form_Element_Text();
		$field->setName($this->getRankName().'[{{index}}]['.$this->getSortOrderString().']')
			->setId($this->getRankId().'_{{index}}_'.$this->getSortOrderString())
			->setClass('validate-digits')
			->setForm(new Varien_Data_Form());
		
		return $this->toJSTmplHtml($field);
	}
	
	public function getRankDeleteHiddenField()
	{
		$hidden = new Varien_Data_Form_Element_Hidden();
		$hidden->setName($this->getRankName().'[{{index}}][delete]')
			->addClass('delete')
			->setNoSpan(true)
			->setForm(new Varien_Data_Form());

		return $this->toJSTmplHtml($hidden);
	}
		
	public function getRankIdHiddenField()
	{
		$hidden = new Varien_Data_Form_Element_Hidden();
		$hidden->setName($this->getRankName().'[{{index}}][field_id]')
			->setId($this->getRankId().'_{{index}}_field_id')
			->setNoSpan(true)
			->setForm(new Varien_Data_Form());
		
		return $this->toJSTmplHtml($hidden);
	}
}
