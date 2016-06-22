<?php 
class Magestore_Auction_Block_Adminhtml_Productauction_Renderer_Product
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	/* Render Grid Column*/
	public function render(Varien_Object $row) 
	{
		
			return sprintf('
				<a href="%s" title="%s">%s</a>',
				$this->getUrl('adminhtml/catalog_product/edit/', array('_current'=>true, 'id' => $row->getProductId())),
				Mage::helper('catalog')->__('View Product Detail'),
				$row->getProductName()
			);
	}
}