<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */
class Belitsoft_Survey_Block_Adminhtml_Question_Edit_Fields_Pickmany extends Belitsoft_Survey_Block_Adminhtml_Question_Edit_Fields_Pickone
{
	public function getQuestionType()
	{
		return 'pickmany';
	}
}
