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


class Mirasvit_Fpc_Model_System_Config_Source_FlushCacheButton
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 0, 'label'=>Mage::helper('fpc')->__('Disabled')),
            array('value' => Mirasvit_Fpc_Model_Config::FLUSH_CURRENT_PAGE_CACHE_BUTTON, 'label'=>Mage::helper('fpc')->__('Flush current page cache')),
            array('value' => Mirasvit_Fpc_Model_Config::FLUSH_DEPENDING_TAGS_CACHE_BUTTON, 'label'=>Mage::helper('fpc')->__('Flush depending tags cache')),
        );
    }
}
