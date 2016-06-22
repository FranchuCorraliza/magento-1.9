<?php
class Mage_Servired_Block_Standard_Redirect extends Mage_Core_Block_Template
{

    protected function _construct()
    {
        parent::_construct();
        $standard = Mage::getModel('servired/standard');
        $form = new Varien_Data_Form();
        $form->setAction($standard->getServiredUrl())
            ->setId('servired_standard_checkout')
            ->setName('Servired')
            ->setMethod('POST')
            ->setUseContainer(true);

        foreach($standard->getStandardCheckoutFormFields() as $field => $value)
        {
            $form->addField($field, 'hidden', array('name' => $field, 'value' => $value));
        }
        $this->setFormRedirect($form->toHtml());
    }
}