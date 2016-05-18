<?php
/**
 * 
 * Author@ Nimila Jose
 * Company : Echidna Software Pvt Ltd
 * Purpose@ Custom Price List for B2B according to the Group.
 *
 */
class Echidna_Extendedpricing_Model_Resource_Pricelist_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _constuct()
	{
        $this->_init('extendedpricing/extendedpricing');    
    }
}