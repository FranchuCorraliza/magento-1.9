<?php
/**
 * MagPleasure Ltd.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE-CE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magpleasure.com/LICENSE-CE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * MagPleasure does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Magpleasure does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   MagPleasure
 * @package    Magpleasure_Paypalcurrency
 * @version    1.0.1
 * @copyright  Copyright (c) 2012 MagPleasure Ltd. (http://www.magpleasure.com)
 * @license    http://www.magpleasure.com/LICENSE-CE.txt
 */

class Magpleasure_Paypalcurrency_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function confPaypalSandBox()
    {
        return !!Mage::getStoreConfig('paypal/wpp/sandbox_flag');
    }

    public function confPaypalUsername()
    {
        return Mage::getStoreConfig('paypal/wpp/api_username');
    }

    public function confPaypalPassword()
    {
        return Mage::getStoreConfig('paypal/wpp/api_password');
    }

    public function confPaypalSignature()
    {
        return Mage::getStoreConfig('paypal/wpp/api_signature');
    }

    public function confPaypalXComAppId()
    {
        return Mage::getStoreConfig('paypal/wpp/xcom_app_id') ?
               Mage::getStoreConfig('paypal/wpp/xcom_app_id') :
               Mage::getStoreConfig('paypal/api/xcom_app_id');

    }
    
}