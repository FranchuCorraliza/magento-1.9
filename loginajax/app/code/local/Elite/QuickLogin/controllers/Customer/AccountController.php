<?php
// Require the core controller file that you're planning to override
require_once('Mage/Customer/controllers/AccountController.php');
 
// The class name follows this format:
// YOURPACKAGE_YOUREXTENSION_COREMODULEFOLDER_CONTROLLERFILENAME
// We extend the original Mage_Customer_AccountController class to inherit unused actions and override specific actions
class FastDivision_QuickLogin_Customer_AccountController extends Mage_Customer_AccountController
{
    // Code referenced from AccountController.php
    public function loginPostAction()
    {
    	if(!$this->getRequest()->isXmlHttpRequest()) {
	        if ($this->_getSession()->isLoggedIn()) {
	            $this->_redirect('*/*/');
	            return;
	        }
	    }
 
        $session = $this->_getSession();
 
        if($this->getRequest()->isXmlHttpRequest()) {
        	// Report exceptions via JSON
    		$ajaxExceptions = array();
    	}
 
        if ($this->getRequest()->isPost()) {
            $login = $this->getRequest()->getPost('login');
            if (!empty($login['username']) && !empty($login['password'])) {
                try {
                    $session->login($login['username'], $login['password']);
                    if ($session->getCustomer()->getIsJustConfirmed()) {
                        $this->_welcomeCustomer($session->getCustomer(), true);
                    }
                } catch (Mage_Core_Exception $e) {
                    if($this->getRequest()->isXmlHttpRequest()) {
		                $messages = array_unique(explode("\n", $e->getMessage()));
		                foreach ($messages as $message) {
		                    $ajaxExceptions['exceptions'][] = $message;
		                }
                    } else {
	                    switch ($e->getCode()) {
	                        case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
	                            $value = Mage::helper('customer')->getEmailConfirmationUrl($login['username']);
	                            $message = Mage::helper('customer')->__('This account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.', $value);
	                            break;
	                        case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
	                            $message = $e->getMessage();
	                            break;
	                        default:
	                            $message = $e->getMessage();
	                    }
	                    $session->addError($message);
                    }
 
                    $session->setUsername($login['username']);
                } catch (Exception $e) {
                    // Mage::logException($e); // PA DSS violation: this exception log can disclose customer password
                }
            } else {
                if($this->getRequest()->isXmlHttpRequest()) {
                	$ajaxExceptions['exceptions'][] = 'Login and password are required.';
                } else {
                	$session->addError($this->__('Login and password are required.'));
                }
            }
        }
 
        if($this->getRequest()->isXmlHttpRequest()) {
	        // If errors
	        if(count($ajaxExceptions)) {
	        	echo json_encode($ajaxExceptions);
	        } else {
	        	// No Errors
	        	echo json_encode(array('success' => 'success'));
	        }
	    } else {
	    	// Redirect for non-ajax
	    	$this->_loginPostRedirect();
	    }
    }
 
