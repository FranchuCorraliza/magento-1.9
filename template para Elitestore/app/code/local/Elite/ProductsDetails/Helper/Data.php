<?php
class Elite_ProductsDetails_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getAdminRecipientEmail(){
		$recipientEmails = array();
		$emailstring = Mage::getStoreConfig('product_info/email_config/email_receive_contact_form', Mage::app()->getStore()->getId());
		// var_dump($recipientEmails);die();
		$recipientEmails	 = explode(",", $emailstring);
		return $recipientEmails;
	}
}
	 