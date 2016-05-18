<?php  
$installer = $this;
 
$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('extendedpricing/extendedpricing'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Id')
    ->addColumn('sku', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
        'nullable'  => false,
        ), 'SKU')
	->addColumn('priceregion', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
        'nullable'  => false,
        ), 'Price Region')
	->addColumn('subpriceregion', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
        'nullable'  => false,
        ), 'Sub Price Region')
	->addColumn('price', Varien_Db_Ddl_Table::TYPE_FLOAT, 10, array(
        'nullable'  => false,
        ), 'Price');
$installer->getConnection()->createTable($table);

$table = $installer->getConnection()
    ->newTable($installer->getTable('extendedpricing/extendedpricingAttributes'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Id')
   ->addColumn('priceregion', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
        'nullable'  => false,
        ), 'Price Region')
   ->addColumn('pricebook', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
        'nullable'  => false,
        ), 'Sub Price Book');

$installer->getConnection()->createTable($table);
 

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$entityTypeId     = $setup->getEntityTypeId('customer');
$attributeSetId   = $setup->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $setup->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$installer->addAttribute("customer", "priceregion",  array(
    "type"     => "text",
    "backend"  => "",
    "input_renderer" => "extension/adminhtml_catalog_category_dependency",
    "label"    => "Price Region",
    "input"    => "select",
    "source"   => "extendedpricing/source_option",
    "visible"  => true,
    "required" => false,
    "default" => "",
    "frontend" => "",
    "unique"     => false
	
        ));

$attribute   = Mage::getSingleton("eav/config")->getAttribute("customer", "priceregion");


$setup->addAttributeToGroup(
    $entityTypeId,
    $attributeSetId,
    $attributeGroupId,
    'priceregion',
    '20'  //sort_order
);

$used_in_forms=array();

$used_in_forms[]="adminhtml_customer";
//$used_in_forms[]="checkout_register";
//$used_in_forms[]="customer_account_create";
$used_in_forms[]="customer_account_edit";
//$used_in_forms[]="adminhtml_checkout";
        $attribute->setData("used_in_forms", $used_in_forms)
                ->setData("is_used_for_customer_segment", true)
                ->setData("is_system", 0)
                ->setData("is_user_defined", 1)
                ->setData("is_visible", 1)
                ->setData("sort_order", 100)
                ;
        $attribute->save();
		
$installer->addAttribute("customer", "pricebook",  array(
    "type"     => "text",
    "backend"  => "",
    "label"    => "Price Book",
    "input"    => "select",
    "source"   => "extendedpricing/source_option",
    "visible"  => true,
    "required" => false,
    "default" => "",
    "frontend" => "",
    "unique"     => false
	
        ));

$attribute   = Mage::getSingleton("eav/config")->getAttribute("customer", "pricebook");


$setup->addAttributeToGroup(
    $entityTypeId,
    $attributeSetId,
    $attributeGroupId,
    'pricebook',
    '20'  //sort_order
);

$used_in_forms=array();

$used_in_forms[]="adminhtml_customer";
//$used_in_forms[]="checkout_register";
//$used_in_forms[]="customer_account_create";
$used_in_forms[]="customer_account_edit";
//$used_in_forms[]="adminhtml_checkout";
        $attribute->setData("used_in_forms", $used_in_forms)
                ->setData("is_used_for_customer_segment", true)
                ->setData("is_system", 0)
                ->setData("is_user_defined", 1)
                ->setData("is_visible", 1)
                ->setData("sort_order", 100)
                ;
        $attribute->save();        

$installer->endSetup();

?>