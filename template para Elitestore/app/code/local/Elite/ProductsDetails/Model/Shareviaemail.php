<?php

class Elite_ProductsDetails_Model_Shareviaemail extends Mage_Core_Model_Abstract
{
	
	const XML_PATH_EMAIL_IDENTITY = "product_info/share_via_email_config/email_sve_sender";
	const XML_PATH_GENERAL_EMAIL_IDENTITY = "trans_email/ident_general";
	const XML_PATH_SEND_TO_CUSTOMER_EMAIL = "product_info/share_via_email_config/email_sve_template_sent_to_customer";
	
	public function _construct()
    {
        parent::_construct();
        $this->_init('productsdetails/shareviaemail');
    }
	
	public function sendMail($data) {
		$store = Mage::app()->getStore();
		
		$translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);
		$template = Mage::getStoreConfig(self::XML_PATH_SEND_TO_CUSTOMER_EMAIL,$store->getId());
		$mailTemplate = Mage::getModel('core/email_template');
		$recipient = Mage::getStoreConfig(self::XML_PATH_GENERAL_EMAIL_IDENTITY, $store->getId());
		$sender = array(
					'email' => $recipient['email'],
					'name'  => $recipient['name']
					);
		$mailTemplate->setDesignConfig(array('area'=>'frontend', 'store'=>$store->getId()))
                ->sendTransactional(
                    $template,
					$sender,
					$data['friendemail'],
					$data['friendname'],
					$data,
					$store->getId()
                );
		$translate->setTranslateInline(true);
		return $this;
	}
	
}