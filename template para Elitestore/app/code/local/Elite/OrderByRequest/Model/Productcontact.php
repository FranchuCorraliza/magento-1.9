<?php

class Elite_OrderByRequest_Model_Productcontact extends Mage_Core_Model_Abstract
{
	
	const XML_PATH_EMAIL_IDENTITY = "product_info/orderbyrequest_config/email_obr_sender";
	const XML_PATH_GENERAL_EMAIL_IDENTITY = "trans_email/ident_general";
	const XML_PATH_ADMIN_EMAIL_IDENTITY = "product_info/orderbyrequest_config/email_obr_receive_contact_form";
	const XML_PATH_SEND_TO_CUSTOMER_EMAIL = "product_info/orderbyrequest_config/email_obr_template_sent_to_customer";
	const XML_PATH_SEND_TO_ADMIN_EMAIL = "product_info/orderbyrequest_config/email_obr_template_sent_to_admin";
	
	public function _construct()
    {
        parent::_construct();
        $this->_init('orderbyrequest/productcontact');
    }
	
	public function sendmailtocustomer($data) {
		$store = Mage::app()->getStore();
		$translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);
		$template = Mage::getStoreConfig(self::XML_PATH_SEND_TO_CUSTOMER_EMAIL,$store->getId());
		$mailTemplate = Mage::getModel('core/email_template');
		/*codigo mio*/
        $senderName = Mage::getStoreConfig('trans_email/ident_support/name');
        $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
        $sender = array('name' => $senderName, 'email' => $senderEmail);
        // Set recepient information
        $recepientEmail = $data['cemail'];
        $recepientName = $data['cname']; 
        /*fin codigo mio*/
		$mailTemplate->setDesignConfig(array('area'=>'frontend', 'store'=>$store->getId()))
                ->sendTransactional(
                    $template,
					$sender,
					$recepientEmail,
					$recepientName,
					$data,
					$store->getId()
                );
		$translate->setTranslateInline(true);
		return $this;
	}
	
	public function sendmailtoadmin($data) {
		$store = Mage::app()->getStore();
		$translate = Mage::getSingleton('core/translate');
		$translate->setTranslateInline(false);
		$template = Mage::getStoreConfig(self::XML_PATH_SEND_TO_ADMIN_EMAIL,$store->getId());
		$mailTemplate = Mage::getModel('core/email_template');
		$adminRecipients = Mage::helper('orderbyrequest')->getAdminRecipientEmail();
		if(count($adminRecipients)) {
			foreach($adminRecipients as $adminRecipient) {
				$sendTo[] = array('email'=>$adminRecipient,
								  'name'=>null
								);
			}
		} else {
			$recipient = Mage::getStoreConfig(self::XML_PATH_GENERAL_EMAIL_IDENTITY, $store->getId());
			 $sendTo = array(
				array(
					'email' => $recipient['email'],
					'name'  => $recipient['name']
				)
			);
		}

		foreach ($sendTo as $recipient) {
		    $mailTemplate->setDesignConfig(array('area'=>'frontend', 'store'=>$store->getId()))
                ->sendTransactional(
                    $template,
                    Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY, $store->getId()),
                    $recipient['email'],
                    $recipient['name'],
                    $data,
					$store->getId()
                );
		}
		$translate->setTranslateInline(true);
		return $this;
	}
}