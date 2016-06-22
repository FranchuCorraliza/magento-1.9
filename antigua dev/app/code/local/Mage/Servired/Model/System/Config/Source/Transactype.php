<?php
class Mage_Servired_Model_System_Config_Source_TransacType
{

    public function toOptionArray()
    {
        return array(
            array('value' => 0, 'label' => Mage::helper('servired')->__('Autorizacion')),
            array('value' => 1, 'label' => Mage::helper('servired')->__('Preautorizacion')),
            array('value' => 2, 'label' => Mage::helper('servired')->__('Confirmacion')),
            array('value' => 3, 'label' => Mage::helper('servired')->__('Devolucion Automatica')),
            array('value' => 4, 'label' => Mage::helper('servired')->__('Pago Referencia')),
            array('value' => 5, 'label' => Mage::helper('servired')->__('Transaccion Recurrente')),
            array('value' => 6, 'label' => Mage::helper('servired')->__('Transaccion Sucesiva')),
            array('value' => 7, 'label' => Mage::helper('servired')->__('Autenticacion')),
            array('value' => 8, 'label' => Mage::helper('servired')->__('Confirmacion de Autenticacion')),
            array('value' => 9, 'label' => Mage::helper('servired')->__('Anulacion de Preautorizacion')),
        );
    }
}