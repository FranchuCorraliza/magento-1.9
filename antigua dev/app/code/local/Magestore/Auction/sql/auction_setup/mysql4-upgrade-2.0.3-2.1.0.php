<?php
$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('auction_bid')}
ADD `auctioninfo` text NOT NULL default '',
ADD `auctionlistinfo` text NOT NULL default '';

");

$installer->endSetup();
