<?php 
class Magestore_Auction_Block_Adminhtml_Productauction_Renderer_Customer
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	/* Render Grid Column*/
	public function render(Varien_Object $row) 
	{
		if($row->getCustomerId())
			return sprintf('
				<a href="%s" title="%s">%s</a>',
				$this->getUrl('adminhtml/customer/edit/', array('_current'=>true, 'id' => $row->getCustomerId())),
				Mage::helper('catalog')->__('View Customer Detail'),
				$row->getCustomerName()
			);
		else
			return sprintf('%s',
				$row->getCustomerEmail()
			);	
	}
}