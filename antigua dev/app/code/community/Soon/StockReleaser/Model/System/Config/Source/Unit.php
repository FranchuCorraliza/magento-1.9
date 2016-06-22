<?php

/**
 * Agence Soon
 *
 * @category    Soon
 * @package     Soon_StockReleaser
 * @copyright   Copyright (c) 2011 Agence Soon. (http://www.agence-soon.fr)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author      Hervé Guétin
 * 
 */
class Soon_StockReleaser_Model_System_Config_Source_Unit {

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
        return array(
            array('value' => 'min', 'label' => Mage::helper('stockreleaser')->__('Minutes')),
            array('value' => 'hour', 'label' => Mage::helper('stockreleaser')->__('Hours')),
            array('value' => 'day', 'label' => Mage::helper('stockreleaser')->__('Days')),
        );
    }

}
