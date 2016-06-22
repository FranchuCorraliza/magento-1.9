<?php
$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('auction_product')} 
	ADD `reserved_price` decimal(12,4) NOT NULL default '0.000',
	ADD `limit_time` int(8) NOT NULL default '15',
	ADD `multi_winner` int(8) NOT NULL default '1'
	;

DROP TABLE IF EXISTS {$this->getTable('auction_last_autobid_run')};
DROP TABLE IF EXISTS {$this->getTable('auction_watcher')};
DROP TABLE IF EXISTS {$this->getTable('auction_autobid')};

CREATE TABLE {$this->getTable('auction_autobid')}  (
  `autobid_id` int(11) NOT NULL auto_increment,
  `productauction_id` int(11) NOT NULL default '0',
  `customer_id` int(11) NOT NULL default '0',
  `customer_name` varchar(255) NOT NULL default '',
  `customer_email` varchar(255) NOT NULL default '',
  `bidder_name` varchar(255) NOT NULL default '',  
  `created_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `price` decimal(12,4) NOT NULL default '0.000',  
  `store_id` smallint(5) unsigned NOT NULL,
  INDEX(`store_id`),
  INDEX ( `productauction_id` ),
  FOREIGN KEY (`store_id`) REFERENCES {$this->getTable('core/store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE, 
  FOREIGN KEY ( `productauction_id` ) REFERENCES {$this->getTable('auction_product')} ( `productauction_id` ) ON DELETE CASCADE ON UPDATE CASCADE,
  PRIMARY KEY  (`autobid_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8  ;

CREATE TABLE {$this->getTable('auction_watcher')}  (
  `watcher_id` int(11) NOT NULL auto_increment,
  `productauction_id` int(11) NOT NULL default '0',
  `customer_id` int(11) NOT NULL default '0',
  `customer_name` varchar(255) NOT NULL default '',
  `customer_email` varchar(255) NOT NULL default '', 
  `status` tinyint(1) default '1',
  `store_id` smallint(5) unsigned NOT NULL,
  INDEX(`store_id`),
  INDEX ( `productauction_id` ),
  FOREIGN KEY (`store_id`) REFERENCES {$this->getTable('core/store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE, 
  FOREIGN KEY ( `productauction_id` ) REFERENCES {$this->getTable('auction_product')} ( `productauction_id` ) ON DELETE CASCADE ON UPDATE CASCADE,
  PRIMARY KEY  (`watcher_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8  ;

CREATE TABLE {$this->getTable('auction_last_autobid_run')}  (
  `last_run_autobid_id` int(11) UNSIGNED NOT NULL auto_increment,
  `productauction_id` int(11) UNSIGNED NOT NULL default '0',
  `autobid_id` int(11) UNSIGNED NOT NULL default '0',
  `price` decimal(12,4) NOT NULL default '0.000',
  `created_time` datetime NOT NULL default '0000-00-00 00:00:00',
  INDEX (`productauction_id`),
  INDEX (`autobid_id`),
  
  FOREIGN KEY (`autobid_id`) REFERENCES {$this->getTable('auction_autobid')} (`autobid_id`) ON DELETE CASCADE ON UPDATE CASCADE, 
  FOREIGN KEY ( `productauction_id` ) REFERENCES {$this->getTable('auction_product')} ( `productauction_id` ) ON DELETE CASCADE ON UPDATE CASCADE,
  PRIMARY KEY  (`last_run_autobid_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

    ");
	
$installer->endSetup(); 