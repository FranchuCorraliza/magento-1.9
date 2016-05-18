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
 * Import edit form block
 *
 * @category    Echidna
 * @package     Echidna_Importdata
 * @author      Sheshagiri Anvekar
 */
class Echidna_Extendedpricing_Block_Adminhtml_Import_Edit_Form extends Mage_Adminhtml_Block_Widget_Form 
{

    /**
     * Add fieldset
     *
     * @return Mage_ImportExport_Block_Adminhtml_Import_Edit_Form
     */
      protected function _prepareForm() 
      {
            $helper = Mage::helper('extendedpricing');
            $form = new Varien_Data_Form(array(
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/upload'),
                'method' => 'post',
                'enctype' => 'multipart/form-data'
            ));
        
            $fieldset = $form->addFieldset('base_fieldset', array('legend' => $helper->__('Upload Settings')));
        
            if (Mage::app()->getRequest()->getControllerName() == 'adminhtml_importcustomerdata') 
           {
                $fieldset->addField('filetype', 'select', array(
                    'label' => $helper->__('Select File Type'),
                    'class' => 'validate-select',
                    'name' => 'filetype',
                    'values' => array('' => ' ', '1' => 'Customerdata', '2' => 'Pricebook'),
                ));


                $this->setChild('form_after', $this->getLayout()
                                ->createBlock('adminhtml/widget_form_element_dependence')
                                ->addFieldMap('filetype', 'filetype')
                                ->addFieldMap('Importcsv', 'Importcsv')
                                ->addFieldDependence('Importcsv', 'filetype', 1)
                );
                $this->setChild('form_after', $this->getLayout()
                                ->createBlock('adminhtml/widget_form_element_dependence')
                                ->addFieldMap('filetype', 'filetype')
                                ->addFieldMap('Importcsv', 'Importcsv')
                                ->addFieldMap('Importcsv2', 'Importcsv2')
                                ->addFieldDependence('Importcsv', 'filetype', 2)
                                ->addFieldDependence('Importcsv2', 'filetype', 1)
                );

                $field = $fieldset->addField('Importcsv2', 'file', array(
                    'name' => 'Importcsv2',
                    'label' => $helper->__('Select File to Upload'),
                    'title' => $helper->__('Select File to Upload'),
                    'required' => true
                ));
            }

            $field = $fieldset->addField('Importcsv', 'file', array(
                'name' => 'Importcsv',
                'label' => $helper->__('Select File to Upload'),
                'title' => $helper->__('Select File to Upload'),
                'required' => true
            ));

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

}
