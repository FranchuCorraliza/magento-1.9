<?php 
class Magestore_Productcontact_Block_Adminhtml_Productcontact_Renderer_Productname extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
	public function render(Varien_Object $row)
    {
        
		return sprintf('<a href="%s">%s</a>',
			$this->getUrl('adminhtml/catalog_product/edit', array('_current'=>true, 'id'=>$row->getProductId())),
			$row->getProductName()
		);
    }
}