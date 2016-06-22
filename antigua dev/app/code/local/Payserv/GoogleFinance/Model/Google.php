<?php
/**
* Payserv
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@magentocommerce.com so we can send you a copy immediately.
*
* @category   Payserv
* @package    Payserv
* @copyright  Copyright (c) 2009 JT
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/
 
/**
* Currency rate import model (From google.com)
*
* @category   Payserv
* @package    Payserv
* @author     Pay Serv Internatioal SRL
*/
//class JT_Directory_Model_Currency_Import_Yahoofinance extends Mage_Directory_Model_Currency_Import_Abstract
class Payserv_GoogleFinance_Model_Google extends Mage_Directory_Model_Currency_Import_Abstract
{
    //protected $_url = 'http://quote.yahoo.com/d/quotes.csv?s==X&f=l1&e=.csv';
    protected $_url = 'http://www.google.com/finance/converter?a=1&from={{CURRENCY_FROM}}&to={{CURRENCY_TO}}';
    protected $_messages = array();
 
    protected function _convert($currencyFrom, $currencyTo, $retry=0)
    {
		$url = str_replace('{{CURRENCY_FROM}}', $currencyFrom, $this->_url);
        $url = str_replace('{{CURRENCY_TO}}', $currencyTo, $url);

        try {
            sleep(1); //Be nice to Google, they don't have a lot of hi-spec servers
			
			$ch = curl_init();

			// set URL and other appropriate options
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

			// grab URL and pass it to the browser
			$res = curl_exec($ch);
			curl_close($ch);
			
			if(preg_match("'<span class=bld>([0-9\.]+)\s\w+</span>'", $res, $m))
				$exchange_rate = $m[1];

			if( !$exchange_rate ) {
                $this->_messages[] = Mage::helper('directory')->__('Cannot retrieve rate from %s', $this->_url);
                return null;
            }
			
            return (float) $exchange_rate * 1.0; // change 1.0 to influence rate;
        }
        catch (Exception $e) {
            if( $retry == 0 ) {
                $this->_convert($currencyFrom, $currencyTo, 1);
            } else {
                $this->_messages[] = Mage::helper('directory')->__('Cannot retrieve rate from %s', $url);
            }
        }
    }
}