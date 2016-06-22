<?php
$installer = $this;

$installer->startSetup();

$installer->run("
    DELETE FROM {$installer->getTable('core_config_data')}
    WHERE path in ('payment/servired_standard/redirect_status','payment/servired_standard/order_status');");

$installer->endSetup();

