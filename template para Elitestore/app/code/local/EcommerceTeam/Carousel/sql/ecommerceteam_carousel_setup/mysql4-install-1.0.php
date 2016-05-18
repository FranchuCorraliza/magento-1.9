<?php
/**
 * Products Carousel - Magento Extension
 *
 * @package:     ProductsCarousel
 * @category:    EcommerceTeam
 * @copyright:   Copyright 2012 EcommerceTeam Inc. (http://www.ecommerce-team.com)
 * @version:     1.0.0
 */

$this->startSetup();
$this->addAttribute('catalog_product', 'home_pic', array(
        'group'             => 'General',
        'type'              => 'int',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'Home Pic',
        'input'             => 'boolean',
        'class'             => '',
        'source'            => '',
        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible'           => true,
        'required'          => false,
        'user_defined'      => true,
        'default'           => '0',
        'searchable'        => false,
        'filterable'        => false,
        'comparable'        => false,
        'visible_on_front'  => false,
        'unique'            => false,
        'apply_to'          => 'simple,configurable,virtual,bundle,downloadable',
        'is_configurable'   => false
    ));
$this->endSetup();
