<?php
class Mage_Servired_Block_Standard_Form extends Mage_Payment_Block_Form
{

    protected function _construct()
    {
        $this->setTemplate('servired/form.phtml');
        parent::_construct();
    }
}