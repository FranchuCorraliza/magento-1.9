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



class Mirasvit_Fpc_Model_Blockmf_Messages extends Mirasvit_Fpc_Model_Blockmf_Abstract
{
     /**
     * @var array
     */
    protected $_messageStoreTypes = array(
        'core/session',
        'customer/session',
        'catalog/session',
        'checkout/session',
        'tag/session',
    );

    /**
     * @param string $content
     * @return bool
     */
    public function applyToContentWithoutApp(&$content)
    {
        $sessionHelper = new Mirasvit_Fpc_Helper_Fpcmf_Sessionmf();

        if ($sessionHelper->getMessage() == true) {
            return false;
        }

        return true;
    }

    /**
     * @param string $content
     * @return void
     */
    public function applyToContentInApp(&$content)
    {
        $startTime = microtime(true);

        $layoutHelper = Mage::helper('fpc/layout');

        $block = $layoutHelper->renderBlock($this->getLayoutXml(), $this->getNameInLayout());

        foreach ($this->_messageStoreTypes as $type) {
            $this->_addMessagesToBlock($type, $block);
        }

        $html = $block->toHtml();

        $html = $this->getWrappedHtml($html);

        $pattern = '/'.preg_quote($this->_getStartWrapperTag(), '/').'(.*?)'.preg_quote($this->_getEndWrapperTag(), '/').'/ims';

        Mirasvit_Fpc_Helper_Fpcmf_Debugmf::appendDebugInformationToBlock($html, $this, 1, microtime(true));

        $content = preg_replace($pattern, str_replace('$', '\\$', $html), $content, 1);

        $this->setRenderTime(microtime(true) - $startTime);
    }

    /**
     * @param  object $messagesStorage
     * @param  Mage_Core_Block_Messages $block
     * @return void
     */
    protected function _addMessagesToBlock($messagesStorage, Mage_Core_Block_Messages $block)
    {
        if ($storage = Mage::getSingleton($messagesStorage)) {
            $block->addMessages($storage->getMessages(true));
            $block->setEscapeMessageFlag($storage->getEscapeMessages(true));
        }
    }
}
