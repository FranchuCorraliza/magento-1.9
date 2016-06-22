<?php

class Wyomind_Ordersexporttool_Block_Adminhtml_Attributes_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {

        $form = new Varien_Data_Form(
                        array(
                            'id' => 'edit_form',
                            'action' => $this->getUrl('*/*/save', array('attribute_id' => $this->getRequest()->getParam('attribute_id'))),
                            'method' => 'post',
                        )
        );
        $model = Mage::getModel('ordersexporttool/attributes');
        $model->load($this->getRequest()->getParam('id'));
        $form->setUseContainer(true);
        $this->setForm($form);

        $fieldset = $form->addFieldset('ordersexporttool_form', array('legend' => $this->__('Attribute configuration')));


        if ($this->getRequest()->getParam('id')) {
            $fieldset->addField('attribute_id', 'hidden', array(
                'name' => 'attribute_id',
            ));
        }

        $fieldset->addField('attribute_name', 'text', array(
            'name' => 'attribute_name',
            'required' => true,
            'value' => $model->getAttributeName(),
            'label' => Mage::helper('ordersexporttool')->__('Attribute code'),
            'class' => 'validate-code',
            'note' => "Use only letters (a-z), numbers (0-9) or underscore(_) in this field"
        ));
        $fieldset->addField('attribute_script', 'textarea', array(
            'name' => 'attribute_script',
            'class' => 'CodeMirror',
            'required' => true,
            'value' => $model->getAttributeScript(),
            'label' => Mage::helper('ordersexporttool')->__('Custom php script'),
            'note' => "Create your custom php script (no openning or closing tags needed)"
        ));


/*
        $types = array(
            array("code" => "order_item", "label" => "Product", "table" => "sales_flat_order_item"),
            //array("code" => "order_address", "label" => "Address", "table" => "sales_flat_order_address"),
            array("code" => "order_payment", "label" => "Payment", "table" => "sales_flat_order_payment"),
            array("code" => "invoice", "label" => "Invoice", "table" => "sales_flat_invoice"),
            array("code" => "shipment", "label" => "Shipment", "table" => "sales_flat_shipment"),
            array("code" => "creditmemo", "label" => "Creditmemo", "table" => "sales_flat_creditmemo"),
        );

        function cmp($a, $b) {

            return ($a['attribute_code'] < $b['attribute_code']) ? -1 : 1;
        }

        foreach ($types as $type) {
            eval('$value=$model->get_' . ucFirst($type['code']) . '();');
            $value = explode(',', $value);
            if (version_compare(Mage::getVersion(), '1.5.0', '<')) {

                $resource = Mage::getSingleton('core/resource');
                $read = $resource->getConnection('core_read');
                $tableEet = $resource->getTableName('eav_entity_type');
                $select = $read->select()->from($tableEet)->where('entity_type_code IN ("' . $type['code'] . '")');

                $data = $read->fetchAll($select);
                $typeId = $data[0]['entity_type_id'];

                $attributesList = Mage::getResourceModel('eav/entity_attribute_collection')
                        ->setEntityTypeFilter($typeId)
                        ->addSetInfo()
                        ->getData();
            } else {

                $attributesList = array();
                $resource = Mage::getSingleton('core/resource');
                $read = $resource->getConnection('core_read');
                $tableSfo = $resource->getTableName($type['table']);
                $fields = $read->describeTable($tableSfo);
                foreach (array_keys($fields) as $field) {
                    $attributesList[]['attribute_code'] = $field;
                }
            }

            usort($attributesList, "cmp");
            $i = 0;
            $attributes=array();
            foreach ($attributesList as $attribute) {


                if (!empty($attribute['attribute_code'])) {
                    $attributes[$i]['value'] = $attribute['attribute_code'];
                    $attributes[$i]['label'] = $attribute['attribute_code'];
                    $i++;
                }
            }

            $fieldset->addField('attribute_' . $type['code'], 'multiselect', array(
                'name' => 'attribute_' . $type['code'],
                'value' => $value,
                'label' => $type['label'] . Mage::helper('ordersexporttool')->__(' attributes required'),
                'values' => $attributes,
                'note' => "Select the required attributes in use in the php script"
            ));
        }
*/
        $fieldset->addField('continue', 'hidden', array(
            'name' => 'continue',
            'value' => ''
        ));

        if (Mage::getSingleton('adminhtml/session')->getOrdersexporttoolData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getOrdersexporttoolData());
            Mage::getSingleton('adminhtml/session')->setOrdersexporttoolData(null);
        } elseif (Mage::registry('ordersexporttool_data')) {
            $form->setValues(Mage::registry('ordersexporttool_data')->getData());
        }


        return parent::_prepareForm();
    }

}

?>