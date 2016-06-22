<?php class ThemeOptions_ExtraConfig_Model_Productpages
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'default', 'label'=>Mage::helper('ExtraConfig')->__('Default')),
            array('value'=>'horizontal', 'label'=>Mage::helper('ExtraConfig')->__('Horizontal')),
            array('value'=>'vertical', 'label'=>Mage::helper('ExtraConfig')->__('Vertical'))   
        );
    }

}?>