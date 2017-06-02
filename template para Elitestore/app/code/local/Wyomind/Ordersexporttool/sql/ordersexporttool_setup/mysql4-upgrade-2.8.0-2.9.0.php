<?php

$installer = $this;

$installer->startSetup();

$installer->run("ALTER TABLE {$this->getTable('ordersexporttool_profiles')} 
ADD  `file_use_sftp` int(1) not null default 0;");

$installer->endSetup();