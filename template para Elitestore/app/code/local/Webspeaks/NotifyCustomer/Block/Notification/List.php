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
 * Notification list block
 *
 * @category    Webspeaks
 * @package     Webspeaks_NotifyCustomer
 * @author Ultimate Module Creator
 */
class Webspeaks_NotifyCustomer_Block_Notification_List extends Mage_Core_Block_Template
{
    /**
     * initialize
     *
     * @access public
     * @author Ultimate Module Creator
     */
    public function _construct()
    {
        parent::_construct();
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $notifications = Mage::getResourceModel('webspeaks_notifycustomer/notification_collection')
                         ->addFieldTofilter('customer_id', $customer->getId())
                         ->setOrder('entity_id', 'DESC')
                         ;
        $notifications->setOrder('title', 'asc');
        $this->setNotifications($notifications);
    }

    /**
     * prepare the layout
     *
     * @access protected
     * @return Webspeaks_NotifyCustomer_Block_Notification_List
     * @author Ultimate Module Creator
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock(
            'page/html_pager',
            'webspeaks_notifycustomer.notification.html.pager'
        )
        ->setCollection($this->getNotifications());
        $this->setChild('pager', $pager);
        $this->getNotifications()->load();
        return $this;
    }

    /**
     * get the pager html
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}
