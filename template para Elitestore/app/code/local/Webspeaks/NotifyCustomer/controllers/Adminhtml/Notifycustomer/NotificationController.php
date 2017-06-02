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
 * Notification admin controller
 *
 * @category    Webspeaks
 * @package     Webspeaks_NotifyCustomer
 * @author      Webspeaks
 */
class Webspeaks_NotifyCustomer_Adminhtml_Notifycustomer_NotificationController extends Webspeaks_NotifyCustomer_Controller_Adminhtml_NotifyCustomer
{
    /**
     * init the notification
     *
     * @access protected
     * @return Webspeaks_NotifyCustomer_Model_Notification
     */
    protected function _initNotification()
    {
        $notificationId  = (int) $this->getRequest()->getParam('id');
        $notification    = Mage::getModel('webspeaks_notifycustomer/notification');
        if ($notificationId) {
            $notification->load($notificationId);
        }
        Mage::register('current_notification', $notification);
        return $notification;
    }

    /**
     * default action
     *
     * @access public
     * @return void
     * @author Webspeaks
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_title(Mage::helper('webspeaks_notifycustomer')->__('Customer Notification'))
             ->_title(Mage::helper('webspeaks_notifycustomer')->__('Notifications'));
        $this->renderLayout();
    }

    /**
     * grid action
     *
     * @access public
     * @return void
     * @author Webspeaks
     */
    public function gridAction()
    {
        $this->loadLayout()->renderLayout();
    }

    /**
     * edit notification - action
     *
     * @access public
     * @return void
     * @author Webspeaks
     */
    public function editAction()
    {
        $notificationId    = $this->getRequest()->getParam('id');
        $notification      = $this->_initNotification();
        if ($notificationId && !$notification->getId()) {
            $this->_getSession()->addError(
                Mage::helper('webspeaks_notifycustomer')->__('This notification no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getNotificationData(true);
        if (!empty($data)) {
            $notification->setData($data);
        }
        Mage::register('notification_data', $notification);
        $this->loadLayout();
        $this->_title(Mage::helper('webspeaks_notifycustomer')->__('Customer Notification'))
             ->_title(Mage::helper('webspeaks_notifycustomer')->__('Notifications'));
        if ($notification->getId()) {
            $this->_title($notification->getTitle());
        } else {
            $this->_title(Mage::helper('webspeaks_notifycustomer')->__('Add notification'));
        }
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }

    /**
     * new notification action
     *
     * @access public
     * @return void
     * @author Webspeaks
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * save notification - action
     *
     * @access public
     * @return void
     * @author Webspeaks
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost('notification')) {
            try {
                $notification = $this->_initNotification();

                // Find customer's id from email if id does not exist
                if (!$this->getRequest()->getParam('id')) {
                    $data['status'] = 0; // unread
    
                    $customer_id = (int) $data['customer_id'];
                    if (!$customer_id) {
                        $customer_email = $data['customer_email'];
                        $customer = Mage::helper('webspeaks_notifycustomer')->findCustomerByEmail($customer_email);
                        if (!$customer || !$customer->getId()) {
                            Mage::getSingleton('adminhtml/session')->addError(
                                Mage::helper('webspeaks_notifycustomer')->__('Provided customer email does not exist.')
                            );
                            Mage::getSingleton('adminhtml/session')->setNotificationData($data);
                            $this->_redirect('*/*/new');
                            return;
                        }
                        $data['customer_id'] = $customer->getId();
                    }
                } else {
                    $this->_redirect('*/*/');
                    return;
                }

                $notification->addData($data);
                $notification->save();

