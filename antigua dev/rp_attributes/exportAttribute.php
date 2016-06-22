<?php
/**
 * Author: Magerips
 * Website: www.magerips.com
 * Suport Email: support@magerips.com
 *
**/
define('MAGENTO', realpath(dirname(__FILE__)));
require_once MAGENTO . '/../app/Mage.php';
Mage::app();
$entity_type_id = Mage::getModel('catalog/product')->getResource()->getTypeId();

prepareCollection($entity_type_id);

function prepareCollection($ent_type_id){
    $resource = Mage::getSingleton('core/resource');
    $connection = $resource->getConnection('core_read');
    $select_attribs = $connection->select()
            ->from(array('ea'=>$resource->getTableName('eav/attribute')))
            ->join(array('c_ea'=>$resource->getTableName('catalog/eav_attribute')), 'ea.attribute_id = c_ea.attribute_id');
            // ->join(array('e_ao'=>$resource->getTableName('eav/attribute_option'), array('option_id')), 'c_ea.attribute_id = e_ao.attribute_id')
            // ->join(array('e_aov'=>$resource->getTableName('eav/attribute_option_value'), array('value')), 'e_ao.option_id = e_aov.option_id and store_id = 0')
    $select_prod_attribs = $select_attribs->where('ea.entity_type_id = '.$ent_type_id)
                                            ->order('ea.attribute_id ASC');

    $product_attributes = $connection->fetchAll($select_prod_attribs);

    $select_attrib_option = $select_attribs
                                ->join(array('e_ao'=>$resource->getTableName('eav/attribute_option'), array('option_id')), 'c_ea.attribute_id = e_ao.attribute_id')
                                ->join(array('e_aov'=>$resource->getTableName('eav/attribute_option_value'), array('value')), 'e_ao.option_id = e_aov.option_id and store_id = 0')
                                ->order('e_ao.attribute_id ASC');

    $product_attribute_options = $connection->fetchAll($select_attrib_option);

    $attributesCollection = mergeCollections($product_attributes, $product_attribute_options);
    prepareCsv($attributesCollection);

}

function mergeCollections($product_attributes, $product_attribute_options){

    foreach($product_attributes as $key => $_prodAttrib){
        $values = array();
        $attribId = $_prodAttrib['attribute_id'];
        foreach($product_attribute_options as $pao){
            if($pao['attribute_id'] == $attribId){
                $values[] = $pao['value'];
            }
        }
        if(count($values) > 0){
            $values = implode(";", $values);
            $product_attributes[$key]['_options'] = $values;
        }
        else{
            $product_attributes[$key]['_options'] = "";
        }
        /*
            temp
        */
        $product_attributes[$key]['attribute_code'] = $product_attributes[$key]['attribute_code'];
    }

    return $product_attributes;

}

function prepareCsv($attributesCollection, $filename = "all_attributes.csv", $delimiter = '|', $enclosure = '"'){

    $f = fopen('php://memory', 'w');
    $first = true;
    foreach ($attributesCollection as $line) {
        if($first){
            $titles = array();
            foreach($line as $field => $val){
                $titles[] = $field;
            }
            fputcsv($f, $titles, $delimiter, $enclosure);
            $first = false;
        }
        fputcsv($f, $line, $delimiter, $enclosure); 
    }
    fseek($f, 0);
    header('Content-Type: application/csv');
    header('Content-Disposition: attachement; filename="'.$filename.'"');
    fpassthru($f);
}