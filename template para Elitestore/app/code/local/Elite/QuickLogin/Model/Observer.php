<?php
class Elite_QuickLogin_Model_Observer
{
    /**
     * @param Varien_Event_Observer $observer
     * @return Emzee_ForgotPasswortRedirect_Model_Observer
     */
    public function setForgotPasswordRedirect(Varien_Event_Observer $observer)
    {
        $controller = $observer->getEvent()->getControllerAction();
        $sessionMessages = Mage::getSingleton('customer/session')->getMessages();


        if ($sessionMessages->count(Mage_Core_Model_Message::SUCCESS) > 0 &&
            $sessionMessages->count(Mage_Core_Model_Message::ERROR) === 0) {
            $controller->setRedirectWithCookieCheck();  
        }

        return $this;
    }
    //funcion para remember me
    public function checkRememberMe($observer)
    {
        $session = $observer->getEvent()->getCustomerSession();
        if(!$session->isLoggedIn() and isset($_COOKIE['info']))
        {
            $collection = Mage::getModel('customer/customer')->setWebsiteId(Mage::app()->getStore()->getWebsiteId())->getCollection()->load();
            if ($collection->getSize() > 0)
            {
                foreach ($collection as $user)
                {
                    $user->loadByEmail($user->getEmail());
                    $salt = $user->getCreatedAtTimestamp();
                    $pass = $user->getPasswordHash();
                    $safe_pass = sha1(md5($pass).md5($salt));
                    if($safe_pass == $_COOKIE['info'])
                    {
                        $observer->getEvent()->getCustomerSession()->setCustomerAsLoggedIn($user);
                        header("Location: ".Mage::helper('core/url')->getCurrentUrl());
                        exit;
                    }
                }
            }
        }
        return;
    }
}