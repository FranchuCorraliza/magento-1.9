<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package	Belitsoft_Survey
 * @author	 Belitsoft <bits@belitsoft.com>
 */

class Belitsoft_Survey_Block_Adminhtml_Question_Grid_Column_Renderer_Html extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	/**
	 * Renders grid column
	 *
	 * @param Varien_Object $row
	 * @return string
	 */
	public function render(Varien_Object $row)
	{
		return strip_tags($row->getData($this->getColumn()->getIndex()));
	}
}
