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
 * Notification admin edit form
 *
 * @category    Webspeaks
 * @package     Webspeaks_NotifyCustomer
 * @author      Webspeaks
 */
class Webspeaks_NotifyCustomer_Block_Adminhtml_Notification_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * constructor
     *
     * @access public
     * @return void
     * @author Webspeaks
     */
    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'webspeaks_notifycustomer';
        $this->_controller = 'adminhtml_notification';
        $this->_updateButton(
            'save',
            'label',
            Mage::helper('webspeaks_notifycustomer')->__('Send Notification')
        );
        $this->_updateButton(
            'delete',
            'label',
            Mage::helper('webspeaks_notifycustomer')->__('Delete Notification')
        );
        /*$this->_addButton(
            'saveandcontinue',
            array(
                'label'   => Mage::helper('webspeaks_notifycustomer')->__('Save And Continue Edit'),
                'onclick' => 'saveAndContinueEdit()',
                'class'   => 'save',
            ),
            -100
        );
        $this->_formScripts[] = "
            function saveAndContinueEdit() {
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";*/

        if ($this->getRequest()->getParam('id')) {
            $this->_removeButton('save');
            $this->_removeButton('reset');
        }
    }

    /**
     * get the edit form header
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getHeaderText()
    {
        if (Mage::registry('current_notification') && Mage::registry('current_notification')->getId()) {
            return Mage::helper('webspeaks_notifycustomer')->__(
                "View Notification '%s'",
                $this->escapeHtml(Mage::registry('current_notification')->getTitle())
            );
        } else {
            return Mage::helper('webspeaks_notifycustomer')->__('Send Notification');
        }
    }
}
