<?php
$installer = $this;
$installer->startSetup();
$installer->run("
DROP TABLE IF EXISTS {$this->getTable('sizechart')};
CREATE TABLE {$this->getTable('sizechart')} (
`sizechart_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
`tallaje` VARCHAR(255) NOT NULL,
`idequivalente` TEXT NOT NULL,
`talla` TEXT NOT NULL,
`categoria` TEXT NOT NULL,
`status` smallint(6) NOT NULL DEFAULT '0',
`created_time` DATETIME NULL DEFAULT NULL,
`update_time` DATETIME NULL DEFAULT NULL,
PRIMARY KEY (`sizechart_id`)
) 
ENGINE=InnoDB
COLLATE='utf8_general_ci'
;
");
$installer->endSetup();
?>