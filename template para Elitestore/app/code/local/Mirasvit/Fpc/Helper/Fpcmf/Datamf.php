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



class Mirasvit_Fpc_Helper_Fpcmf_Datamf extends Mage_Core_Helper_Abstract
{
    /**
     * @var array
     */
    protected static $_ignoredUrlParams = array();

    /**
     * @return array
     */
    protected function _getIgnoredUrlParams()
    {
        if (!self::$_ignoredUrlParams) {
            self::$_ignoredUrlParams = $this->getConfig()->getIgnoredUrlParams();
        }

        return self::$_ignoredUrlParams;
    }

    /**
     * @return Mirasvit_Fpc_Model_Configmf
     */
    public function getConfig()
    {
        $config = new Mirasvit_Fpc_Model_Configmf();

        return $config;
    }

    /**
     * @param bool $protocol
     * @return string
     */
    public function getNormlizedUrl($protocol = false)
    {
        $uri = false;

        if (isset($_SERVER['HTTP_HOST'])) {
            $uri = $_SERVER['HTTP_HOST'];
        } elseif (isset($_SERVER['SERVER_NAME'])) {
            $uri = $_SERVER['SERVER_NAME'];
        }

        if ($uri) {
            if (isset($_SERVER['REQUEST_URI'])) {
                $uri .= $_SERVER['REQUEST_URI'];
                $uri = strtok($uri, '?');
            } elseif (!empty($_SERVER['IIS_WasUrlRewritten']) && !empty($_SERVER['UNENCODED_URL'])) {
                $uri .= $_SERVER['UNENCODED_URL'];
            } elseif (isset($_SERVER['ORIG_PATH_INFO'])) {
                $uri .= $_SERVER['ORIG_PATH_INFO'];
            }

            $query = $_GET;
            foreach ($this->_getIgnoredUrlParams() as $param) {
                if (isset($query[$param])) {
                    unset($query[$param]);
                }
            }
            ksort($query);
            $query = http_build_query($query);

            $currentUrl = Mage::helper('core/url')->getCurrentUrl();
            if ($query && $currentUrl && strpos($currentUrl, '?') !== false) {
                $uri .= '?'.$query;
            }
        }

        if ($protocol) {
            $ssl = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? true : false;
            $sp = strtolower($_SERVER['SERVER_PROTOCOL']);
            $protocol = substr($sp, 0, strpos($sp, '/')).(($ssl) ? 's' : '');
            $uri = $protocol.'://'.$uri;
        }

        return $uri;
    }
}
