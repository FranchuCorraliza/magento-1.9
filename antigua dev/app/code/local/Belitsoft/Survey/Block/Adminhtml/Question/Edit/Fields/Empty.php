<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */
class Belitsoft_Survey_Block_Adminhtml_Question_Edit_Fields_Empty extends Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset_Element
{
	public function __construct()
	{
		$this->setTemplate('survey/question/edit/fields/empty.phtml');
	}
}
