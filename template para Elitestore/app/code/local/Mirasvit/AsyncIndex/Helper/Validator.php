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
 * @package   Fast Asynchronous Re-indexing
 * @version   1.1.6
 * @build     285
 * @copyright Copyright (C) 2014 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_AsyncIndex_Helper_Validator extends Mirasvit_MstCore_Helper_Validator_Abstract
{
    public function testMagentoCrc()
    {
        $filter = array(
            'app/code/Mage/Core',
            'app/code/Mage/Catalog',
            'app/code/Mage/CatalogIndex',
            'app/code/Mage/CatalogRule',
            'app/code/Mage/CatalogInventory',
        );
        return Mage::helper('mstcore/validator_crc')->testMagentoCrc($filter);
    }

    public function testMirasvitCrc()
    {
        $modules = array('AsyncIndex');
        return Mage::helper('mstcore/validator_crc')->testMirasvitCrc($modules);
    }

    public function testConflicts()
    {
        return Mage::helper('mstcore/validator_conflict')->testConflicts();
    }
}