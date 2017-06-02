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
 * @author      Ultimate Module Creator
 */
class Webspeaks_NotifyCustomer_Block_Notification_View extends Mage_Core_Block_Template
{
    /**
     * get the current notification
     *
     * @access public
     * @return mixed (Webspeaks_NotifyCustomer_Model_Notification|null)
     * @author Ultimate Module Creator
     */
    public function getCurrentNotification()
    {
        return Mage::registry('current_notification');
    }
}
