<?php class ThemeOptions_ExtraConfig_Model_Layertypes
{
    public function toOptionArray()
    {
        return array(
            array('value'=>1, 'label'=>Mage::helper('ExtraConfig')->__('Collapsed')),
            array('value'=>2, 'label'=>Mage::helper('ExtraConfig')->__('Opened'))            
        );
    }

}?>