<?php
$installer = $this;
$installer->startSetup();


$installer->addAttribute("catalog_category", "sale_id",  array(
    "type"     => "varchar",
    "backend"  => "",
    "frontend" => "",
    "label"    => "Sale Category Id",
    "input"    => "text",
    "class"    => "",
    "source"   => "",
    "global"   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    "visible"  => true,
    "required" => false,
    "user_defined"  => false,
    "default" => "",
    "searchable" => false,
    "filterable" => false,
    "comparable" => false,
	
    "visible_on_front"  => false,
    "unique"     => false,
    "note"       => "Id de la categoría equivalente en la rama Sale"

	));

$installer->addAttribute("catalog_category", "outlet_id",  array(
    "type"     => "varchar",
    "backend"  => "",
    "frontend" => "",
    "label"    => "Outlet Category Id",
    "input"    => "text",
    "class"    => "",
    "source"   => "",
    "global"   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    "visible"  => true,
    "required" => false,
    "user_defined"  => false,
    "default" => "",
    "searchable" => false,
    "filterable" => false,
    "comparable" => false,
	
    "visible_on_front"  => false,
    "unique"     => false,
    "note"       => "Id de la categoría equivalente en la rama Outlet"

	));
$installer->endSetup();
	 