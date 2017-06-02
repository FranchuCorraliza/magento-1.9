<?php
// Require the core controller file that you're planning to override
require_once Mage::getModuleDir('controllers', 'Mage_Customer').DS.'AccountController.php';
 
// The class name follows this format:
// YOURPACKAGE_YOUREXTENSION_COREMODULEFOLDER_CONTROLLERFILENAME
// We extend the original Mage_Customer_AccountController class to inherit unused actions and override specific actions
class Elite_QuickLogin_Customer_AccountController extends Mage_Customer_AccountController
{
    // Code referenced from AccountController.php
    public function loginPostAction()
    {
        Mage::log($this->getRequest()->isXmlHttpRequest());
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

                    //codigo para gestionar el remember me

                    if (isset($login['remember']))
                    {
                        //create cookies with user information, and salted password
                        $user = $login['username'];
                        //At the moment Created At timestamp could be a good idea to salt the password
                        $customer = Mage::getModel("customer/customer");
                        $salt = $customer->loadByEmail($customer);
                        $pass = $login['password'];
                        $safe_pass = sha1(md5($pass).md5($salt));
                        //Set the cookie with prepared data
                        setcookie('info',$safe_pass,time()+60*60*24*30,'/');
                    }
                    else
                    {
                        //Remove cookie if not checked
                        if (isset($_COOKIE['info']))
                            setcookie('info',$safe_pass,time()-60*60*24*30,'/');
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
    //borramos la cookie cuando nos deslogamos
    public function logoutAction()
    {
        //Remove the cookie if someone clicked logout
        if (isset($_COOKIE['info']))
            setcookie('info','',time()-60*60*24*30,'/');

        //Do whatever original method does
        $session = $this->_getSession();
        $session->logout()->renewSession();

        if (Mage::getStoreConfigFlag(Mage_Customer_Helper_Data::XML_PATH_CUSTOMER_STARTUP_REDIRECT_TO_DASHBOARD)) {
            $session->setBeforeAuthUrl(Mage::getBaseUrl());
        } else {
            $session->setBeforeAuthUrl($this->_getRefererUrl());
        }
        $this->_redirect("/");
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
                    $customer->setPasswordConfirmation($this->getRequest()->getPost('confirmation'));
					$customer->setPrefix($this->getRequest()->getPost('prefix'));
					//para registrar en las newsletter
					//Mage::getModel('newsletter/subscriber')->subscribe($this->getRequest()->getPost('email'));
					# create new subscriber without send an confirmation email
					Mage::getModel('newsletter/subscriber')->setImportMode(true)->subscribe($email);
					
					# get just generated subscriber
					$subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($email);
					# change status to "subscribed" and save
					$subscriber->setStatus(Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED);
					
					$subscriber->save();
					
					//$query = "UPDATE newsletter_subscriber SET subscriber_suffix = 'ESP' WHERE subscriber_email '" . $email ."'";
					
					//para registrar en las newsletter
					
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
	public function createAction(){//funcion que sobrescribe toda la funcion de creacion de usuarios
		
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
                    $customer->setPasswordConfirmation($this->getRequest()->getPost('confirmation'));
					$customer->setPrefix($this->getRequest()->getPost('prefix'));
					//para registrar en las newsletter
					//Mage::getModel('newsletter/subscriber')->subscribe($this->getRequest()->getPost('email'));
					//http://desarrollo.elitestore.es/eu/es/newsletter/subscriber/new/
					# create new subscriber without send an confirmation email
					//Mage::getModel('newsletter/subscriber')->setImportMode(true)->subscribe($email);

					# get just generated subscriber
					//$subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($email);

					# change status to "subscribed" and save
					//$subscriber->setStatus(Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE);
					//no funciona $subscriber->setSubscriberEmail($email);
					//no funciona $subscriber->setSubscriberConfirmCode($subscriber->RandomSequence());
					//no funciona $subscriber->setStoreId(Mage::app()->getStore()->getId());
					//no funciona $subscriber->setCustomerId($customer->getId());
					//$subscriber->save();
					//para registrar en las newsletter
                    $customerErrors = $customer->validate();
                    if (is_array($customerErrors)) {
                        $errors = array_merge($customerErrors, $errors);
                    }
                }
 
                $validationResult = count($errors) == 0;
 
                if (true === $validationResult) {
                    $customer->save();
					
					if($this->getRequest()->getPost('receive')){
						$suscriptor = Mage::getModel('mediarocks_newsletterextended/subscriber');
						$suscriptor->setSubscriberSuffix(Mage::getSingleton('geoip/country')->getCountry());
						$suscriptor->subscribe($this->getRequest()->getPost('email'));
						//Mage::getModel('newsletter/subscriber')->subscribe($this->getRequest()->getPost('email'));
						
					}
					
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
	public function forgotpasswordAction()
    {
		$email = (string) $this->getRequest()->getPost('email');
		
        if ($email) {
            if (!Zend_Validate::is($email, 'EmailAddress')) {
                $this->_getSession()->setForgottenEmail($email);
                $this->_getSession()->addError($this->__('Invalid email address.'));
				$ajaxExceptions['exceptions'][]=$this->__('Invalid email address.');
                
            }else{
				$customer = $this->_getModel('customer/customer')
					->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
					->loadByEmail($email);
				if ($customer->getId()) {
					try {
						$newResetPasswordLinkToken =  $this->_getHelper('customer')->generateResetPasswordLinkToken();
						$customer->changeResetPasswordLinkToken($newResetPasswordLinkToken);
						$customer->sendPasswordResetConfirmationEmail();
						$ajaxSuccess['success'][]=$this->__('We send you an email to reset your password');
					} catch (Exception $exception) {
						$this->_getSession()->addError($exception->getMessage());
						$ajaxExceptions['exceptions'][]=$exception->getMessage();
					}
				}else{
					$ajaxExceptions['exceptions'][]=$this->__('There is no user with this email');
				}
			}
			
			if(count($ajaxExceptions)) {
	        	echo json_encode($ajaxExceptions);
	        } else {
	        	// No Errors
	        	echo json_encode($ajaxSuccess);
	        }
		}
    }

    /**
     * Display reset forgotten password form
     *
     * User is redirected on this action when he clicks on the corresponding link in password reset confirmation email
     *
     */
    public function resetPasswordAction()
    {
        $resetPasswordLinkToken = (string) $this->getRequest()->getQuery('token');
        $customerId = (int) $this->getRequest()->getQuery('id');

        try {
            $this->_validateResetPasswordLinkToken($customerId, $resetPasswordLinkToken);
            $this->loadLayout();
            // Pass received parameters to the reset forgotten password form
            $this->getLayout()->getBlock('resetPassword')
                ->setCustomerId($customerId)
                ->setResetPasswordLinkToken($resetPasswordLinkToken);
            $this->renderLayout();
        } catch (Exception $exception) {
            $this->_getSession()->addError( $this->_getHelper('customer')->__('Your password reset link has expired.'));
            $this->_redirect();
        }
    }

    /**
     * Reset forgotten password
     * Used to handle data recieved from reset forgotten password form
     */
    public function resetPasswordPostAction()
    {
        $resetPasswordLinkToken = (string) $this->getRequest()->getQuery('token');
        $customerId = (int) $this->getRequest()->getQuery('id');
        $password = (string) $this->getRequest()->getPost('password');
        $passwordConfirmation = (string) $this->getRequest()->getPost('confirmation');

        try {
            $this->_validateResetPasswordLinkToken($customerId, $resetPasswordLinkToken);
        } catch (Exception $exception) {
            $this->_getSession()->addError( $this->_getHelper('customer')->__('Your password reset link has expired.'));
            $this->_redirect('*/*/');
            return;
        }

        $errorMessages = array();
        if (iconv_strlen($password) <= 0) {
            array_push($errorMessages, $this->_getHelper('customer')->__('New password field cannot be empty.'));
        }
        /** @var $customer Mage_Customer_Model_Customer */
        $customer = $this->_getModel('customer/customer')->load($customerId);

        $customer->setPassword($password);
        $customer->setPasswordConfirmation($passwordConfirmation);
        $validationErrorMessages = $customer->validate();
        if (is_array($validationErrorMessages)) {
            $errorMessages = array_merge($errorMessages, $validationErrorMessages);
        }

        if (!empty($errorMessages)) {
            $this->_getSession()->setCustomerFormData($this->getRequest()->getPost());
            foreach ($errorMessages as $errorMessage) {
                $this->_getSession()->addError($errorMessage);
            }
            $this->_redirect('*/*/resetpassword', array(
                'id' => $customerId,
                'token' => $resetPasswordLinkToken
            ));
            return;
        }

        try {
            // Empty current reset password token i.e. invalidate it
            $customer->setRpToken(null);
            $customer->setRpTokenCreatedAt(null);
            $customer->cleanPasswordsValidationData();
            $customer->save();
            $this->_getSession()->addSuccess( $this->_getHelper('customer')->__('Your password has been updated.'));
            $this->_redirect('*/*/login');
        } catch (Exception $exception) {
            $this->_getSession()->addException($exception, $this->__('Cannot save a new password.'));
            $this->_redirect('*/*/resetpassword', array(
                'id' => $customerId,
                'token' => $resetPasswordLinkToken
            ));
            return;
        }
    }
}