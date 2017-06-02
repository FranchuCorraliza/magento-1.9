<?php
class Elite_OrderByRequest_Helper_Data extends Mage_Core_Helper_Abstract
{
		public function getAdminRecipientEmail(){
			$recipientEmails = array();
			$emailstring = Mage::getStoreConfig('product_info/orderbyrequest_config/email_obr_receive_contact_form', Mage::app()->getStore()->getId());
			$recipientEmails	 = explode(",", $emailstring);
			return $recipientEmails;
	}
}
	 