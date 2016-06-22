<?php

/**
 * Optimiseweb CookieNotice Block Notice
 *
 * @package     Optimiseweb_CookieNotice
 * @author      Sid Vel (sid@optimiseweb.co.uk)
 * @copyright   Copyright (c) 2013 Optimiseweb Ltd
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Optimiseweb_CookieNotice_Block_Notice extends Mage_Core_Block_Template
{

    /**
     *
     * @return type
     */
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    /**
     * Check if cookie notice is enabled
     *
     * @return boolean
     */
    public function isEnabled()
    {
        if (Mage::getStoreConfig('optimisewebcookienotice/general/enabled') == 1) {
            if ((Mage::getModel('core/cookie')->get('ow_cookie_notice') == 'closed') OR (Mage::getModel('core/cookie')->get('ow_cookie_notice') == 'hidden')) {
                return FALSE;
            } elseif (!$this->isPersistent() AND (Mage::getModel('core/cookie')->get('ow_cookie_notice') == 'shown')) {
                return FALSE;
            }
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Get cookie lifetime
     *
     * @return boolean
     */
    public function getLifetime()
    {
        if (Mage::getStoreConfig('optimisewebcookienotice/general/lifetime')) {
            return Mage::getStoreConfig('optimisewebcookienotice/general/lifetime');
        } else {
            $lifetime = 31;
            return $lifetime;
        }
    }

    /**
     * Get Notice Message
     *
     * @return boolean
     */
    public function getMessage()
    {
        if (Mage::getStoreConfig('optimisewebcookienotice/message/message')) {
            return Mage::getStoreConfig('optimisewebcookienotice/message/message');
        } else {
            return FALSE;
        }
    }

    /**
     * Get Cookie Page Link
     *
     * @return boolean
     */
    public function getCookiePageLink()
    {
        if (Mage::getStoreConfig('optimisewebcookienotice/message/cms_page')) {
            return Mage::getUrl() . Mage::getStoreConfig('optimisewebcookienotice/message/cms_page');
        } else {
            return FALSE;
        }
    }

    /**
     * Get Cookie Link Text
     *
     * @return boolean
     */
    public function getCookieLinkText()
    {
        if (Mage::getStoreConfig('optimisewebcookienotice/message/link_text')) {
            return Mage::getStoreConfig('optimisewebcookienotice/message/link_text');
        } else {
            return FALSE;
        }
    }

    /**
     * Check if the notice type is Bar and position is Top
     *
     * @return boolean
     */
    public function isTopBar()
    {
        if ((Mage::getStoreConfig('optimisewebcookienotice/appearance/type') == 'bar') AND (Mage::getStoreConfig('optimisewebcookienotice/appearance/position_bar') == 'top')) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Get the top padding when the notice is shown
     *
     * @return boolean
     */
    public function topBarPaddingVisible()
    {
        if (Mage::getStoreConfig('optimisewebcookienotice/appearance/top_bar_padding')) {
            return Mage::getStoreConfig('optimisewebcookienotice/appearance/top_bar_padding') . 'px';
        } else {
            return FALSE;
        }
    }

    /**
     * Get the top padding when the notice is closed
     *
     * @return boolean
     */
    public function topBarPaddingClosed()
    {
        if (Mage::getStoreConfig('optimisewebcookienotice/appearance/top_bar_padding_closed')) {
            return Mage::getStoreConfig('optimisewebcookienotice/appearance/top_bar_padding_closed') . 'px';
        } else {
            return '0px';
        }
    }

    /**
     * Get Container classes
     *
     * @return string
     */
    public function getContainerClass()
    {
        $containerClass = Mage::getStoreConfig('optimisewebcookienotice/appearance/container_class');
        $type = Mage::getStoreConfig('optimisewebcookienotice/appearance/type');
        $barPosition = Mage::getStoreConfig('optimisewebcookienotice/appearance/position_bar');
        $boxPosition = Mage::getStoreConfig('optimisewebcookienotice/appearance/position_box');

        $class = 'cookienotice-container ';
        if ($containerClass) {
            $class .= $containerClass . ' ';
        }
        switch ($type) {
            case 'bar':
                $class .= 'cookienotice-bar ';
                switch ($barPosition) {
                    case 'top':
                        $class .= 'cookienotice-bar-top';
                        break;
                    case 'bottom':
                        $class .= 'cookienotice-bar-bottom';
                        break;
                }
                break;
            case 'box':
                $class .= 'cookienotice-box ';
                switch ($boxPosition) {
                    case 'top-left':
                        $class .= 'cookienotice-box-top-left';
                        break;
                    case 'top-right':
                        $class .= 'cookienotice-box-top-right';
                        break;
                    case 'bottom-right':
                        $class .= 'cookienotice-box-bottom-right';
                        break;
                    case 'bottom-left':
                        $class .= 'cookienotice-box-bottom-left';
                        break;
                }
                break;
        }

        return $class;
    }

    /**
     * Get Content classes
     *
     * @return string
     */
    public function getContentClass()
    {
        $contentClass = Mage::getStoreConfig('optimisewebcookienotice/appearance/content_class');

        $class = 'cookienotice-content';
        if ($contentClass) {
            $class .= ' ' . $contentClass;
        }

        return $class;
    }

    /**
     * Check if Persistent setting is on
     *
     * @return boolean
     */
    public function isPersistent()
    {
        return (bool) Mage::getStoreConfig('optimisewebcookienotice/behaviour/persistent');
    }

    /**
     * Is Close button enabled
     *
     * @return boolean
     */
    public function isCloseEnabled()
    {
        return (bool) Mage::getStoreConfig('optimisewebcookienotice/behaviour/close_enabled');
    }

    /**
     * Get Close button text
     *
     * @return boolean
     */
    public function getCloseText()
    {
        if (Mage::getStoreConfig('optimisewebcookienotice/behaviour/close_text')) {
            return Mage::getStoreConfig('optimisewebcookienotice/behaviour/close_text');
        } else {
            return FALSE;
        }
    }

    /**
     * Check if Auto Hide is enabled
     *
     * @return boolean
     */
    public function isAutoHide()
    {
        return (bool) Mage::getStoreConfig('optimisewebcookienotice/behaviour/autohide');
    }

    /**
     * Get the number of seconds the auto hide happens after
     *
     * @return boolean
     */
    public function getAutoHideSeconds()
    {
        if (Mage::getStoreConfig('optimisewebcookienotice/behaviour/autohide_time')) {
            return Mage::getStoreConfig('optimisewebcookienotice/behaviour/autohide_time') . '000';
        } else {
            return FALSE;
        }
    }

}