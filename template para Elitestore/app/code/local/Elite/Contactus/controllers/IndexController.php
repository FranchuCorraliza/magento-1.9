<?php 
class Elite_Contactus_IndexController extends Mage_Core_Controller_Front_Action
{
    const XML_PATH_SEND_TO_CUSTOMER_EMAIL = "contactus/opciones/email_sent_to_customer";
    const XML_PATH_SEND_TO_ADMIN_EMAIL = "contactus/opciones/email_sent_to_admin";

    public function indexAction()
    {
        $this->loadLayout();
		$this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        $this->renderLayout();
    }
    public function formAction()
    {
        
        $this->loadLayout();
        $this->renderLayout();
        
    }
    
    public function submitAction(){
		$data = $this->getRequest()->getPost();
        $error = false;
        if($data) {
            try {
				if ($data['g-recaptcha-response']){
					$data['store_id'] = Mage::app()->getStore()->getId();				
					$this->sendmailtoadmin($data);
					$this->sendmailtocustomer($data);
					Mage::getSingleton('catalog/session')->addSuccess($this->__("Thanks for your contact. We will respond you as soon as possible."));
					$this->_redirectReferer();
				}else{
					Mage::getSingleton('catalog/session')->addError($this->__("Your contact for product can't sent. Please try again"));
					$this->_redirectReferer();
				}
                return;
            } catch (Exception $e) {
                Mage::getSingleton('catalog/session')->addError($this->__("Your contact for product can't sent. Please try again"));
                $this->_redirectReferer();
                return;
            }
        }
        
    }
    public function sendmailtocustomer($data) {       
        $store = Mage::app()->getStore();
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
        $template = Mage::getStoreConfig(self::XML_PATH_SEND_TO_CUSTOMER_EMAIL,$store->getId());
        $mailTemplate = Mage::getModel('core/email_template');
        $senderName = Mage::getStoreConfig('trans_email/ident_support/name');
        $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
        $sender = array('name' => $senderName, 'email' => $senderEmail);
        $recepientEmail = $data['cemail'];
        $recepientName = $data['cname']; 
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
        $adminRecipient = Mage::getBlockSingleton('contactus/contactus')->getEmailFromName($data['departmen']);
		Mage::log($adminRecipient,null,"contactus.log");
		$senderName = Mage::getStoreConfig('trans_email/ident_support/name');
		$senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
		$sender = array('name' => $senderName, 'email' => $senderEmail);
		$mailTemplate->setDesignConfig(array('area'=>'frontend', 'store'=>$store->getId()))
                ->sendTransactional(
                    $template,
                    $sender,
                    $adminRecipient,
                    $data['departmen'],
                    $data,
                    $store->getId()
                );
        $translate->setTranslateInline(true);
        return $this;
    }
}
 ?>