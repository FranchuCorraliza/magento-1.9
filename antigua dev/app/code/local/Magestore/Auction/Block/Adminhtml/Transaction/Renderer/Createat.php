<?php 
class Magestore_Auction_Block_Adminhtml_Transaction_Renderer_Createat
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	/* Render Grid Column*/
	public function render(Varien_Object $row) 
	{
		return sprintf('
			%s',
			
			Mage::helper('core')->formatTime($row->getCreatedDate(), "long")
		);
	}
}