<?php
$installer = $this;
try{
    $productTypes = array(
        Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
        Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE
    );
    $productTypes = join(',', $productTypes);

    $installer->startSetup();
$installer->addAttribute('catalog_product', 'discount', array(
    'group'             => 'Prices',
    'type'              => Varien_Db_Ddl_Table::TYPE_VARCHAR,
    'backend'           => '',
    'frontend'          => '',
    'label'             => 'Discount',
    'input'             => 'price',
    'class'             => '',
    'source'            => '',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => true,
    'default'           => '',
    'searchable'        => false,
    'filterable'        => true,
    'comparable'        => false,
    //'visible_on_front'  => true,
    'unique'            => false,
    'apply_to'          => $productTypes,
    'is_configurable'   => false
));

$installer->endSetup();
}
catch(Exception $ex){
    echo $ex->getMessage();
}