                if ($data['send_email'] == 1) {
                    Mage::helper('webspeaks_notifycustomer')->sendEmailToCustomer($data);
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('webspeaks_notifycustomer')->__('Notification was successfully sent.')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $notification->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setNotificationData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('webspeaks_notifycustomer')->__('There was a problem saving the notification.')
                );
                Mage::getSingleton('adminhtml/session')->setNotificationData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('webspeaks_notifycustomer')->__('Unable to find notification to save.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * delete notification - action
     *
     * @access public
     * @return void
     * @author Webspeaks
     */
    public function deleteAction()
    {
        if ( $this->getRequest()->getParam('id') > 0) {
            try {
                $notification = Mage::getModel('webspeaks_notifycustomer/notification');
                $notification->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('webspeaks_notifycustomer')->__('Notification was successfully deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('webspeaks_notifycustomer')->__('There was an error deleting notification.')
                );
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('webspeaks_notifycustomer')->__('Could not find notification to delete.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * mass delete notification - action
     *
     * @access public
     * @return void
     * @author Webspeaks
     */
    public function massDeleteAction()
    {
        $notificationIds = $this->getRequest()->getParam('notification');
        if (!is_array($notificationIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('webspeaks_notifycustomer')->__('Please select notifications to delete.')
            );
        } else {
            try {
                foreach ($notificationIds as $notificationId) {
                    $notification = Mage::getModel('webspeaks_notifycustomer/notification');
                    $notification->setId($notificationId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('webspeaks_notifycustomer')->__('Total of %d notifications were successfully deleted.', count($notificationIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('webspeaks_notifycustomer')->__('There was an error deleting notifications.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * mass status change - action
     *
     * @access public
     * @return void
     * @author Webspeaks
     */
    public function massStatusAction()
    {
        $notificationIds = $this->getRequest()->getParam('notification');
        if (!is_array($notificationIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('webspeaks_notifycustomer')->__('Please select notifications.')
            );
        } else {
            try {
                foreach ($notificationIds as $notificationId) {
                $notification = Mage::getSingleton('webspeaks_notifycustomer/notification')->load($notificationId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d notifications were successfully updated.', count($notificationIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('webspeaks_notifycustomer')->__('There was an error updating notifications.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * mass Read By Customer change - action
     *
     * @access public
     * @return void
     * @author Webspeaks
     */
    public function massReadAction()
    {
        $notificationIds = $this->getRequest()->getParam('notification');
        if (!is_array($notificationIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('webspeaks_notifycustomer')->__('Please select notifications.')
            );
        } else {
            try {
                foreach ($notificationIds as $notificationId) {
                $notification = Mage::getSingleton('webspeaks_notifycustomer/notification')->load($notificationId)
                    ->setRead($this->getRequest()->getParam('flag_read'))
                    ->setIsMassupdate(true)
                    ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d notifications were successfully updated.', count($notificationIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('webspeaks_notifycustomer')->__('There was an error updating notifications.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * export as csv - action
     *
     * @access public
     * @return void
     * @author Webspeaks
     */
    public function exportCsvAction()
    {
        $fileName   = 'notification.csv';
        $content    = $this->getLayout()->createBlock('webspeaks_notifycustomer/adminhtml_notification_grid')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export as MsExcel - action
     *
     * @access public
     * @return void
     * @author Webspeaks
     */
    public function exportExcelAction()
    {
        $fileName   = 'notification.xls';
        $content    = $this->getLayout()->createBlock('webspeaks_notifycustomer/adminhtml_notification_grid')
            ->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export as xml - action
     *
     * @access public
     * @return void
     * @author Webspeaks
     */
    public function exportXmlAction()
    {
        $fileName   = 'notification.xml';
        $content    = $this->getLayout()->createBlock('webspeaks_notifycustomer/adminhtml_notification_grid')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Check if admin has permissions to visit related pages
     *
     * @access protected
     * @return boolean
     * @author Webspeaks
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('webspeaks_notifycustomer/notification');
    }


    public function customerfindAction()
    {
        $q = $this->getRequest()->getParam('q');
        $result = Mage::helper('webspeaks_notifycustomer')->findCustomer($q);
        echo Mage::helper('core')->jsonEncode($result);
        die;
    }
}
