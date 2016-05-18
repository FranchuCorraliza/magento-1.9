<?php
/**
 * 
 * Author@ Nimila Jose
 * Company : Echidna Software Pvt Ltd
 * Purpose@ Custom Price List for B2B according to the Group.
 *
 */
class Echidna_Extendedpricing_Model_Resource_Pricelist extends Mage_Core_Model_Resource_Db_Abstract
{
   /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('extendedpricing/extendedpricing', 'id');
    }
}