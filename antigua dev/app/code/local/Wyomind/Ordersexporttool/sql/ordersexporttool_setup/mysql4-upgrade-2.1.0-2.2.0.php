<?php

$installer = $this;

$installer->startSetup();

$installer->run("ALTER TABLE {$this->getTable('ordersexporttool_profiles')} 
MODIFY `file_repeat_for_each` int (1) NOT NULL default '0',
ADD `file_repeat_for_each_increment` int (1)  NULL ,
ADD `file_order_by` int (1) NOT NULL default '0',
ADD `file_order_by_field` int (3) NULL;");




$installer->endSetup();