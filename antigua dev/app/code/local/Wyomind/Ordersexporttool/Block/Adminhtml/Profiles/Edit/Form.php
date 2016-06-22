<?php

class Wyomind_Ordersexporttool_Block_Adminhtml_Profiles_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
     
        $form = new Varien_Data_Form(
                        array(
                            'id' => 'edit_form',
                            'action' => $this->getUrl('*/*/save', array('file_id' => $this->getRequest()->getParam('file_id'))),
                            'method' => 'post',
                        )
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

}

?>