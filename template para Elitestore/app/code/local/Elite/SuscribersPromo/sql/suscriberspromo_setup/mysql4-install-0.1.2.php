<?php 
$installer = $this;
$installer->startSetup();

// add additional columns to "newsletter_subscriber" table
$tableName = $installer->getTable('newsletter_subscriber');
$installer->getConnection()->addColumn($tableName, 'subscriber_promo', array(
    'nullable' => true,
    'length' => 1,
    'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
	'value' => 0,
    'comment' => 'added from extension Suscriberspromo para saber si se ha enviado codigo promocional o aun no'
));


$installer->endSetup();