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
 * Notification admin block
 *
 * @category    Webspeaks
 * @package     Webspeaks_NotifyCustomer
 * @author      Ultimate Module Creator
 */
class Webspeaks_NotifyCustomer_Block_Adminhtml_Notification extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * constructor
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function __construct()
    {
        $this->_controller         = 'adminhtml_notification';
        $this->_blockGroup         = 'webspeaks_notifycustomer';
        parent::__construct();
        $this->_headerText         = Mage::helper('webspeaks_notifycustomer')->__('Notification');
        $this->_updateButton('add', 'label', Mage::helper('webspeaks_notifycustomer')->__('New Notification'));

    }
}
