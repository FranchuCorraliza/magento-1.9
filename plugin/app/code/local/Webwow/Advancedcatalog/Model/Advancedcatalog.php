<?php 
class Webwow_Advancedcatalog_Model_Advancedcatalog extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('advancedcatalog/advancedcatalog');
        
        Mage::dispatchEvent('salesrule_validator_process', array(
	        'rule'    => $rule,
	        'item'    => $item,
	        'address' => $address,
	        'quote'   => $quote,
	        'qty'     => $qty,
	        'result'  => $result,
	    ));
    }
}