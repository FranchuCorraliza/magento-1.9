<?php
 
$installer = $this;
$connection = $installer->getConnection();
 
$installer->startSetup();
 
$installer->run("ALTER TABLE `manufacturer`
	CHANGE COLUMN `description_short` `theicons` TEXT NOT NULL COLLATE 'utf8_unicode_ci',
	CHANGE COLUMN `default_description_short` `default_theicons` TEXT NOT NULL COLLATE 'utf8_unicode_ci',
	ADD COLUMN `imagemanufacturer2` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci',
	ADD COLUMN `idlinea1` TEXT NULL COLLATE 'utf8_unicode_ci',
	ADD COLUMN `imagelinea1` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci',
	ADD COLUMN `idlinea2` TEXT NULL COLLATE 'utf8_unicode_ci',
	ADD COLUMN `imagelinea2` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci',
	ADD COLUMN `idlinea3` TEXT NULL COLLATE 'utf8_unicode_ci',
	ADD COLUMN `imagelinea3` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci',
	ADD COLUMN `idlinea4` TEXT NULL COLLATE 'utf8_unicode_ci',
	ADD COLUMN `imagelinea4` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci',
	ADD COLUMN `imagerunway` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci',
	ADD COLUMN `urlblog` TEXT NULL COLLATE 'utf8_unicode_ci',
	ADD COLUMN `idsubcat` TEXT NULL COLLATE 'utf8_unicode_ci',
	ADD COLUMN `titulodesc1` TEXT NULL COLLATE 'utf8_unicode_ci',
	ADD COLUMN `descripcion1` TEXT NULL COLLATE 'utf8_unicode_ci',
	ADD COLUMN `titulodesc2` TEXT NULL COLLATE 'utf8_unicode_ci',
	ADD COLUMN `descripcion2` TEXT NULL COLLATE 'utf8_unicode_ci',
	ADD COLUMN `genero` TEXT NULL COLLATE 'utf8_unicode_ci',
	ADD COLUMN `tipologia` TEXT NULL COLLATE 'utf8_unicode_ci',
	ADD COLUMN `default_idlinea1` TINYINT(1) NOT NULL DEFAULT '1',
	ADD COLUMN `default_imagemanufacturer2` TINYINT(1) NOT NULL DEFAULT '1',
	ADD COLUMN `default_imagelinea1` TINYINT(1) NOT NULL DEFAULT '1',
	ADD COLUMN `default_idlinea2` TINYINT(1) NOT NULL DEFAULT '1',
	ADD COLUMN `default_imagelinea2` TINYINT(1) NOT NULL DEFAULT '1',
	ADD COLUMN `default_idlinea3` TINYINT(1) NOT NULL DEFAULT '1',
	ADD COLUMN `default_imagelinea3` TINYINT(1) NOT NULL DEFAULT '1',
	ADD COLUMN `default_idlinea4` TINYINT(1) NOT NULL DEFAULT '1',
	ADD COLUMN `default_imagelinea4` TINYINT(1) NOT NULL DEFAULT '1',
	ADD COLUMN `default_imagerunway` TINYINT(1) NOT NULL DEFAULT '1',
	ADD COLUMN `default_urlblog` TINYINT(1) NOT NULL DEFAULT '1',
	ADD COLUMN `default_idsubcat` TINYINT(1) NOT NULL DEFAULT '1',
	ADD COLUMN `default_titulodesc1` TINYINT(1) NOT NULL DEFAULT '1',
	ADD COLUMN `default_descripcion1` TINYINT(1) NOT NULL DEFAULT '1',
	ADD COLUMN `default_titulodesc2` TINYINT(1) NOT NULL DEFAULT '1',
	ADD COLUMN `default_descripcion2` TINYINT(1) NOT NULL DEFAULT '1',
	ADD COLUMN `default_genero` TINYINT(1) NOT NULL DEFAULT '1',
	ADD COLUMN `default_tipologia` TINYINT(1) NOT NULL DEFAULT '1'");	
 
$installer->endSetup();