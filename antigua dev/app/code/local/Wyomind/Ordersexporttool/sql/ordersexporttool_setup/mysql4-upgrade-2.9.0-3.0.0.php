<?php

$installer = $this;

$installer->startSetup();

$installer->run("ALTER TABLE {$this->getTable('ordersexporttool_profiles')} 
ADD    `file_product_type` varchar(150) NULL;");

$installer->endSetup();