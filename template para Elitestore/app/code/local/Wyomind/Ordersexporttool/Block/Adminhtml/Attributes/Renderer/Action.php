<?php

class Wyomind_Ordersexporttool_Block_Adminhtml_Attributes_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action {

    public function render(Varien_Object $row) {
        $this->getColumn()->setActions(
                array(
                    array(
                        'url' => $this->getUrl('*/adminhtml_attributes/edit', array('id' => $row->getAttribute_id())),
                        'caption' => Mage::helper('ordersexporttool')->__('Edit'),
                    ),
                    array(
                        'url' => $this->getUrl('*/adminhtml_attributes/delete', array('id' => $row->getAttribute_id())),
                        'confirm' => Mage::helper('ordersexporttool')->__('Are you sure you want to delete this attribute ?'),
                        'caption' => Mage::helper('ordersexporttool')->__('Delete'),
                    ),
                   
                )
        );
        return parent::render($row);
    }

}
