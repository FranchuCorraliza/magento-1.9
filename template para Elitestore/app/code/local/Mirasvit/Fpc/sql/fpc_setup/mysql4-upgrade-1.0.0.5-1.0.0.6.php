<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Full Page Cache
 * @version   1.0.32
 * @build     662
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



$installer = $this;

$installer->startSetup();
$installer->run("DROP TABLE IF EXISTS `{$installer->getTable('fpc/log_aggregated')}`;");
$installer->run("
CREATE TABLE `{$installer->getTable('fpc/log_aggregated')}` (
    `id`               int(11)       NOT NULL AUTO_INCREMENT COMMENT 'Log Id',
    `period`           date          NOT NULL DEFAULT '0000-00-00',
    `from_cache`       int(11)       NOT NULL DEFAULT '0' COMMENT 'From Cache',
    `response_time`    decimal(12,4) NOT NULL DEFAULT '0.0000' COMMENT 'Response Time',
    `hits`             int(11)       NOT NULL DEFAULT '0.0000' COMMENT 'Hits',
    PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='FPC Log Aggregated';
");
$installer->endSetup();

Mage::getModel('core/variable')
    ->loadByCode(Mirasvit_Fpc_Model_Config::OPTIMAL_CONFIG_MESSAGE)
    ->setCode(Mirasvit_Fpc_Model_Config::OPTIMAL_CONFIG_MESSAGE)
    ->setName('Show FPC Optimal Config Message')
    ->setPlainValue(1)
    ->save();
