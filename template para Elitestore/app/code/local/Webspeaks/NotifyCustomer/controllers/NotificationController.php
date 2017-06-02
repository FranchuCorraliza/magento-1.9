<?php
/**
 * Webspeaks_NotifyCustomer extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this  in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Webspeaks
 * @        Webspeaks_NotifyCustomer
 * @copyright      Copyright (c) 2016
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Notification front contrller
 *
 * @category    Webspeaks
 * @     Webspeaks_NotifyCustomer
 * @author      Ultimate Module Creator
 */
class Webspeaks_NotifyCustomer_NotificationController extends Mage_Core_Controller_Front_Action
{

    public function preDispatch()
    {
        parent::preDispatch();
        $action = $this->getRequest()->getActionName();
        $loginUrl = Mage::helper('customer')->getLoginUrl();
     
        if (!Mage::getSingleton('customer/session')->authenticate($this, $loginUrl)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
    }

    /**
      * default action
      *
      * @access public
      * @return void
      *
      */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        if (Mage::helper('webspeaks_notifycustomer/notification')->getUseBreadcrumbs()) {
            if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbBlock->addCrumb(
                    'home',
                    array(
                        'label' => Mage::helper('webspeaks_notifycustomer')->__('Home'),
                        'link'  => Mage::getUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'notifications',
                    array(
                        'label' => Mage::helper('webspeaks_notifycustomer')->__('Notifications'),
                        'link'  => '',
                    )
                );
            }
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->addLinkRel('canonical', Mage::helper('webspeaks_notifycustomer/notification')->getNotificationsUrl());
        }
        $this->renderLayout();
    }

    /**
     * init Notification
     *
     * @access protected
     * @return Webspeaks_NotifyCustomer_Model_Notification
     *
     */
    protected function _initNotification()
    {
        $notificationId   = $this->getRequest()->getParam('id', 0);
        $notification     = Mage::getModel('webspeaks_notifycustomer/notification')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($notificationId);
        if (!$notification->getId()) {
            return false;
        }
        return $notification;
    }

    /**
     * view notification action
     *
     * @access public
     * @return void
     *
     */
    public function viewAction()
    {
        $notification = $this->_initNotification();
        if (!$notification) {
            $this->_forward('no-route');
            return;
        }
        Mage::register('current_notification', $notification);
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        if ($root = $this->getLayout()->getBlock('root')) {
            $root->addBodyClass('notifycustomer-notification notifycustomer-notification' . $notification->getId());
        }
        if (Mage::helper('webspeaks_notifycustomer/notification')->getUseBreadcrumbs()) {
            if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbBlock->addCrumb(
                    'home',
                    array(
                        'label'    => Mage::helper('webspeaks_notifycustomer')->__('Home'),
                        'link'     => Mage::getUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'notifications',
                    array(
                        'label' => Mage::helper('webspeaks_notifycustomer')->__('Notifications'),
                        'link'  => Mage::helper('webspeaks_notifycustomer/notification')->getNotificationsUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'notification',
                    array(
                        'label' => $notification->getTitle(),
                        'link'  => '',
                    )
                );
            }
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->addLinkRel('canonical', $notification->getNotificationUrl());
        }

        // Mark as read
        $notification->setData('status', 1)->save();

        $this->renderLayout();
    }

    /**
     * Get customer notification action
     *
     * @access public
     * @return void
     *
     */
    public function ajax_loadAction()
    {
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            echo json_encode([
                'status' => 'error',
                'code' => 'not_logged_in'
            ]);
            exit();
        }

        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $notifications = Mage::getModel('webspeaks_notifycustomer/notification')
                            ->getCustomerNotifications($customer->getData('entity_id'), $count=10, ['title', 'status']);

        $notif_html = $this->getLayout()
                    ->createBlock('webspeaks_notifycustomer/notification_link')
                    ->setData('notifications', $notifications)
                    ->setTemplate('webspeaks_notifycustomer/notification/pop-message-item.phtml')
                    ->toHtml();

        echo json_encode([
            'status' => 'success',
            'notif_html' => $notif_html
        ]);
    }

    /**
     * Delete customer notification action Ajax
     *
     * @access public
     * @return void
     *
     */
    public function notif_delAction()
    {
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            echo json_encode([
                'status' => 'error',
                'code' => 'not_logged_in'
            ]);
            exit();
        }

        $id = $this->getRequest()->getParam('id');
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $notification = Mage::getResourceModel('webspeaks_notifycustomer/notification_collection')
                         ->addFieldTofilter('customer_id', $customer->getId())
                         ->addFieldTofilter('entity_id', $id)
                         ->getFirstItem();
        if ($notification && $notification->getId()) {
            $notification->delete();
            echo json_encode([
                'status' => 'success',
                'unread_count' => Mage::getModel('webspeaks_notifycustomer/notification')->getUnreadCount($customer->getId())
            ]);
        } else {
            echo json_encode([
                'status' => 'error'
            ]);
        }

    }

    /**
     * Delete customer notification action
     *
     * @access public
     * @return void
     *
     */
    public function deleteAction()
    {
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->_redirect('notifications/notification/');
            return;
        }

        $id = $this->getRequest()->getParam('id');
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $notification = Mage::getResourceModel('webspeaks_notifycustomer/notification_collection')
                         ->addFieldTofilter('customer_id', $customer->getId())
                         ->addFieldTofilter('entity_id', $id)
                         ->getFirstItem();
        if ($notification && $notification->getId()) {
            $notification->delete();
            Mage::getSingleton('core/session')->addSuccess(Mage::helper('webspeaks_notifycustomer')->__('Message deleted.'));
            $this->_redirect('notifications/notification');
        } else {
            Mage::getSingleton('core/session')->addError(Mage::helper('webspeaks_notifycustomer')->__('Cannot delete message.'));
            $this->_redirect('notifications/notification');
        }

    }
}