    // Create Account
    public function createPostAction()
    {
    	if($this->getRequest()->isXmlHttpRequest()) {
        	// Report exceptions via JSON
    		$ajaxExceptions = array();
    	}
 
        $session = $this->_getSession();
        if ($session->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
        $session->setEscapeMessages(true); // prevent XSS injection in user input
        if ($this->getRequest()->isPost()) {
            $errors = array();
 
            if (!$customer = Mage::registry('current_customer')) {
                $customer = Mage::getModel('customer/customer')->setId(null);
            }
 
            /* @var $customerForm Mage_Customer_Model_Form */
            $customerForm = Mage::getModel('customer/form');
            $customerForm->setFormCode('customer_account_create')
                ->setEntity($customer);
 
            $customerData = $customerForm->extractData($this->getRequest());
 
            if ($this->getRequest()->getParam('is_subscribed', false)) {
                $customer->setIsSubscribed(1);
            }
 
            /**
             * Initialize customer group id
             */
            $customer->getGroupId();
 
            if ($this->getRequest()->getPost('create_address')) {
                /* @var $address Mage_Customer_Model_Address */
                $address = Mage::getModel('customer/address');
                /* @var $addressForm Mage_Customer_Model_Form */
                $addressForm = Mage::getModel('customer/form');
                $addressForm->setFormCode('customer_register_address')
                    ->setEntity($address);
 
                $addressData    = $addressForm->extractData($this->getRequest(), 'address', false);
                $addressErrors  = $addressForm->validateData($addressData);
                if ($addressErrors === true) {
                    $address->setId(null)
                        ->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))
                        ->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false));
                    $addressForm->compactData($addressData);
                    $customer->addAddress($address);
 
                    $addressErrors = $address->validate();
                    if (is_array($addressErrors)) {
                        $errors = array_merge($errors, $addressErrors);
                    }
                } else {
                    $errors = array_merge($errors, $addressErrors);
                }
            }
 
            try {
                $customerErrors = $customerForm->validateData($customerData);
                if ($customerErrors !== true) {
                    $errors = array_merge($customerErrors, $errors);
                } else {
                    $customerForm->compactData($customerData);
                    $customer->setPassword($this->getRequest()->getPost('password'));
                    $customer->setConfirmation($this->getRequest()->getPost('confirmation'));
                    $customerErrors = $customer->validate();
                    if (is_array($customerErrors)) {
                        $errors = array_merge($customerErrors, $errors);
                    }
                }
 
                $validationResult = count($errors) == 0;
 
                if (true === $validationResult) {
                    $customer->save();
 
                    Mage::dispatchEvent('customer_register_success',
                        array('account_controller' => $this, 'customer' => $customer)
                    );
 
                    if ($customer->isConfirmationRequired()) {
                        $customer->sendNewAccountEmail(
                            'confirmation',
                            $session->getBeforeAuthUrl(),
                            Mage::app()->getStore()->getId()
                        );
                        $session->addSuccess($this->__('Account confirmation is required. Please, check your email for the confirmation link. To resend the confirmation email please <a href="%s">click here</a>.', Mage::helper('customer')->getEmailConfirmationUrl($customer->getEmail())));
 
                        if($this->getRequest()->isXmlHttpRequest()) {
                        	echo json_encode(array('success' => $this->__('Account confirmation is required. Please, check your email for the confirmation link. To resend the confirmation email please <a href="%s">click here</a>.', Mage::helper('customer')->getEmailConfirmationUrl($customer->getEmail()))));
                        } else {
                        	$this->_redirectSuccess(Mage::getUrl('*/*/index', array('_secure'=>true)));
                        }
 
                        return;
                    } else {
                        $session->setCustomerAsLoggedIn($customer);
                        $url = $this->_welcomeCustomer($customer);
 
                        if($this->getRequest()->isXmlHttpRequest()) {
                        	echo json_encode(array('success' => 'success'));
                       	} else {
                       		$this->_redirectSuccess($url);
                       	}
 
                        return;
                    }
                } else {
                    $session->setCustomerFormData($this->getRequest()->getPost());
 
                    if(!$this->getRequest()->isXmlHttpRequest()) {
	                    if (is_array($errors)) {
	                        foreach ($errors as $errorMessage) {
	                            $session->addError($errorMessage);
	                        }
	                    } else {
	                        $session->addError($this->__('Invalid customer data'));
	                    }
	                } else {
	                    if (is_array($errors)) {
	                        foreach ($errors as $errorMessage) {
	                            $ajaxExceptions['exceptions'][] = $errorMessage;
	                        }
	                    } else {
	                        $ajaxExceptions['exceptions'][] = 'Invalid customer data';
	                    }
                    }
                }
            } catch (Mage_Core_Exception $e) {
                $session->setCustomerFormData($this->getRequest()->getPost());
                if ($e->getCode() === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
                    $url = Mage::getUrl('customer/account/forgotpassword');
                    $message = $this->__('There is already an account with this email address. If you are sure that it is your email address, <a href="%s">click here</a> to get your password and access your account.', $url);
                    $session->setEscapeMessages(false);
                } else {
                    $message = $e->getMessage();
                }
 
                if(!$this->getRequest()->isXmlHttpRequest()) {
                	$session->addError($message);
               	} else {
	                $messages = array_unique(explode("\n", $e->getMessage()));
	                foreach ($messages as $message) {
	                    $ajaxExceptions['exceptions'][] = $message;
	                }
                }
            } catch (Exception $e) {
            	if(!$this->getRequest()->isXmlHttpRequest()) {
                	$session->setCustomerFormData($this->getRequest()->getPost())
                    ->addException($e, $this->__('Cannot save the customer.'));
                } else {
	            	$ajaxExceptions['exceptions'][] = 'Cannot save the customer.';
                }
            }
        }
 
        if($this->getRequest()->isXmlHttpRequest()) {
        	echo json_encode($ajaxExceptions);
        } else {
        	$this->_redirectError(Mage::getUrl('*/*/create', array('_secure' => true)));
        }
    }
}