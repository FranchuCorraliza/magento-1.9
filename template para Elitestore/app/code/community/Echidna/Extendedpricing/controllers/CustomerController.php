<?php
// Include the Original Mage_Adminhtml's CustomerController.php file
// where the class 'Mage_Adminhtml_AccountController' is defined
require_once 'Mage\Adminhtml\controllers\CustomerController.php';

class Echidna_ExtendedPricing_CustomerController extends Mage_Adminhtml_CustomerController  
{
    
public function editAction()
    {  
         $this->_initCustomer();
         $this->loadLayout();

        /* @var $customer Mage_Customer_Model_Customer */
         $customer = Mage::registry('current_customer');
          
        // set entered data if was error when we do save
         $data = Mage::getSingleton('adminhtml/session')->getCustomerData(true);
       
        // restore data from SESSION
         if ($data) {
            $request = clone $this->getRequest();
            $request->setParams($data);

            if (isset($data['account'])) {
                /* @var $customerForm Mage_Customer_Model_Form */
                $customerForm = Mage::getModel('customer/form'); 
                $customerForm->setEntity($customer)
                    ->setFormCode('adminhtml_customer')
                    ->setIsAjaxRequest(true);
                $formData = $customerForm->extractData($request, 'account');
                $customerForm->restoreData($formData);
             }

         if (isset($data['address']) && is_array($data['address'])) {
                /* @var $addressForm Mage_Customer_Model_Form */
                $addressForm = Mage::getModel('customer/form');
                $addressForm->setFormCode('adminhtml_customer_address');

                foreach (array_keys($data['address']) as $addressId) {
                    if ($addressId == '_template_') {
                        continue;
                    }

                $address = $customer->getAddressItemById($addressId);
                    if (!$address) {
                        $address = Mage::getModel('customer/address');
                        $customer->addAddress($address);
                    }

                $formData = $addressForm->setEntity($address)
                                        ->extractData($request);
                $addressForm->restoreData($formData);
                }
            }
        }

        $this->_title($customer->getId() ? $customer->getName() : $this->__('New Customer'));

        /**
         * Set active menu item
         */
        $this->_setActiveMenu('customer/new');

        $this->renderLayout();
    }
   
    //set the option(s) list for pricebook custom attribute through ajax call
    public function dynamicAction()
    {  

      //check for ajax priceregion parameter  
      if(!empty($_GET["priceregion"])) 
      {
        $priceregion = $_GET["priceregion"];  
      }
      else
      {
        $priceregion = null;
      }
        
    /**
     * Get the resource model
     */
    
      $resource = Mage::getSingleton('core/resource');
     
    /**
     * Retrieve the read connection
     */
      $readConnection = $resource->getConnection('core_read');
     
      $query = "SELECT DISTINCT pricebook FROM echidna_extendedpricing_attributes WHERE priceregion = '$priceregion' ";
     
    /**
     * Execute the query and store the results in $results
     */
      $results = $readConnection->fetchAll($query);
    
      echo "<option selected='selected' disabled='disabled' value=''>Select value</option>";
    
        foreach ($results as $list)
        {
            $pricebookOptionId = Mage::getResourceModel('customer/customer')
                                ->getAttribute('pricebook')
                                ->getSource()
                                ->getOptionId($list['pricebook']);
            echo '<option value="'.$pricebookOptionId.'">'.$list['pricebook'].'</option>';
        }

    }
   
    
}