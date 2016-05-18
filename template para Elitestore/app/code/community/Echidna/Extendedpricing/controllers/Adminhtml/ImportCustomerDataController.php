<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * ImportCustomerData controller
 *
 * @category    Echidna
 * @package     Echidna_Extendedpricing
 * @author      Nimila Jose
 */
class Echidna_Extendedpricing_Adminhtml_ImportCustomerDataController extends Mage_Adminhtml_Controller_Action {

    /**
     * Custom constructor.
     *
     * @return void
     */
    protected function _construct() {
        // Define module dependent translate
        $this->setUsedModuleName('Echidna_Extendedpricing');
    }

    /**
     * Initialize layout.
     *
     * @return Mage_Extendedpricing_Adminhtml_ImportCustomerDataController
     */
    protected function _initAction() {
        $this->_title($this->__('Import/Export'))->loadLayout();
        return $this;
    }

    /**
     * Check access (in the ACL) for current user.
     *
     * @return bool
     */
    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('system/convert/import');
    }

    /**
     * Index action.
     *
     * @return void
     */
    public function indexAction() {
        $this->_initAction()
                ->_title($this->__('Import'))
                ->_addBreadcrumb($this->__('Import'), $this->__('Import'));

        $this->renderLayout();
    }

    public function uploadAction() {

        if (isset($_FILES['Importcsv2']['name']) and ( file_exists($_FILES['Importcsv2']['tmp_name']))) {
            try {
                $uploader = new Varien_File_Uploader('Importcsv2');
                $uploader->setAllowedExtensions(array('csv')); // or pdf or anything
                $uploader->setAllowRenameFiles(false);
                $uploader->setFilesDispersion(false);

                //read csv file
                $csvFile = $_FILES['Importcsv2']['tmp_name'];
                $csv = $this->readCSV($csvFile);
                $csv = array_filter($csv);

                //validate csv files contents
                $this->validationAction($csv);
            } catch (Exception $e) {
                Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
            }

            //save csv file 
            if (Mage::getSingleton('adminhtml/session')->getMessages()->count() == 0) {
                $path = Mage::getBaseDir('var') . DS . 'import';
                $uploader->save($path, "customerAttribute.csv");
                Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Import successfully done."));
            }
            $this->_redirect('*/*/');
        } elseif (isset($_FILES['Importcsv']['name']) and ( file_exists($_FILES['Importcsv']['tmp_name']))) {

            try {
                $uploader = new Varien_File_Uploader('Importcsv');
                $uploader->setAllowedExtensions(array('csv')); // or pdf or anything
                $uploader->setAllowRenameFiles(false);
                $uploader->setFilesDispersion(false);

                $csvFile = $_FILES['Importcsv']['tmp_name'];
                $csv = $this->readCSV($csvFile);
                $csv = array_filter($csv);

                foreach ($csv as $key => $pricebook) {
                    if (!$key == 0) {
                        if (isset($pricebook[0]) && trim($pricebook[0]) !== '') {
                            $sku = $pricebook[0];
                            $id = Mage::getModel('catalog/product')->getIdBySku($sku);

                            //checking sku is present or not in database.
                            if (empty($id)) {
                                Mage::getSingleton("adminhtml/session")->addError("product with sku " . $sku . " not present");
                            }
                        } else {
                            Mage::getSingleton("adminhtml/session")->addError("The sku field is empty");
                        }
                    }
                }

                //saving pricebook.csv files          
                if (Mage::getSingleton('adminhtml/session')->getMessages()->count() == 0) {
                    $path = Mage::getBaseDir('var') . DS . 'import';
                    $uploader->save($path, 'pricebook.csv');
                    Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Import successfully done."));
                }
            } catch (Exception $e) {
                Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
            }
        } else {

            Mage::getSingleton("adminhtml/session")->addError("File name is not valid");
        }
        $this->_redirect('*/*/');
    }

    //read csv file contents line by line 
    public function readCSV($csvFile) {
        $file_handle = fopen($csvFile, 'r');
        while (!feof($file_handle)) {
            $line_of_text[] = fgetcsv($file_handle, 1024);
        }
        fclose($file_handle);
        return $line_of_text;
    }

    //validation for each fields in csv file
    public function validationAction($csv) {
        foreach ($csv as $key => $customerdata) {
            if (!$key == 0) {
                //checking for customer id field in csv.
                if (isset($customerdata[0]) && trim($customerdata[0]) !== '') {
                    $customer_id = $customerdata[0];
                    $customer = Mage::getModel('customer/customer')->load($customer_id);

                    //checking customer id is present or not in database.
                    if (!empty($customer['entity_id'])) {
                        $priceregion = $customer->getResource()->getAttribute("priceregion");
                        $pricebook = $customer->getResource()->getAttribute("pricebook");

                        //checking priceregion  field is present in csv.
                        if (isset($customerdata[1]) && trim($customerdata[1]) !== '') {
                            $priceregion_option_id = $priceregion->getSource()->getOptionId($customerdata[1]);

                            if (!empty($priceregion_option_id) && trim($priceregion_option_id) !== '') {
                                $customer->setData('priceregion', $priceregion_option_id);
                            } else {
                                Mage::getSingleton("adminhtml/session")->addError("The price region " . $customerdata[1] . " is not valid data");
                            }
                        } else {
                            Mage::getSingleton("adminhtml/session")->addError("The priceregion is empty for customer ID  " . $customerdata[0]);
                        }

                        //checking pricebook field is present in csv
                        if (isset($customerdata[2]) && trim($customerdata[2]) !== '') {
                            $pricebook_option_id = $pricebook->getSource()->getOptionId($customerdata[2]);

                            //checking for valid  pricebook value(which is present in master data)
                            if (!empty($pricebook_option_id) && trim($pricebook_option_id) !== '') {
                                $customer->setData('pricebook', $pricebook_option_id);
                            } else {
                                Mage::getSingleton("adminhtml/session")->addError("The pricebook " . $customerdata[2] . " is not valid data");
                            }
                        } else {
                            Mage::getSingleton("adminhtml/session")->addError("The pricebook is empty for customer ID  " . $customerdata[0]);
                        }


                        //saving attribute option value
                        $customer->save();
                    } else {
                        Mage::getSingleton("adminhtml/session")->addError("Customer with ID " . $customerdata[0] . "  not present");
                    }
                } else {
                    Mage::getSingleton("adminhtml/session")->addError("The customer ID is empty for priceregion " . $customerdata[1]);
                }
            }
        }
    }

}
