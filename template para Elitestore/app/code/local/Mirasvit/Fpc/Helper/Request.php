<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Full Page Cache
 * @version   1.0.32
 * @build     662
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_Fpc_Helper_Request extends Mage_Core_Helper_Abstract
{
    /**
     * @return bool
     */
    public function isRedirect()
    {
        foreach (Mage::app()->getResponse()->getHeaders() as $header) {
            if ($header['name'] == 'Location') {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function getUserAgent()
    {
        return Mage::helper('core/http')->getHttpUserAgent();
    }

    /**
     * @return bool
     */
    public function isCrawler()
    {
        return preg_match('/FpcCrawler/', $this->getUserAgent());
    }

    /**
     * @return bool
     */
    public function isIgnoredPage()
    {
        $regExps = $this->getConfig()->getIgnoredPages();
        foreach ($regExps as $exp) {
            if ($this->_validateRegExp($exp) && preg_match($exp, Mage::helper('fpc')->getNormalizedUrl())) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    protected function _validateRegExp($exp)
    {
        if (@preg_match($exp, null) === false) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public  function isIgnoredParams()
    {
        $result = true;
        for ($i = 1; $i < 10; $i++) {
            if (isset($_GET) && (isset($_GET['no_cache']) || isset($_GET['no_cache' . $i]))) {
                $result = false;
                break;
            }
        }

        return $result;
    }

    /**
     * GetCartSize
     * @return int
     */
    public function getCartSize() {
        $checkout = Mage::getSingleton('checkout/session');
        $cartItemsCollection = $checkout->getQuote()->getItemsCollection();

        return $cartItemsCollection->getSize();
    }


    /**
     * @return Mirasvit_Fpc_Model_Config
     */
    public function getConfig()
    {
        return Mage::getSingleton('fpc/config');
    }

}