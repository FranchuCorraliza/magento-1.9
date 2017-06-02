<?php

$installer = $this;

$installer->startSetup();

$installer->run("ALTER TABLE {$this->getTable('ordersexporttool_profils')} 

ADD `file_flag` int(1) NOT NULL default '0' , 
ADD `file_single_export` int(1) NOT NULL default '0';");

$installer->run("RENAME TABLE ordersexporttool_profils    TO ordersexporttool_profiles");

// ajout du champ assignation dans les commandes
if (version_compare(Mage::getVersion(), '1.4.0', '<')) {
    $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'), 'export_flag', 'varchar(100) NOT NULL DEFAULT 0;'
    );
    $attribute = new Mage_Eav_Model_Entity_Setup('core_setup');
    $attribute->addAttribute('order', 'export_flag', array('type' => 'static', 'visible' => true));
} else {
    $installer->getConnection()->addColumn(
            $installer->getTable('sales_flat_order'), 'export_flag', 'varchar(100) NOT NULL DEFAULT 0;'
    );
    $installer->getConnection()->addColumn(
            $installer->getTable('sales/order_grid'), 'export_flag', 'varchar(100) NOT NULL DEFAULT 0;'
    );
}

$installer->endSetup();