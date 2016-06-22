<?php
class Wyomind_Ordersexporttool_Block_Adminhtml_Profiles_Renderer_Type extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
		$types=array('none','xml','txt','csv','tsv','din');
		return $types[$row->getFile_type()];
	}

}