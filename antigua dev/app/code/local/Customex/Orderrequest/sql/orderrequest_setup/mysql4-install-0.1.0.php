<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('orderrequest')};
CREATE TABLE {$this->getTable('orderrequest')} (
  `orderrequest_id` int(11) unsigned NOT NULL auto_increment,
  `product_id` int(11) unsigned NOT NULL,
  `product_name` varchar(255) NOT NULL default '',
  `company_name` varchar(255) NOT NULL default '',
  `personal_name` varchar(255) NOT NULL default '',
  `address` varchar(255) NOT NULL default '',
  `zipcode` varchar(255) NOT NULL default '',
  `city` varchar(255) NOT NULL default '',
  `country_id` varchar(20) NOT NULL default '',
  `phone` varchar(255) NOT NULL default '',
  `fax` varchar(255) NOT NULL default '',
  `customer_email` varchar(255) NOT NULL default '',
  `website` varchar(255) NOT NULL default '',
  `detail` text NOT NULL default '',
  `store_id` varchar(20) NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`orderrequest_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 