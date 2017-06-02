
<?php

$installer = $this;

$installer->startSetup();

$installer->run("ALTER TABLE {$this->getTable('ordersexporttool_profiles')} 
ADD  `file_product_relation` varchar(12) default 'all';");

$installer->endSetup();