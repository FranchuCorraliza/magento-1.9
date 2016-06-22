<?php

class Customex_Orderrequest_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getCountryOption() {
		$countryCollection = Mage::getModel('directory/country')->getCollection()
			;
		$countryOption = array();
		if(count($countryCollection)) {
			foreach($countryCollection as $country) {
				$countryOption[$country->getId()] = $country->getName();
			}
		}
		return $countryOption;
	}
	
	public function isActive() {
		return Mage::getStoreConfig('orderrequest/general/is_active', $this->getStore()->getId());
	}
	
	public function isShowCompanyName() {
		return Mage::getStoreConfig('orderrequest/contact_form/is_show_company_name', $this->getStore()->getId());
	}
	
	public function isShowPersonalName() {
		return Mage::getStoreConfig('orderrequest/contact_form/is_show_personal_name', $this->getStore()->getId());
	}
	
	public function isShowAddress() {
		return Mage::getStoreConfig('orderrequest/contact_form/is_show_address', $this->getStore()->getId());
	}
	
	public function isShowZipcode() {
		return Mage::getStoreConfig('orderrequest/contact_form/is_show_zipcode', $this->getStore()->getId());
	}
	
	public function isShowCity() {
		return Mage::getStoreConfig('orderrequest/contact_form/is_show_city', $this->getStore()->getId());
	}
	
	public function isShowCountry() {
		return Mage::getStoreConfig('orderrequest/contact_form/is_show_country', $this->getStore()->getId());
	}
	
	public function isShowPhone() {
		return Mage::getStoreConfig('orderrequest/contact_form/is_show_phone', $this->getStore()->getId());
	}
	
	public function isShowFax() {
		return Mage::getStoreConfig('orderrequest/contact_form/is_show_fax', $this->getStore()->getId());
	}
	
	public function isShowEmail() {
		return Mage::getStoreConfig('orderrequest/contact_form/is_show_email', $this->getStore()->getId());
	}
	
	public function isShowWebsite() {
		return Mage::getStoreConfig('orderrequest/contact_form/is_show_website', $this->getStore()->getId());
	}
	
	public function isShowDetail() {
		return Mage::getStoreConfig('orderrequest/contact_form/is_show_detail', $this->getStore()->getId());
	}
	
	public function isRequireCompanyName() {
		return Mage::getStoreConfig('orderrequest/contact_form/is_require_company_name', $this->getStore()->getId());
	}
	
	public function isRequirePersonalName() {
		return Mage::getStoreConfig('orderrequest/contact_form/is_require_personal_name', $this->getStore()->getId());
	}
	
	public function isRequireAddress() {
		return Mage::getStoreConfig('orderrequest/contact_form/is_require_address', $this->getStore()->getId());
	}
	
	public function isRequireZipcode() {
		return Mage::getStoreConfig('orderrequest/contact_form/is_require_zipcode', $this->getStore()->getId());
	}
	
	public function isRequireCity() {
		return Mage::getStoreConfig('orderrequest/contact_form/is_require_city', $this->getStore()->getId());
	}
	
	public function isRequireCountry() {
		return Mage::getStoreConfig('orderrequest/contact_form/is_require_country', $this->getStore()->getId());
	}
	
	public function isRequirePhone() {
		return Mage::getStoreConfig('orderrequest/contact_form/is_require_phone', $this->getStore()->getId());
	}
	
	public function isRequireFax() {
		return Mage::getStoreConfig('orderrequest/contact_form/is_require_fax', $this->getStore()->getId());
	}
	
	public function isRequireEmail() {
		return Mage::getStoreConfig('orderrequest/contact_form/is_require_email', $this->getStore()->getId());
	}
	
	public function isRequireWebsite() {
		return Mage::getStoreConfig('orderrequest/contact_form/is_require_website', $this->getStore()->getId());
	}
	
	public function isRequireDetail() {
		return Mage::getStoreConfig('orderrequest/contact_form/is_require_detail', $this->getStore()->getId());
	}
	
	
	public function getErrorMessage() {
		return Mage::getStoreConfig('orderrequest/contact_form/message_error', $this->getStore()->getId());
	}
	
	public function getSuccessMessage() {
		return Mage::getStoreConfig('orderrequest/contact_form/message_success', $this->getStore()->getId());
	}
	
	
	public function getStore() {
		return Mage::app()->getStore();
	}
	
	public function getAdminRecipientEmail(){
		$recipientEmails = array();
		$emailstring = Mage::getStoreConfig('orderrequest/email_config/email_receive_contact_form', $this->getStore()->getId());
		// var_dump($recipientEmails);die();
		$recipientEmails	 = explode(",", $emailstring);
		return $recipientEmails;
	}
}