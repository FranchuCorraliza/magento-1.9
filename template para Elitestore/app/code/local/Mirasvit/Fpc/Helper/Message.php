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



class Mirasvit_Fpc_Helper_Message extends Mage_Core_Helper_Abstract
{
    public function addMessage(&$content, $message = false, $messageType = false) {
        $messageReplace = false;
        $action = Mage::helper('fpc')->getFullActionCode();

        if ($action == 'catalog/category_view' || $action == 'catalog/product_view') {
            $content = preg_replace('/<ul class="messages">(.*?)<\\/ul>/ims', '', $content, 1);
        }

        if ($message && $action == 'catalog/category_view') {
            $content = preg_replace('/\\<\\/h1\\>/ims', '</h1>' . $message, $content, 1);
            $messageReplace = true;
        } elseif ($message && $action == 'catalog/product_view') {
            $content = preg_replace('/<div id="messages_product_view">/ims', '<div id="messages_product_view">' . $message, $content, 1);
            $messageReplace = true;
        }

        if ($messageReplace) {
            switch ($messageType) {
                case Mirasvit_Fpc_Model_Config::CATALOG_MESSAGE:
                    Mage::getSingleton('catalog/session')->getMessages()->clear(); //clear message after first show
                    break;

                case Mirasvit_Fpc_Model_Config::CHECKOUT_MESSAGE:
                    Mage::getSingleton('checkout/session')->getMessages()->clear(); //clear message after first show
                    break;

                default:
                    break;
           }
        }

        return $this;
    }

    public function getMessage($catalogMessageCount = false, $checkoutMessageCount = false)
    {
        $action = Mage::helper('fpc')->getFullActionCode();

        if ($action  != 'catalog/product_view'
            && $action  != 'catalog/category_view') {
                return false;
        }

        $messageText = '';
        if ($catalogMessageCount
            && ($message = Mage::getSingleton('catalog/session')->getMessages())
            && ($lastMessage = $message->getLastAddedMessage())) {
                $messageText = $this->prepareMessage($lastMessage);
        }

        if ($checkoutMessageCount
            && ($message = Mage::getSingleton('checkout/session')->getMessages())
            && ($lastMessage = $message->getLastAddedMessage())) {
                $messageText = $this->prepareMessage($lastMessage);
        }

        return $messageText;
    }

    protected function prepareMessage($message)
    {
        if (!is_object($message)) {
            return false;
        }

        $messageText = '';
        if ($message->getType() == 'success') {
            $messageText = '<ul class="messages">
                                <li class="success-msg">
                                    <ul>
                                        <li>' . $message->getText() . '</li>
                                    </ul>
                                </li>
                            </ul>';
        } elseif ($message->getType() == 'note') {
            $messageText = '<ul class="messages">
                                <li class="note-msg">
                                    <ul>
                                        <li>' . $message->getText() . '</li>
                                    </ul>
                                </li>
                            </ul>';
        } elseif ($message->getType() == 'error') {
            $messageText = '<ul class="messages">
                                <li class="error-msg">
                                    <ul>
                                        <li>' . $message->getText() . '</li>
                                    </ul>
                                </li>
                            </ul>';
        }

        return $messageText;
    }
}
