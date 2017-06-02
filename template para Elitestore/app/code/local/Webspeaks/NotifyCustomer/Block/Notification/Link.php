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
 * Notification view block
 *
 * @category    Webspeaks
 * @package     Webspeaks_NotifyCustomer
 * @author      Webspeaks
 */
class Webspeaks_NotifyCustomer_Block_Notification_Link extends Mage_Core_Block_Template
{
    public function getNotifBaseUrl()
    {
        return $this->getUrl('notifications/notification');
    }

    public function getUnreadCount()
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        return Mage::getModel('webspeaks_notifycustomer/notification')
                ->getUnreadCount($customer->getData('entity_id'));
    }

}
