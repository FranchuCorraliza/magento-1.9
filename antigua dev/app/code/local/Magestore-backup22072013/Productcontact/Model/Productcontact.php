<?php

class Magestore_Productcontact_Model_Productcontact extends Mage_Core_Model_Abstract
{
	const XML_PATH_EMAIL_IDENTITY = "productcontact/email_config/email_sender";
	const XML_PATH_GENERAL_EMAIL_IDENTITY = "trans_email/ident_general";
	const XML_PATH_ADMIN_EMAIL_IDENTITY = "productcontact/email_config/email_receive_contact_form";
	const XML_PATH_SEND_TO_CUSTOMER_EMAIL = "productcontact/email_config/email_template_sent_to_customer";
	const XML_PATH_SEND_TO_ADMIN_EMAIL = "productcontact/email_config/email_template_sent_to_admin";
	
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('productcontact/productcontact');
    }
	
	public function sendmailtocustomer($model, $productcontact) {
		$store = Mage::app()->getStore();
		
		$translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);
		$template = Mage::getStoreConfig(self::XML_PATH_SEND_TO_CUSTOMER_EMAIL,$store->getId());
		$mailTemplate = Mage::getModel('core/email_template');
		
		
		
		$this->setCustomerEmail($model->getCustomerEmail())
				->setCustomerName($model->getPersonalName())
				->setProductId($model->getProductId())
				->setProductName($model->getProductName())
				->setCreatedTime(Mage::helper('core')->formatDate($model->getCreatedTime(), 'medium', true))
				->setCustomerInformation($productcontact)
				;	
		
		 $sendTo = array(
            array(
                'email' => $model->getCustomerEmail(),
                'name'  => $model->getCustomerName()
            )
        );
		
		foreach ($sendTo as $recipient) {
            $mailTemplate->setDesignConfig(array('area'=>'frontend', 'store'=>$store->getId()))
                ->sendTransactional(
                    $template,
                    Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY, $store->getId()),
                    $recipient['email'],
                    $recipient['name'],
                    array(
						'productcontact'			=> $this,
						'store'						=> $store,
                    )
                );
		}
		
		// var_dump($mailTemplate->getProcessedTemplate(array(
						// 'productcontact'			=> $this,
						// 'store'						=> $store,
                    // )));die('xafd');
		
		$translate->setTranslateInline(true);
			
		return $this;
	}
	
	public function sendmailtoadmin($model, $productcontact) {
		$store = Mage::app()->getStore();
		
		$translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);
		$template = Mage::getStoreConfig(self::XML_PATH_SEND_TO_ADMIN_EMAIL,$store->getId());
		$mailTemplate = Mage::getModel('core/email_template');
		
		$productcontactUrl = Mage::getUrl('productcontact/adminhtml_productcontact/view', array('_current'=>true, 'id'=>$model->getId()));
		
		$this->setCustomerEmail($model->getCustomerEmail())
				->setCustomerName($model->getPersonalName())
				->setProductId($model->getProductId())
				->setProductName($model->getProductName())
				->setCreatedTime(Mage::helper('core')->formatDate($model->getCreatedTime(), 'medium', true))
				->setProductcontactUrl($productcontactUrl)
				->setCustomerInformation($productcontact)
				;	
		$adminRecipients = Mage::helper('productcontact')->getAdminRecipientEmail();
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
		
		// var_dump($sendTo);die();
		
		foreach ($sendTo as $recipient) {
            $mailTemplate->setDesignConfig(array('area'=>'frontend', 'store'=>$store->getId()))
                ->sendTransactional(
                    $template,
                    Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY, $store->getId()),
                    $recipient['email'],
                    $recipient['name'],
                    array(
						'productcontact'			=> $this,
						'store'						=> $store,
                    )
                );
						// var_dump($mailTemplate->getProcessedTemplate(array(
						// 'productcontact'			=> $this,
						// 'store'						=> $store,
                    // )));die('xafd');
		}
		

		
		$translate->setTranslateInline(true);
			
		return $this;
	}
}