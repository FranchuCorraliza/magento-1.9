<?php

/**
 * Agence Soon
 *
 * @category    Soon
 * @package     Soon_StockReleaser
 * @copyright   Copyright (c) 2011 Agence Soon. (http://www.agence-soon.fr)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author      Hervé Guétin
 */
class Soon_StockReleaser_Model_System_Config_Source_Order_Status extends Mage_Adminhtml_Model_System_Config_Source_Order_Status {

    /**
     * Set to null to enable all possible order statuses
     *
     * @var null
     */
    protected $_stateStatuses = null;

}