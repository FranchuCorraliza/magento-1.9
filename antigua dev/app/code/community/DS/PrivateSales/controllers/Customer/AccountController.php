<?php
require_once 'Mage/Customer/controllers/AccountController.php';

/**
 * Extended Account Controller
 * 
 * @author     Design:Slider GbR <magento@design-slider.de>
 * @copyright  (C)Design:Slider GbR <www.design-slider.de>
 * @license    OSL <http://opensource.org/licenses/osl-3.0.php>
 * @link       http://www.design-slider.de/magento-onlineshop/magento-extensions/private-sales/
 * @package    DS_PrivateSales
 */
class DS_PrivateSales_Customer_AccountController extends Mage_Customer_AccountController
{

    /**
     * XML Path to customer dashboard redirect option
     * 
     * @var string
     */
    private $xmlPathCustomerStartupRedirectToDashboard = 'customer/startup/redirect_dashboard';

    /**
     * Supress account registration page if disabled
     * 
     * @see Mage_Customer_AccountController::createAction
     */
    public function createAction()
    {
        if (!Mage::helper('privatesales')->canShowRegistration())
        {
            $this->_getSession()->addError(Mage::helper('privatesales')->getRegistrationErrorMessage());
            $this->_redirect('*/*');
            return;
        }
        
        return parent::createAction();
    }

    /**
     * Supress account registration action if disabled
     * 
     * @see Mage_Customer_AccountController::createPostAction
     */
    public function createPostAction()
    {
        if (!Mage::helper('privatesales')->canShowRegistration())
        {
            $this->_getSession()->addError(Mage::helper('privatesales')->getRegistrationErrorMessage());
            $this->_redirect('*/*');
            return;
        }
        
        return parent::createPostAction();
    }
    
    /**
     * Supress forgot password page if disabled
     * 
     * @see Mage_Customer_AccountController::forgotPasswordAction
     */
    public function forgotPasswordAction()
    {
        if (!Mage::helper('privatesales')->canShowForgotPassword())
        {
            $this->_getSession()->addError(Mage::helper('privatesales')->getForgotPasswordErrorMessage());
            $this->_redirect('*/*');
            return;
        }
        
        return parent::forgotPasswordAction();
    }
    
    /**
     * Supress forgot password action if disabled
     * 
     * @see Mage_Customer_AccountController::forgotPasswordAction
     */
    public function forgotPasswordPostAction()
    {
        if (!Mage::helper('privatesales')->canShowForgotPassword())
        {
            $this->_getSession()->addError(Mage::helper('privatesales')->getForgotPasswordErrorMessage());
            $this->_redirect('*/*');
            return;
        }
        
        return parent::forgotPasswordPostAction();
    }

    /**
     * Define target URL and redirect customer after logging in
     * 
     * @see Mage_Customer_AccountController::_loginPostRedirect
     * @since 2014/11/05 use default redirect if login was not successful to avoid error message display is being suppressed by multiple redirects
     */
    protected function _loginPostRedirect()
    {
        $session = $this->_getSession();

        # retrieve xml path constant (ce >= 1.6)
        if (defined('Mage_Customer_Helper_Data::XML_PATH_CUSTOMER_STARTUP_REDIRECT_TO_DASHBOARD'))
        {
            $this->xmlPathCustomerStartupRedirectToDashboard = Mage_Customer_Helper_Data::XML_PATH_CUSTOMER_STARTUP_REDIRECT_TO_DASHBOARD;
        }

        # use default behaviour if privatesales disabled OR login failed OR customer_startup_redirect_dashboard is configured
        if (!Mage::helper('privatesales')->isEnabled() || !$session->isLoggedIn() || Mage::getStoreConfigFlag($this->xmlPathCustomerStartupRedirectToDashboard)) {
            return parent::_loginPostRedirect();
        }

        if (!$session->getBeforeAuthUrl()) { #no baseurl comparison here, default logic after here

            # set default URL to redirect customer to
            $session->setBeforeAuthUrl(Mage::helper('customer')->getAccountUrl());

            # redirect customer to the last page visited after logging in
            if ($session->isLoggedIn())
            {
                if (!Mage::getStoreConfigFlag($this->xmlPathCustomerStartupRedirectToDashboard))
                {
                    $referer = $this->getRequest()->getParam(Mage_Customer_Helper_Data::REFERER_QUERY_PARAM_NAME);
                    if ($referer)
                    {
                        $referer = Mage::helper('core')->urlDecode($referer);
                        if ($this->_isUrlInternal($referer))
                        {
                            $session->setBeforeAuthUrl($referer);
                        }
                    }
                }
                elseif ($session->getAfterAuthUrl())
                {
                    $session->setBeforeAuthUrl($session->getAfterAuthUrl(true));
                }
            }
            else
            {
                $session->setBeforeAuthUrl(Mage::helper('customer')->getLoginUrl());
            }
        }
        
        # previous url was logout
        elseif ($session->getBeforeAuthUrl() == Mage::helper('customer')->getLogoutUrl())
        {
            $session->setBeforeAuthUrl(Mage::helper('customer')->getDashboardUrl());
        }
        
        # other previous url
        else {
            if (!$session->getAfterAuthUrl())
            {
                $session->setAfterAuthUrl($session->getBeforeAuthUrl());
            }

            if ($session->isLoggedIn())
            {
                $session->setBeforeAuthUrl($session->getAfterAuthUrl(true));
            }
        }

        # perform redirect
        $this->_redirectUrl($session->getBeforeAuthUrl(true));
    }
}