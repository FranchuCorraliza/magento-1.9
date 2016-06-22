<?php
$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('auction_last_autobid_run')};
DROP TABLE IF EXISTS {$this->getTable('auction_autobid')};
DROP TABLE IF EXISTS {$this->getTable('auction_watcher')};
DROP TABLE IF EXISTS {$this->getTable('auction_transaction')};
DROP TABLE IF EXISTS {$this->getTable('auction_product_value')};
DROP TABLE IF EXISTS {$this->getTable('auction_bid')};
DROP TABLE IF EXISTS {$this->getTable('auction_product')};

CREATE TABLE {$this->getTable('auction_product')} (
  `productauction_id` int(11) NOT NULL auto_increment,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `init_price` decimal(12,4) NOT NULL default '0.000',
  `reserved_price` decimal(12,4) NOT NULL default '0.000',
  `min_interval_price` decimal(12,4) NOT NULL default '0.000',
  `max_interval_price` decimal(12,4) NOT NULL default '0.000',
  `start_date` date NOT NULL,
  `start_time` varchar(8) NOT NULL default '00:00:00',
  `end_date` date NOT NULL,
  `end_time` varchar(8) NOT NULL default '00:00:00',
  `created_time` datetime default NULL,
  `update_time` datetime default NULL,
  `status` tinyint(1) default '1',
  `is_applied` tinyint(1) NOT NULL DEFAULT '2',
  `limit_time` int(8) NOT NULL default '15',
  `multi_winner` int(8) NOT NULL default '1',
  PRIMARY KEY  (`productauction_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;


CREATE TABLE {$this->getTable('auction_bid')}  (
  `auctionbid_id` int(11) NOT NULL auto_increment,
  `productauction_id` int(11) NOT NULL default '0',
  `product_id` int(11) NOT NULL default '0',
  `product_name` varchar(255) NOT NULL default '',
  `customer_id` int(11) NOT NULL default '0',
  `customer_name` varchar(255) NOT NULL default '',
  `customer_email` varchar(255) NOT NULL default '',
  `customer_phone` varchar(50) default NULL,
  `customer_address` text default NULL,
  `bidder_name` varchar(255) NOT NULL default '',  
  `order_id` int(11) default '0',
  `created_time` varchar(8) default '00:00:00',  
  `created_date` date default NULL,  
  `price` decimal(12,4) NOT NULL default '0.000',  
  `status` tinyint(1) default '1',
  `store_id` smallint(5) unsigned NOT NULL,
  INDEX(`store_id`),
  INDEX ( `productauction_id` ),
  FOREIGN KEY (`store_id`) REFERENCES {$this->getTable('core/store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE, 
  FOREIGN KEY ( `productauction_id` ) REFERENCES {$this->getTable('auction_product')} ( `productauction_id` ) ON DELETE CASCADE ON UPDATE CASCADE,
  PRIMARY KEY  (`auctionbid_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8  ;


CREATE TABLE {$this->getTable('auction_product_value')}  (
  `value_id` int(10) unsigned NOT NULL auto_increment,
  `productauction_id` int(11) NOT NULL,
  `store_id` SMALLINT( 5 ) UNSIGNED NOT NULL,
  `product_id` int(11) NOT NULL,
  `is_applied` tinyint(1) NOT NULL DEFAULT '2', 
  UNIQUE(`productauction_id`,`store_id`),
  INDEX (`store_id`),
  FOREIGN KEY (`productauction_id`) REFERENCES {$this->getTable('auction_product')} (`productauction_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`store_id`) REFERENCES {$this->getTable('core/store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  PRIMARY KEY  (`value_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


CREATE TABLE {$this->getTable('auction_transaction')}  (
  `transaction_id` int(10) unsigned NOT NULL auto_increment,
  `productauction_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  FOREIGN KEY (`productauction_id`) REFERENCES {$this->getTable('auction_product')} (`productauction_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  PRIMARY KEY  (`transaction_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


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

$setup = Mage::getResourceModel('catalog/setup','catalog_setup');
$setup->startSetup();
$setup->removeAttribute('customer','bidder_name');
$setup->endSetup();
  
$attribute = Mage::getModel("eav/entity_attribute");

//customer 
$entity_type = Mage::getSingleton("eav/entity_type")->loadByCode("customer");
$entity_type_id = $entity_type->getId();

$entity_type = Mage::getModel('eav/entity_type')
				->loadByCode('customer');

$entity_set = Mage::getResourceModel('eav/entity_attribute_set_collection')
				->setEntityTypeFilter($entity_type_id);

$set = $entity_set->getFirstItem();

$entity_group = Mage::getResourceModel('eav/entity_attribute_group_collection')
				->setAttributeSetFilter($set->getId());

$group = $entity_group->getFirstItem();

//create attribute `bidder_name`
$data = array();
$data['id'] = null;
$data['entity_type_id'] = $entity_type_id;
$data['attribute_code'] = "bidder_name";
$data['frontend_label'] = "Bidder Name";
$data['backend_type'] = "varchar";
$data['frontend_input'] = "text";
$data['entity_type_id'] = $entity_type_id;
$data['attribute_set_id'] = $set->getId();
$data['attribute_group_id'] = $group->getId();


$attribute->setData($data)
			->setId(null)
			->save()
			;

$installer->endSetup(); 