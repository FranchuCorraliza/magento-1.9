<?php
class Pook_CollectInStore_Block_Onepage_Shipping_Method_Available extends Mage_Checkout_Block_Onepage_Shipping_Method_Available
{
    /*
    * Rewrite to remove collect in store from regular list of shipping methods.
    */
    public function getShippingRates()
    {
        if (empty($this->_rates)) {
            $this->getAddress()->collectShippingRates()->save();
            $groups = $this->getAddress()->getGroupedAllShippingRates();
            $this->_rates = $groups;
        }

        /* Don't show collect in store rate as an available option. */
        if(!Mage::getStoreConfig('carriers/collectinstore/onestep') && Mage::getStoreConfig('carriers/collectinstore/active') && array_key_exists('collectinstore', $this->_rates)) {
            unset($this->_rates['collectinstore']);
        }

        return $this->_rates;
    }
}