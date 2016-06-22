<?php 
class Customex_Orderrequest_Block_Adminhtml_Orderrequest_Renderer_Customeremail extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
	public function render(Varien_Object $row)
    {
        $customer_email = $row->getCustomerEmail();
		$customer = Mage::getModel('customer/customer')->getCollection()->addFieldToFilter('email',$customer_email)->getFirstItem();
		// var_dump($customer->getId());die();
		if($customer->getId()) {
			return sprintf('<a href="%s">%s</a>',
				$this->getUrl('adminhtml/customer/edit', array('_current'=>true, 'id'=>$customer->getId())),
				$customer_email
			);
		} else {
			return sprintf('%s',
				$customer_email
			);
		}
    }
}