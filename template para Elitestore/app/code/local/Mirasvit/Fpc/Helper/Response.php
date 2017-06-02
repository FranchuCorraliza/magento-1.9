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



class Mirasvit_Fpc_Helper_Response extends Mage_Core_Helper_Abstract
{
    /**
     * @param string $content
     * @return void
     */
    public function cleanExtraMarkup(&$content, $isSid = true)
    {
        $content = preg_replace('/<\[!--\{(.*?)\}--\]>/', '', $content);
        $content = preg_replace('/<\[!--\/\{(.*?)\}--\]>/', '', $content);
        if ($isSid) {
            $sid = array('___SID=U&amp;','___SID=U&','?___SID=U');
            $content = str_replace($sid, '', $content);
        }
    }

    /**
     * @param string $content
     * @return void
     */
    public function updateFormKey(&$content)
    {
        if ($formKey = Mage::getSingleton('core/session')->getFormKey()) {
            $content = preg_replace(
                '/<input type="hidden" name="form_key" value="(.*?)" \\/>/i',
                '<input type="hidden" name="form_key" value="' . $formKey . '" />',
                $content
            );

            $content = preg_replace(
                '/name="form_key" type="hidden" value="(.*?)" \\/>/i',
                'name="form_key" type="hidden" value="' . $formKey . '" />',
                $content
            );

            $content = preg_replace(
                '/\\/form_key\\/([^\"\'\/\s])+(\/|\"|\')/i',
                '/form_key/' . $formKey . "$2",
                $content
            );

            $content = preg_replace(
                '/\\/form_key' . '\\\\' . '\\/(.*?)' . '\\\\' . '\\//i',
                '/form_key\/' . $formKey . '\/',
                $content
            );
        }
    }

    /**
     * @param string $content
     * @return void
     */
    public function updateWelcomeMessage(&$content)
    {
        $welcome = false;

        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $welcome = Mage::helper('fpc')->__('Welcome, %s!', Mage::helper('core')->escapeHtml(Mage::getSingleton('customer/session')->getCustomer()->getName()));
        }

        if ($welcome) {
            $content = preg_replace(
                '/\\<p class="welcome-msg"\\>(.*?)\\<\\/p\\>/i',
                '<p class="welcome-msg">' . $welcome .'</p>',
                $content,
                1
            );

            $content = preg_replace(
                '/\\<div class="welcome-msg"\\>(.*?)\\<\\/div\\>/i',
                '<div class="welcome-msg">' . $welcome .'</div>',
                $content,
                1
            );
        }
    }

    /**
     * @param string $content
     * @return void
     */
    public function updateZopimInfo(&$content)
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()
            && Mage::helper('mstcore')->isModuleInstalled('Diglin_Chat')) {
                    $customer = Mage::getSingleton('customer/session')->getCustomer();
                    $customerName = $customer->getName();
                    $customerEmail = $customer->getEmail();

                    $content = preg_replace(
                        '/\\$zopim\\.livechat\\.setName\\(\'(.*?)\'\\)\;/i',
                        '$zopim.livechat.setName(\'' . $customerName . '\');',
                        $content,
                        1
                    );

                    $content = preg_replace(
                        '/\\$zopim\\.livechat\\.setEmail\\(\'(.*?)\'\\)\;/i',
                        '$zopim.livechat.setEmail(\'' . $customerEmail . '\');',
                        $content,
                        1
                    );

        }
    }
}