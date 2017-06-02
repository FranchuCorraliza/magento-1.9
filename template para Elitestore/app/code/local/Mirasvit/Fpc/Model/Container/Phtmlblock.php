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



class Mirasvit_Fpc_Model_Container_Phtmlblock extends Mirasvit_Fpc_Model_Container_Abstract
{
    public function applyToContent(&$content, $withoutBlockUpdate = false)
    {
        if (!$this->_definition['replacer_tag_begin']
            || !$this->_definition['template']
            || ($this->_definition['replacer_tag_begin']
                && strpos($content, $this->_definition['replacer_tag_begin']) === false)) {
                    return true;
        }

        $startTime = microtime(true);
        $definitionHash = $this->_definition['block'] . '_' .  $this->_definition['template'];
        Mage::helper('fpc/debug')->startTimer('FPC_BLOCK_' . $definitionHash);

        $pattern = '/'.preg_quote($this->_definition['replacer_tag_begin'], '/').'(.*?)'.preg_quote($this->_definition['replacer_tag_end'], '/').'/ims';
        $html = $this->_renderBlock();

        if ($html !== false) {
            ini_set('pcre.backtrack_limit', 100000000);
            Mage::helper('fpc/debug')->appendDebugInformationToBlock($html, $this, 0, $startTime);
            $content = preg_replace($pattern, str_replace('$', '\\$', $html), $content, 1);
            Mage::helper('fpc/debug')->stopTimer('FPC_BLOCK_' . $definitionHash);

            return true;
        }
        Mage::helper('fpc/debug')->stopTimer('FPC_BLOCK_' . $definitionHash);

        return false;
    }


    /**
     * Render block content.
     *
     * @return string
     */
    protected function _renderBlock()
    {
        $html = '';
        if ($block = Mage::app()->getLayout()->createBlock($this->_definition['block'])) {
            $html = $block->setTemplate($this->_definition['template'])->toHtml();
        }

        return $html;
    }
}
