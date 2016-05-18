<?php
$this->startSetup();
$this->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'custom_attribute', array(
    'group'         => 'General',
    'input'         => 'textarea',
    'type'          => 'text',
    'label'         => 'Custom attribute',
    'backend'       => '',
    'visible'       => true,
    'required'      => false,
    'wysiwyg_enabled' => true,
    'visible_on_front' => true,
    'is_html_allowed_on_front' => true,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));
$this->endSetup();

$this = new Mage_Eav_Model_Entity_Setup('core_setup');
 
$this->startSetup();
 
$this->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'additional_description',
    array(
        'backend'                   => '',
        'global'                    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'group'                     => 'General',
        'input'                     => 'textarea',
        'is_html_allowed_on_front'  => true,
        'is_wysiwyg_enabled'        => true,
        'label'                     => 'Additional Description',
        'position'                  => 100,
        'required'                  => false,
        'type'                      => 'text',
        'user_defined'              => true,
        'visible'                   => true,
        'visible_on_front'          => true,
    )
);
 
$this->endSetup();