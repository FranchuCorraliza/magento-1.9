<?php
class Mage_Servired_Model_System_Config_Source_Signaturemethod
{

    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label' => Mage::helper('servired')->__('Completo')),
            array('value' => 2, 'label' => Mage::helper('servired')->__('Completo ampliado')),
        );
    }
}