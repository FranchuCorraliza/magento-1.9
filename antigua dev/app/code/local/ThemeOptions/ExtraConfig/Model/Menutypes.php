<?php class ThemeOptions_ExtraConfig_Model_Menutypes
{
    public function toOptionArray()
    {
        return array(
            array('value'=>1, 'label'=>Mage::helper('ExtraConfig')->__('Wide Menu')),
            array('value'=>2, 'label'=>Mage::helper('ExtraConfig')->__('Superfish Menu'))            
        );
    }

}?>