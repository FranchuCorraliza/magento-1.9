<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_GeoipRedirect
 */
class Amasty_GeoipRedirect_Model_Source_ApplyLogic extends Varien_Object
{
    const ALL_URLS = 0;
    const SPECIFIED_URLS = 1;
    const EXCEPT_URLS = 2;

    public function toOptionArray()
    {
        $hlp = Mage::helper('amgeoipredirect');
        return array(
            array('value' => self::ALL_URLS, 'label' => $hlp->__('All URLs')),
            array('value' => self::SPECIFIED_URLS, 'label' => $hlp->__('Specified URLs')),
            array('value' => self::EXCEPT_URLS, 'label' => $hlp->__('All Except Specified URLs'))
        );
    }
}