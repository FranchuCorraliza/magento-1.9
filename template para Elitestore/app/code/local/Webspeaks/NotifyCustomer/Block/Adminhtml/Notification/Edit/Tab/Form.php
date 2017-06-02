<?php
/**
 * Webspeaks_NotifyCustomer extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Webspeaks
 * @package        Webspeaks_NotifyCustomer
 * @copyright      Copyright (c) 2016
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Notification edit form tab
 *
 * @category    Webspeaks
 * @package     Webspeaks_NotifyCustomer
 * @author      Ultimate Module Creator
 */
class Webspeaks_NotifyCustomer_Block_Adminhtml_Notification_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare the form
     *
     * @access protected
     * @return Webspeaks_NotifyCustomer_Block_Adminhtml_Notification_Edit_Tab_Form
     * @author Ultimate Module Creator
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('notification_');
        $form->setFieldNameSuffix('notification');
        $this->setForm($form);

        $formValues = Mage::registry('current_notification')->getDefaultValues();
        if (!is_array($formValues)) {
            $formValues = array();
        }
        if (Mage::getSingleton('adminhtml/session')->getNotificationData()) {
            $formValues = array_merge($formValues, Mage::getSingleton('adminhtml/session')->getNotificationData());
            Mage::getSingleton('adminhtml/session')->setNotificationData(null);
        } elseif (Mage::registry('current_notification')) {
            $formValues = array_merge($formValues, Mage::registry('current_notification')->getData());
        }

        $fieldset = $form->addFieldset(
            'notification_form',
            array('legend' => Mage::helper('webspeaks_notifycustomer')->__('Notification'))
        );
        $wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig();

        $fieldset->addField(
            'title',
            'text',
            array(
                'label' => Mage::helper('webspeaks_notifycustomer')->__('Title'),
                'name'  => 'title',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'message',
            'editor',
            array(
                'label' => Mage::helper('webspeaks_notifycustomer')->__('Message'),
                'name'  => 'message',
                'config' => $wysiwygConfig,
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        if (!$this->getRequest()->getParam('id')) {
            $fieldset->addField(
                'customer_email',
                'text',
                array(
                    'label' => Mage::helper('webspeaks_notifycustomer')->__('Customer Email'),
                    'name'  => 'customer_email',
                    'required'  => true,
                    'class' => 'required-entry',

               )
            );

            $element = $fieldset->addField('customer_query', 'text', array(
                'label' => Mage::helper('webspeaks_notifycustomer')->__('Search Customer'),
                'name' => 'customer_query',
            ));

            $element->setAfterElementHtml("
                <button type='button' id='find-customer-btn' data-url='".Mage::helper('adminhtml')->getUrl('adminhtml/notifycustomer_notification/customerfind')."'><span>".Mage::helper('webspeaks_notifycustomer')->__('Find').'</span></button>
            ');

            $element = $fieldset->addField('customer_id', 'select', array(
                'name' => 'customer_id',
                'style' => 'display: none',
            ));

            $fieldset->addField('send_email', 'select', array(
                'label'    => Mage::helper('webspeaks_notifycustomer')->__('Send email'),
                'values'   => [
                    ['value' => '1', 'label' => 'Yes'],
                    ['value' => '0', 'label' => 'No']
                ],
                'name' => 'send_email',
                "class" => "required-entry",
                "required" => true,
                'after_element_html' => '<p><small>'.Mage::helper('webspeaks_notifycustomer')->__('Also send email to customer.').'</small></p>',
            ));

        } else {
            $customer = Mage::getModel('customer/customer')->load($formValues['customer_id']);
            $fieldset->addField(
                'customer_email',
                'note',
                array(
                    'label' => Mage::helper('webspeaks_notifycustomer')->__('Customer'),
                    'text' => $customer->getData('firstname') . ' ' . $customer->getData('middlename') . ' ' . $customer->getData('lastname')
               )
            );

            $fieldset->addField(
                'status',
                'note',
                array(
                    'label' => Mage::helper('webspeaks_notifycustomer')->__('Status'),
                    'text' => (!$formValues['status']) ? Mage::helper('webspeaks_notifycustomer')->__('Not read') : Mage::helper('webspeaks_notifycustomer')->__('Read')
               )
            );

        }

        $form->setValues($formValues);
        return parent::_prepareForm();
    }
}
