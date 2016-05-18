<?php
/**
 * Media Rocks GbR
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA that is bundled with 
 * this package in the file MEDIAROCKS-LICENSE-COMMUNITY.txt.
 * It is also available through the world-wide-web at this URL:
 * http://solutions.mediarocks.de/MEDIAROCKS-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package is designed for Magento COMMUNITY edition. 
 * Media Rocks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Media Rocks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please send an email to support@mediarocks.de
 *
 */

/**
 * NewsletterExtended database setup
 *
 * @category   Mediarocks
 * @package    Mediarocks_NewsletterExtended
 * @author     Media Rocks Developer
 */

$installer = $this;
$installer->startSetup();

// add additional columns to "newsletter_subscriber" table
$tableName = $installer->getTable('newsletter_subscriber');
$installer->getConnection()->addColumn($tableName, 'subscriber_gender', array(
    'nullable' => true,
    'length' => 1,
    'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'comment' => 'added from extension ExtendedNewsletterSubscription'
));
$installer->getConnection()->addColumn($tableName, 'subscriber_prefix', array(
    'nullable' => true,
    'length' => 255,
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'comment' => 'added from extension ExtendedNewsletterSubscription'
));
$installer->getConnection()->addColumn($tableName, 'subscriber_firstname', array(
    'nullable' => true,
    'length' => 255,
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'comment' => 'added from extension ExtendedNewsletterSubscription'
));
$installer->getConnection()->addColumn($tableName, 'subscriber_lastname', array(
    'nullable' => true,
    'length' => 255,
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'comment' => 'added from extension ExtendedNewsletterSubscription'
));
$installer->getConnection()->addColumn($tableName, 'subscriber_suffix', array(
    'nullable' => true,
    'length' => 255,
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'comment' => 'added from extension ExtendedNewsletterSubscription'
));

$installer->endSetup();