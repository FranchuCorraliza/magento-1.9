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

class Magpleasure_Paypalcurrency_Model_Currency_Import_Paypal extends Mage_Directory_Model_Currency_Import_Abstract
{
    protected $_messages = array();

    /**
     * Helper
     *
     * @return Magpleasure_Paypalcurrency_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('paypalcurrency');
    }

    protected function _convert($currencyFrom, $currencyTo, $retry=0)
    {
        try {
            $request_array= array(
                Magpleasure_Paypalcurrency_Model_Npv_ConvertCurrency::$baseAmountList_currency_0_amount => 1000000,
                Magpleasure_Paypalcurrency_Model_Npv_ConvertCurrency::$baseAmountList_currency_0_code  =>  $currencyFrom,
                Magpleasure_Paypalcurrency_Model_Npv_ConvertCurrency::$convertToCurrencyList_currencyCode_0 =>  $currencyTo,
                Magpleasure_Paypalcurrency_Model_Npv_RequestEnvelope::$requestEnvelopeErrorLanguage => 'en_US'
            );

            /** @var $npv Magpleasure_Paypalcurrency_Model_Npv  */
            $npv = Mage::getSingleton('paypalcurrency/npv');
            $nvpParams=http_build_query($request_array, '', '&');
            $resArray= $npv->callService('AdaptivePayments/ConvertCurrency', $nvpParams);

            $ack = strtoupper($resArray['responseEnvelope.ack']);
            if ($ack == "SUCCESS"
                && isset($resArray["estimatedAmountTable.currencyConversionList(0).currencyList.currency(0).amount"])
            ){
                return (double)$resArray["estimatedAmountTable.currencyConversionList(0).currencyList.currency(0).amount"]/1000000;
            } else {
                $this->_messages[] = $this->_helper()->__('Cannot retrieve rate for %s.', $currencyTo);
                return null;
            }
        } catch (Exception $e) {
            $this->_messages[] = $this->_helper()->__('Cannot retrieve rate for %s (%s).', $currencyTo, $e->getMessage());
            return null;
        }
    }
}
