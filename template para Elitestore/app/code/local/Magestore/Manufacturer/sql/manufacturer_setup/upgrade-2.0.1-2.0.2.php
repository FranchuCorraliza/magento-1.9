<?php
 
$installer = $this;
$connection = $installer->getConnection();
 
$installer->startSetup();
 
$installer->run("ALTER TABLE `manufacturer`
	ADD COLUMN `newdesigner` TINYINT(1) NOT NULL DEFAULT '0',
	ADD COLUMN `default_newdesigner` TINYINT(1) NOT NULL DEFAULT '1';");	
 
$installer->endSetup();