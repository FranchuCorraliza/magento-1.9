<?php

$installer = $this;

$installer->startSetup();

$installer->run("ALTER TABLE {$this->getTable('ordersexporttool_profiles')} 
ADD  `file_encoding` varchar(40) NOT NULL,
ADD  `file_extra_header` text,
ADD `file_incremential_column` INT(1) NOT NULL default '0',
ADD `file_incremential_column_name` VARCHAR (50) NULL;");

$installer->endSetup();
