<?php
/*
 * Developer: Rene Voorberg
 * Team site: http://cmsideas.net/
 * Support: http://support.cmsideas.net/
 * 
 *
*/
class Glace_Ajaxcart_Model_Source_Dropeffect
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'shrink', 'label'=>Mage::helper('adminhtml')->__('Shrink')),
            array('value' => 'explode', 'label'=>Mage::helper('adminhtml')->__('Explode')),
            array('value' => 'puff', 'label'=>Mage::helper('adminhtml')->__('Puff')),
            array('value' => 'noeffect', 'label'=>Mage::helper('adminhtml')->__('No Effect')),
        );
    }

}
 