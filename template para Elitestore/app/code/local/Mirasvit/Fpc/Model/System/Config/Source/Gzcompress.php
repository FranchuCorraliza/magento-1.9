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


class Mirasvit_Fpc_Model_System_Config_Source_Gzcompress
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options[] = array('value' => 0, 'label' => Mage::helper('fpc')->__('Disabled'));
        for ($i=1; $i <10; $i++) {
            $options[] = array('value' => $i, 'label' => $i);
        }

        return $options;
    }
}
