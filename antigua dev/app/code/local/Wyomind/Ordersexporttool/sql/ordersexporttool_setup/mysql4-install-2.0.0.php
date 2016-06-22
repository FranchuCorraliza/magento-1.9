<?php

$installer = $this;

$installer->startSetup();

$installer->run('DROP TABLE IF EXISTS ' . $this->getTable('ordersexporttool_profils'));


$installer->run('

CREATE TABLE IF NOT EXISTS `' . $this->getTable('ordersexporttool_profils') . '` (
  `file_id` int(11) NOT NULL auto_increment,
  `file_name` varchar(20) NOT NULL,
  `file_type` tinyint(3) NOT NULL,
  `file_path` varchar(255) NOT NULL default \'/export/orders/\',
  `file_store_id` varchar(255) NOT NULL default \'0\',
  `file_status` int(1) NOT NULL default \'0\',
  `file_updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `file_last_exported_id` int(9) DEFAULT 100000000,
  `file_first_exported_id` int(9) NULL ,
  `file_automatically_update_last_order_id` int(1) NOT NULL default \'1\',
  `file_date_format` varchar(50) NOT NULL default \'yyyy-mm-dd\',
  `file_include_header` int(1) NOT NULL default \'0\',
  `file_repeat_for_each` varchar (50) NOT NULL default \'order\',
  `file_header` text,
  `file_body` text,
  `file_footer` text,
  `file_separator` char(3) default NULL,
  `file_protector` char(1) default NULL,
  `file_enclose_data` int(1) NOT NULL default \'1\',
  `file_attributes` text,
  `file_states` text,
  `file_customer_groups` text,
  `file_scheduled_task` varchar(900) NOT NULL DEFAULT \'{"days":["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"],"hours":["00:00","04:00","08:00","12:00","16:00","20:00"]}\',
  `file_ftp_enabled` INT(1) DEFAULT \'0\',
  `file_ftp_host` VARCHAR(300) DEFAULT NULL,
  `file_ftp_login` VARCHAR(300) DEFAULT NULL,
  `file_ftp_password` VARCHAR(300) DEFAULT NULL,
  `file_ftp_active` INT(1) DEFAULT \'0\',
  `file_ftp_dir` VARCHAR(300) DEFAULT NULL,
  PRIMARY KEY  (`file_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;
');


$installer->run('DROP TABLE IF EXISTS ' . $this->getTable('ordersexporttool_attributes'));


$installer->run('

CREATE TABLE IF NOT EXISTS `' . $this->getTable('ordersexporttool_attributes') . '` (
    `attribute_id` int(11) NOT NULL auto_increment,
    `attribute_name` varchar(100) NOT NULL,
    `attribute_order_item` text,
    `attribute_order_address` text,
    `attribute_order_payment` text,
    `attribute_invoice` text,
    `attribute_shipment` text,
    `attribute_creditmemo` text,
   
    `attribute_script` text,
    PRIMARY KEY  (`attribute_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;
');


/*$server = (string) Mage::getConfig()->getNode('global/resources/default_setup/connection/host');
$user = (string) Mage::getConfig()->getNode('global/resources/default_setup/connection/username');
$pwd = (string) Mage::getConfig()->getNode('global/resources/default_setup/connection/password');
$cnx = mysql_connect($server, $user, $pwd);
mysql_select_db($user, $cnx);*/
            
//$installer->run('insert into `' . $this->getTable('ordersexporttool_profils') . '');

//$installer->run('insert into `' . $this->getTable('ordersexporttool_attributes') .'');

$installer->endSetup();

