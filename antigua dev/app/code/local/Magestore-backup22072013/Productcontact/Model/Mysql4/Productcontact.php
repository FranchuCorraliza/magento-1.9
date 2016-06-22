<?php

class Magestore_Productcontact_Model_Mysql4_Productcontact extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the productcontact_id refers to the key field in your database table.
        $this->_init('productcontact/productcontact', 'productcontact_id');
    }
}