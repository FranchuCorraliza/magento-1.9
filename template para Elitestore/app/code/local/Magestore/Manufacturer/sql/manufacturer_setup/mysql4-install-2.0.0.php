<?php
$installer = $this;

$installer->startSetup();

$installer->run("


DROP TABLE IF EXISTS {$this->getTable('manufacturer')};
CREATE TABLE {$this->getTable('manufacturer')}(
  `manufacturer_id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci',
  `name_store` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci',
  `image` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci',
  `description_short` TEXT NOT NULL COLLATE 'utf8_unicode_ci',
  `description` TEXT NOT NULL COLLATE 'utf8_unicode_ci',
  `meta_keywords` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
  `meta_description` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
  `featured` TINYINT(1) NOT NULL DEFAULT '0',
  `status` TINYINT(1) NOT NULL DEFAULT '1',
  `ordering` INT(11) NOT NULL DEFAULT '0',
  `created_time` DATETIME NULL DEFAULT NULL,
  `update_time` DATETIME NULL DEFAULT NULL,
  `page_title` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci',
  `url_key` VARCHAR(255) NULL DEFAULT NULL,
  `store_id` INT(11) NOT NULL DEFAULT '0',
  `option_id` INT(11) NOT NULL DEFAULT '0',
  `default_image` TINYINT(1) NOT NULL DEFAULT '1',
  `default_featured` TINYINT(1) NOT NULL DEFAULT '1',
  `default_status` TINYINT(1) NOT NULL DEFAULT '1',
  `default_name_store` TINYINT(1) NOT NULL DEFAULT '1',
  `default_page_title` TINYINT(1) NOT NULL DEFAULT '1',
  `default_description_short` TINYINT(1) NOT NULL DEFAULT '1',
  `default_description` TINYINT(1) NOT NULL DEFAULT '1',
  `default_meta_keywords` TINYINT(1) NOT NULL DEFAULT '1',
  `default_meta_description` TINYINT(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`manufacturer_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;");	
$installer->endSetup(); 