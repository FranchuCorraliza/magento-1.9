<?php

$installer = $this;

$installer->startSetup();
$installer->run('
 ALTER TABLE ' . $this->getTable('ordersexporttool_profiles')).'
 ADD file_update_status INT(1) NOT NULL DEFAULT \'0\',
 ADD file_update_status_to VARCHAR(255),
 ADD file_update_status_message VARCHAR(500);');


$installer->endSetup();
