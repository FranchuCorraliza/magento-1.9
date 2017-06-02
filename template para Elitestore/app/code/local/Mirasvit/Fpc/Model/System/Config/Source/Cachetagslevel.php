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


class Mirasvit_Fpc_Model_System_Config_Source_Cachetagslevel
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => Mirasvit_Fpc_Model_Config::CACHE_TAGS_LEVEL_FIRST, 'label'=>Mage::helper('fpc')->__('Default')),
            // array('value' => Mirasvit_Fpc_Model_Config::CACHE_TAGS_LEVEL_SECOND, 'label'=>Mage::helper('fpc')->__('Extended set of tags')),
            array('value' => Mirasvit_Fpc_Model_Config::CACHE_TAGS_LEVEL_MINIMAL, 'label'=>Mage::helper('fpc')->__('Minimal set of tags')),
            array('value' => Mirasvit_Fpc_Model_Config::CACHE_TAGS_LEVEL_EMPTY, 'label'=>Mage::helper('fpc')->__('Don\'t use tags')),
            array('value' => Mirasvit_Fpc_Model_Config::CACHE_TAGS_LEVEL_MINIMAL_PREFIX, 'label'=>Mage::helper('fpc')->__('Minimal set of tags with custom prefix')),
        );
    }
}
