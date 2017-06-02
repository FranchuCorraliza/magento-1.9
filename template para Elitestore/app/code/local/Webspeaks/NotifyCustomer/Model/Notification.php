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
 * Notification model
 *
 * @category    Webspeaks
 * @package     Webspeaks_NotifyCustomer
 * @author      Ultimate Module Creator
 */
class Webspeaks_NotifyCustomer_Model_Notification extends Mage_Core_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY    = 'webspeaks_notifycustomer_notification';
    const CACHE_TAG = 'webspeaks_notifycustomer_notification';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'webspeaks_notifycustomer_notification';

    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject = 'notification';

    /**
     * constructor
     *
     * @access public
     * @return void
     * @author Webspeaks
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('webspeaks_notifycustomer/notification');
    }

    /**
     * before save notification
     *
     * @access protected
     * @return Webspeaks_NotifyCustomer_Model_Notification
     * @author Webspeaks
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $now = Mage::getSingleton('core/date')->gmtDate();
        if ($this->isObjectNew()) {
            $this->setCreatedAt($now);
        }
        $this->setUpdatedAt($now);
        return $this;
    }

    /**
     * get the url to the notification details page
     *
     * @access public
     * @return string
     * @author Webspeaks
     */
    public function getNotificationUrl()
    {
        return Mage::getUrl('webspeaks_notifycustomer/notification/view', array('id'=>$this->getId()));
    }

    /**
     * get the notification Message
     *
     * @access public
     * @return string
     * @author Webspeaks
     */
    public function getMessage()
    {
        $message = $this->getData('message');
        $helper = Mage::helper('cms');
        $processor = $helper->getBlockTemplateProcessor();
        $html = $processor->filter($message);
        return $html;
    }

    /**
     * save notification relation
     *
     * @access public
     * @return Webspeaks_NotifyCustomer_Model_Notification
     * @author Webspeaks
     */
    protected function _afterSave()
    {
        return parent::_afterSave();
    }

    /**
     * get default values
     *
     * @access public
     * @return array
     * @author Webspeaks
     */
    public function getDefaultValues()
    {
        $values = array();
        $values['status'] = 1;
        return $values;
    }

    /**
     * Get customer unread count
     *
     * @access public
     * @return array
     * @author Webspeaks
     */
    public function getUnreadCount($customer_id)
    {
        $notifications = Mage::getResourceModel('webspeaks_notifycustomer/notification_collection');
        $notifications->addFieldToFilter('status', 0)
                      ->addFieldToFilter('customer_id', $customer_id);
        return $notifications->getSize();
    }

    /**
     * Get customer notifications
     *
     * @access public
     * @return array
     * @author Webspeaks
     */
    public function getCustomerNotifications($customer_id, $count=null, $fields=[])
    {
        $notifications = Mage::getResourceModel('webspeaks_notifycustomer/notification_collection');

        if (count($fields)) {
            $notifications->addFieldToSelect($fields);
        }

        $notifications->addFieldToFilter('customer_id', $customer_id);

        if ($count) {
            $notifications->setPageSize($count)->setCurPage(1);
        }

        $notifications->setOrder('entity_id', 'DESC');
        return $notifications;
    }

}
