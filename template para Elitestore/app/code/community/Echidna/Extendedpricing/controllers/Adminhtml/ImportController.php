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
 * Import controller
 *
 * @category    Echidna
 * @package     Echidna_Extendedpricing
 * @author      Nimila Jose
 */
class Echidna_Extendedpricing_Adminhtml_ImportController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Custom constructor.
     *
     * @return void
     */
        const priceregion_attributename = 'priceregion';
        const pricebook_attributename = 'pricebook';
        protected function _construct()
        {       
            // Define module dependent translate
            $this->setUsedModuleName('Echidna_Extendedpricing');
        }

        /**
         * Initialize layout.
         *
         * @return Mage_Extendedpricing_Adminhtml_ImportController
         */
        protected function _initAction()
        {
            $this->_title($this->__('Import/Export'))->loadLayout();
            return $this;
        }

        /**
         * Check access (in the ACL) for current user.
         *
         * @return bool
         */
        protected function _isAllowed()
        {
            return Mage::getSingleton('admin/session')->isAllowed('system/convert/import');
        }

        /**
         * Index action.
         *
         * @return void
         */
        public function indexAction()
        {  
            $this->_initAction()
                ->_title($this->__('Import'))
                ->_addBreadcrumb($this->__('Import'), $this->__('Import'));

            $this->renderLayout();
        }

        public function uploadAction()
        {
            if(isset($_FILES['Importcsv']['name']) and (file_exists($_FILES['Importcsv']['tmp_name']))) 
                    {
                            try 
                            {
                                    $uploader = new Varien_File_Uploader('Importcsv'); 
                                    $uploader->setAllowedExtensions(array('csv')); // or pdf or anything
                                    $uploader->setAllowRenameFiles(false);
                                    $uploader->setFilesDispersion(false);

                                    //read csv file
                                    $csvFile = $_FILES['Importcsv']['tmp_name'];
                                    $csv = $this->readCSV($csvFile); 
                                    $csv = array_filter($csv); 
                                foreach ($csv as $key=>$customerdata)
                                {
                                  if(!$key == 0)
                                  {
                                   if((isset($customerdata[0]) && trim($customerdata[0])!== '') && !isset($customerdata[2]))
                                     {
                                   
                                         foreach ($csv as $key=>$extendedpricing)
                                         {
                                                   {   if(!$key == 0){
                                                       $pricereg[$key] = $extendedpricing[0].','.$extendedpricing[1]; 
                                                    
                                                       }
                                                   }
                                         } 
                                        
                                     }else{                                     
                                             Mage::getSingleton("adminhtml/session")->addError("The file data is not valid");
                                             break;

                                          }

                                   }
                                }
                                        
                                        //remove duplicate priceregions and price book 
                                        $priceregion = array();
                                        $pricebook = array();
                                        $pricereg = array_unique($pricereg);
                                        $count =0;
                                        
                                        $model= Mage::getSingleton('extendedpricing/extendedpricingAttributes');  
                                        foreach($pricereg as $key=>$val)
                                        {
                                           $myArray[$key] = explode(',', $val);
                                           $priceregion[] =$myArray[$key][0];  
                                           $pricebook[] = $myArray[$key][1];
                                      try {
                                                $data = array('priceregion'=>$priceregion[$count],'pricebook'=>$pricebook[$count]);

                                                $masterdatacollection = Mage::getModel('extendedpricing/extendedpricingAttributes')
                                                                        ->getCollection()						
                                                                        ->addFieldToFilter(array('priceregion'),array(array('like'=>$data['priceregion'])))
                                                                        ->addFieldToFilter(array('pricebook'),array(array('like'=>$data['pricebook'])));

                                                $masterdata = $masterdatacollection->getData();

                                               if(empty($masterdata))
                                               {
                                                $model->setData($data);
                                                $model->save();
                                               }
                                          } 
                                          catch (Exception $e)
                                          {
                                           echo $e->getMessage();
                                          }                                         
                                        $count++;   
                                        } 
                                        
                                        $priceregion = array_unique($priceregion);
                                        $pricebook = array_unique($pricebook);
                                        
                                        //checking for existing attribute option
                                        $this->addAttributeValue(self::priceregion_attributename,$priceregion);
                                        $this->addAttributeValue(self::pricebook_attributename,$pricebook);

                            }
                            catch(Exception $e) 
                            {
                                    Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
                            }
                          
                          //save csv file 
                          if(Mage::getSingleton('adminhtml/session')->getMessages()->count() == 0)
                          {
                              $path = Mage::getBaseDir('var').DS.'import';
                              $uploader->save($path, "CustomerAttributeOptions.csv"); 
                              Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Import successfully done."));
                          }
                            $this->_redirect('*/*/');
                    }
        }
    
        public function readCSV($csvFile)
        {
                $file_handle = fopen($csvFile, 'r');
                while (!feof($file_handle) ) {
                $line_of_text[]= fgetcsv($file_handle, 1024);
               }
                fclose($file_handle);
                return $line_of_text;
         }
    
        //storing attribute options for attribute value
        public function addAttributeValue($arg_attribute, $arg_value)
        {  
                $attribute   = Mage::getSingleton("eav/config")->getAttribute("customer", $arg_attribute);
                if(!$this->attributeValueExists($arg_attribute, $arg_value))
                {
                    foreach ($arg_value as $option){
                        $value['option'] = array($option,$option);
                        $result = array('value' => $value);
                        $attribute->setData('option',$result);
                        $attribute->save();
                     }
                }

                $attribute_options_model= Mage::getModel('eav/entity_attribute_source_table') ; 
                $attribute_table        = $attribute_options_model->setAttribute($attribute);
                $options                = $attribute_options_model->getAllOptions(false); 

                foreach($options as $option)
                {
                    if ($option['label'] == $arg_value)
                    {
                        return $option['value'];
                    }
                }
               return false;
            }
    
    
        //checking for attribute option   
         public function attributeValueExists($arg_attribute, $arg_value)
         {
                $attribute_model        = Mage::getModel('eav/entity_attribute');
                $attribute_options_model= Mage::getModel('eav/entity_attribute_source_table') ;
                $attribute   = Mage::getSingleton("eav/config")->getAttribute("customer", $arg_attribute);
                $attribute_table        = $attribute_options_model->setAttribute($attribute); 
                $options                = $attribute_options_model->getAllOptions(false);      

                foreach($options as $option)
                {
                    foreach($arg_value as $val)
                    if ($option['label'] == $val)
                    {
                        return $option['value'];
                    }
                }
                return false;
         }
    
        //delete all options of attribute priceregion and pricebook
        public function deleteAction()
        {   
                $attribute   = Mage::getSingleton("eav/config")->getAttribute("customer", self::priceregion_attributename);

                $options = $attribute->getSource()->getAllOptions(); 
                $collection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                   ->setAttributeFilter($attribute->getId())
                   ->setStoreFilter($attribute->getStoreId())
                   ->load();

                foreach ($collection as $option) {
                            $option->delete();
                            }
                            
                $attribute   = Mage::getSingleton("eav/config")->getAttribute("customer", self::pricebook_attributename); 
                $options = $attribute->getSource()->getAllOptions(); 
                $collection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                   ->setAttributeFilter($attribute->getId())
                   ->setStoreFilter($attribute->getStoreId())
                   ->load();

                 foreach ($collection as $option) {
                            $option->delete();
                            }
                            
                  //EMPTY ECHIDNA_EXTENDEDPRICINGATTRIBUTE TABLE
                   $collection = Mage::getModel('extendedpricing/extendedpricingAttributes')->getCollection();
                                foreach ($collection as $item) {
                                    $item->delete();
                                }
              
                           
              Mage::getSingleton('adminhtml/session')->addSuccess("All options are deleted for attributes");              
              $this->_redirect('*/*/');
        }


}
