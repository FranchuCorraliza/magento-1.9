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
 * Notification admin edit tabs
 *
 * @category    Webspeaks
 * @package     Webspeaks_NotifyCustomer
 * @author      Ultimate Module Creator
 */
class Webspeaks_NotifyCustomer_Block_Adminhtml_Notification_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Initialize Tabs
     *
     * @access public
     * @author Ultimate Module Creator
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('notification_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('webspeaks_notifycustomer')->__('Notification'));
    }

    /**
     * before render html
     *
     * @access protected
     * @return Webspeaks_NotifyCustomer_Block_Adminhtml_Notification_Edit_Tabs
     * @author Ultimate Module Creator
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'form_notification',
            array(
                'label'   => Mage::helper('webspeaks_notifycustomer')->__('Notification'),
                'title'   => Mage::helper('webspeaks_notifycustomer')->__('Notification'),
                'content' => $this->getLayout()->createBlock(
                    'webspeaks_notifycustomer/adminhtml_notification_edit_tab_form'
                )
                ->toHtml(),
            )
        );
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve notification entity
     *
     * @access public
     * @return Webspeaks_NotifyCustomer_Model_Notification
     * @author Ultimate Module Creator
     */
    public function getNotification()
    {
        return Mage::registry('current_notification');
    }
}
