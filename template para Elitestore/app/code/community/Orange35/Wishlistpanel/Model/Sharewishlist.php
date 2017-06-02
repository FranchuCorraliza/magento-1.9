<?php
	class Orange35_Wishlistpanel_Model_Sharewishlist extends Mage_Core_Model_Abstract
{
	const XML_PATH_EMAIL_IDENTITY = "wishlist/email/email_identity";
	const XML_PATH_SEND_TO_CUSTOMER_EMAIL = "wishlist/email/email_template";
	const XML_PATH_REMINDER_IDENTITY = "wishlist/reminder/email_identity";
	const XML_PATH_SEND_TO_CUSTOMER_REMINDER = "wishlist/reminder/email_template";
	const XML_PATH_SEND_TO_CUSTOMER_LASTUNIT = "wishlist/reminder/lastunit_template";
	const XML_PATH_SEND_TO_CUSTOMER_RESTOCK = "wishlist/reminder/restock_template";
	public function _construct()
    {
        parent::_construct();
        $this->_init('wishlistpanel/sharewishlist');
    }
	public function sendmail($data) {
		$store = Mage::app()->getStore();
		$translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
		$template = Mage::getStoreConfig(self::XML_PATH_SEND_TO_CUSTOMER_EMAIL,$store->getId());
		$mailTemplate = Mage::getModel('core/email_template');
		$sender = Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY);
		$mailTemplate->setDesignConfig(array('area'=>'frontend', 'store'=>$store->getId()))
                ->sendTransactional(
                    $template,
					$sender,
					$data['email'],
					$data['name'],
					$data,
					$store->getId()
                );
		$translate->setTranslateInline(true);
		return $this;
		
	}
	
	public function sendReminder($data) {
		$store = Mage::app()->getStore();
		$translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
		$template = Mage::getStoreConfig(self::XML_PATH_SEND_TO_CUSTOMER_REMINDER,$store->getId());
		$mailTemplate = Mage::getModel('core/email_template');
		$sender = Mage::getStoreConfig(self::XML_PATH_REMINDER_IDENTITY);		
		$mailTemplate->setDesignConfig(array('area'=>'frontend', 'store'=>$store->getId()))
                ->sendTransactional(
                    $template,
					$sender,
					$data['customerEmail'],
					$data['customerName'],
					$data,
					$store->getId()
                );
		$translate->setTranslateInline(true);
		return $this;
	}
	
	public function sendLastUnitNotification($data){
		$store = Mage::app()->getStore();
		$translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
		$template = Mage::getStoreConfig(self::XML_PATH_SEND_TO_CUSTOMER_LASTUNIT,$store->getId());
		$mailTemplate = Mage::getModel('core/email_template');
		$sender = Mage::getStoreConfig(self::XML_PATH_REMINDER_IDENTITY);		
		$mailTemplate->setDesignConfig(array('area'=>'frontend', 'store'=>$store->getId()))
                ->sendTransactional(
                    $template,
					$sender,
					$data['customerEmail'],
					$data['customerName'],
					$data,
					$store->getId()
                );
		$translate->setTranslateInline(true);
		return $this;		
	}
	
	public function sendRestockNotification($data){
		$store = Mage::app()->getStore();
		$translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
		$template = Mage::getStoreConfig(self::XML_PATH_SEND_TO_CUSTOMER_RESTOCK,$store->getId());
		$mailTemplate = Mage::getModel('core/email_template');
		$sender = Mage::getStoreConfig(self::XML_PATH_REMINDER_IDENTITY);		
		$mailTemplate->setDesignConfig(array('area'=>'frontend', 'store'=>$store->getId()))
                ->sendTransactional(
                    $template,
					$sender,
					$data['customerEmail'],
					$data['customerName'],
					$data,
					$store->getId()
                );
		$translate->setTranslateInline(true);
		return $this;		
	}
}