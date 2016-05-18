<?php

/**
 * 
 *
 *
 * Author@ Nimila Jose
 * Company@ Echidna Software Pvt Ltd
 * Purpose@ Extended Pricing Sheet
 * 
 *
 */
 
class Echidna_Extendedpricing_Model_Resource_ExtendedpricingAttributes extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('extendedpricing/extendedpricingAttributes', 'id');
    }
}