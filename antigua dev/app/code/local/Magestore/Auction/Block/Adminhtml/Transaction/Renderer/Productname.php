<?php 
class Magestore_Auction_Block_Adminhtml_Transaction_Renderer_Productname
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	/* Render Grid Column*/
	public function render(Varien_Object $row) 
	{
		return sprintf('
			<a href="%s" title="%s">%s</a>',
			$this->getUrl('auctionadmin/adminhtml_productauction/edit/', array('_current'=>true, 'id' => $row->getProductauctionId())),
			Mage::helper('catalog')->__('View Product Auction Detail'),
			$row->getProductName()
		);
	}
}