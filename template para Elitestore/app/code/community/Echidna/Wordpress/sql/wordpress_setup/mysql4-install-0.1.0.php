<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('wordpress')};
CREATE TABLE {$this->getTable('requestsample')} (
  `sample_id` int(11) unsigned NOT NULL auto_increment,
  `first_name` varchar(50) default NULL,
  `last_name` varchar(50) default NULL,
  `email` varchar(50) default NULL,
  `phone` int(50) default '0',
  `company` varchar(50) default NULL,
  `job_title` varchar(50) default NULL,
  `industry` varchar(50) default NULL,
  `street_one` varchar(50) default NULL,
  `street_two` varchar(50) default NULL,
  `city` varchar(50) default NULL,
  `state` varchar(50) default NULL,
  `zip` varchar(50) default NULL,
  `counrty` varchar(50) default NULL,
  `best_describes` varchar(50) default NULL,
  `best_describes_other` varchar(50) default NULL,
  `organization_role` varchar(50) default NULL,
  `fr_clothing` varchar(50) default NULL,
  `frcs` varchar(50) default NULL,
  `garment_size` varchar(50) default NULL,
  `garment_color` varchar(50) default NULL,
  `testing_frg` varchar(50) default NULL,
  `fr_number` int(50) default NULL,
  `fr_clothing_price` varchar(50) default NULL,
  `general_comments` varchar(100) default NULL,
  `join_tenacious` varchar(20) default NULL,
  `created_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`sample_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");
$installer->endSetup(); 