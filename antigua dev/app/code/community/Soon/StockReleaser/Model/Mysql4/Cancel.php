<?php

/**
 * Agence Soon
 *
 * @category    Soon
 * @package     Soon_StockReleaser
 * @copyright   Copyright (c) 2011 Agence Soon. (http://www.agence-soon.fr)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author      Hervé Guétin (herve.guetin@gmail.com)
 */
class Soon_StockReleaser_Model_Mysql4_Cancel extends Mage_Core_Model_Mysql4_Abstract {

    public function _construct() {
        $this->_init('stockreleaser/cancel', 'id');
    }

}
