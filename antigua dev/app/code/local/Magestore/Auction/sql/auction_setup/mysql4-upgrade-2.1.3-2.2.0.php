<?php
$installer = $this;

$installer->startSetup();

$installer->run("
    ALTER TABLE {$this->getTable('auction_last_autobid_run')} ENGINE=InnoDB;
    ALTER TABLE {$this->getTable('auction_autobid')} ENGINE=InnoDB;
    ALTER TABLE {$this->getTable('auction_watcher')} ENGINE=InnoDB;
    ALTER TABLE {$this->getTable('auction_transaction')} ENGINE=InnoDB;
    ALTER TABLE {$this->getTable('auction_product_value')} ENGINE=InnoDB;
    ALTER TABLE {$this->getTable('auction_bid')} ENGINE=InnoDB;
    ALTER TABLE {$this->getTable('auction_product')} ENGINE=InnoDB;

    ALTER TABLE {$this->getTable('auction_product')} 
        ADD `featured` tinyint(1) NOT NULL default '2',
	ADD `allow_buyout` tinyint(1) NOT NULL default '1',
	ADD `day_to_buy` int NOT NULL default '0'
	;
    
    ALTER TABLE {$this->getTable('auction_transaction')} 
	ADD `transaction_price` decimal(12,4) NOT NULL default '0.000'
	;
   
    DROP TABLE IF EXISTS {$this->getTable('auction_email')};
    CREATE TABLE {$this->getTable('auction_email')} (
        `auctionemail_id` int(11) NOT NULL auto_increment,
        `customer_id` int(11) NOT NULL default '0',
        `place_bid` tinyint(1) NOT NULL default '1',
        `place_autobid` tinyint(1) NOT NULL default '1',
        `overbid` tinyint(1) NOT NULL default '1',
        `overautobid` tinyint(1) NOT NULL default '1',
        `cancel_bid` tinyint(1) NOT NULL default '1',
        `highest_bid` tinyint(1) NOT NULL default '1',
        PRIMARY KEY  (`auctionemail_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 ");
$installer->endSetup(); 