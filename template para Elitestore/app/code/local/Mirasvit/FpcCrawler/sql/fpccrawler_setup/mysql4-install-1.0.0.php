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

Mage::helper('mstcore')->copyConfigData('fpc/crawler/enabled', 'fpccrawler/crawler/enabled');
Mage::helper('mstcore')->copyConfigData('fpc/crawler/max_threads', 'fpccrawler/crawler/max_threads');
Mage::helper('mstcore')->copyConfigData('fpc/crawler/max_urls_per_run', 'fpccrawler/crawler/max_urls_per_run');
Mage::helper('mstcore')->copyConfigData('fpc/crawler/schedule', 'fpccrawler/crawler/schedule');
Mage::helper('mstcore')->copyConfigData('fpc/crawler/sort_crawler_urls', 'fpccrawler/crawler/sort_crawler_urls');
Mage::helper('mstcore')->copyConfigData('fpc/crawler/sort_by_page_type', 'fpccrawler/crawler/sort_by_page_type');
Mage::helper('mstcore')->copyConfigData('fpc/crawler/sort_by_product_attribute', 'fpccrawler/crawler/sort_by_product_attribute');

$installer->run("DROP TABLE IF EXISTS `{$installer->getTable('fpccrawler/crawlerlogged_url')}`;");
$installer->run("
CREATE TABLE `{$installer->getTable('fpccrawler/crawlerlogged_url')}` (
    `url_id`                    int(11)       NOT NULL AUTO_INCREMENT COMMENT 'Url Id',
    `url`                       text          NOT NULL,
    `cache_id`                  varchar(255)  NOT NULL,
    `status`                    int(11)       NOT NULL DEFAULT 0,
    `sort_by_page_type`         VARCHAR(255)  NOT NULL,
    `sort_by_product_attribute` INT(11)       NOT NULL DEFAULT 1000,
    `customer_group_id`         smallint(5),
    `store_id`                  smallint(5)   NOT NULL DEFAULT 0,
    `currency`                  varchar(3),
    `mobile_group`              varchar(100)  DEFAULT NULL,
    `rate`                      int(11)       NOT NULL DEFAULT 0,
    `created_at`                datetime      NOT NULL DEFAULT '0000-00-00 00:00:00',
    `updated_at`                datetime      NOT NULL DEFAULT '0000-00-00 00:00:00',
    `checked_at`                datetime      NOT NULL DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY (`url_id`),
    KEY `urllogged` (`url`(333)),
    KEY `customer_group_id` (`customer_group_id`),
    KEY `store_id` (`store_id`),
    KEY `currency` (`currency`),
    KEY `mobile_group` (`mobile_group`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='FPC Log';
");

$installer->run("DROP TABLE IF EXISTS `{$installer->getTable('fpccrawler/crawler_url')}`;");
$installer->run("
CREATE TABLE `{$installer->getTable('fpccrawler/crawler_url')}` (
    `url_id`                    int(11)       NOT NULL AUTO_INCREMENT COMMENT 'Url Id',
    `url`                       text          NOT NULL,
    `cache_id`                  varchar(255)  NOT NULL,
    `status`                    int(11)       NOT NULL DEFAULT 0,
    `sort_by_page_type`         VARCHAR(255)  NOT NULL,
    `sort_by_product_attribute` INT(11) NOT   NULL DEFAULT 1000,
    `customer_group_id`         smallint(5)   NOT NULL DEFAULT 0,
    `store_id`                  smallint(5)   NOT NULL DEFAULT 0,
    `currency`                  varchar(3),
    `mobile_group`              varchar(100)  DEFAULT NULL,
    `rate`                      int(11)       NOT NULL DEFAULT 0,
    `created_at`                datetime      NOT NULL DEFAULT '0000-00-00 00:00:00',
    `updated_at`                datetime      NOT NULL DEFAULT '0000-00-00 00:00:00',
    `checked_at`                datetime      NOT NULL DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY (`url_id`),
    KEY `url` (`url`(333)),
    KEY `store_id` (`store_id`),
    KEY `currency` (`currency`),
    KEY `mobile_group` (`mobile_group`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='FPC Log';
");

$installer->endSetup();
