<?php

$installer = $this;

$installer->startSetup();

$installer->run("ALTER TABLE {$this->getTable('ordersexporttool_profiles')} 
                MODIFY `file_last_exported_id`  VARCHAR (20) DEFAULT '0'"
);



$installer->endSetup();