<?php
$installer = $this;

$installer->startSetup();

$installer->run("


DROP TABLE IF EXISTS {$this->getTable('manufacturer')};
CREATE TABLE {$this->getTable('manufacturer')}(
  `manufacturer_id` int(11) NOT NULL auto_increment,
  `name` varchar(255) character set utf8 collate utf8_unicode_ci NOT NULL,
  `name_store` varchar(255) character set utf8 collate utf8_unicode_ci NOT NULL,
  `image` varchar(255) character set utf8 collate utf8_unicode_ci NOT NULL,
  `description_short` text character set utf8 collate utf8_unicode_ci NOT NULL,
  `description` text character set utf8 collate utf8_unicode_ci NOT NULL,
  `meta_keywords` varchar(255) character set utf8 collate utf8_unicode_ci default NULL,
  `meta_description` varchar(255) character set utf8 collate utf8_unicode_ci default NULL,
  `featured` tinyint(1) NOT NULL default '0',
  `status` tinyint(1) NOT NULL default '1',
  `ordering` int(11) NOT NULL default '0',
  `created_time` datetime default NULL,
  `update_time` datetime default NULL,
  `page_title` varchar(255) character set utf8 collate utf8_unicode_ci NOT NULL,
  `url_key` varchar(255) default NULL,
  `store_id` int(11) NOT NULL default '0',
  `option_id` int(11) NOT NULL default '0',
  `default_image` tinyint(1) NOT NULL default '1',
  `default_featured` tinyint(1) NOT NULL default '1',
  `default_status` tinyint(1) NOT NULL default '1',
  `default_name_store` tinyint(1) NOT NULL default '1',
  `default_page_title` tinyint(1) NOT NULL default '1',
  `default_description_short` tinyint(1) NOT NULL default '1',
  `default_description` tinyint(1) NOT NULL default '1',
  `default_meta_keywords` tinyint(1) NOT NULL default '1',
  `default_meta_description` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`manufacturer_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

    ");	
$installer->endSetup(); 