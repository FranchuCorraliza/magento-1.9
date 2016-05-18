<?php
/**
 * Mageplace Survey
 *
 * @category	Belitsoft
 * @package		Belitsoft_Survey
 * @copyright	Copyright (c) 2012 Mageplace. (http://www.mageplace.com)
 * @license		http://www.mageplace.com/disclaimer.html
 */

$query = "
ALTER TABLE {$this->getTable('belitsoft_survey/category')}
	ADD `only_for_registered` tinyint(1) NOT NULL DEFAULT 0;

ALTER TABLE `{$this->getTable('belitsoft_survey/category')}`
	ADD `category_url_key` TEXT NOT NULL;

ALTER TABLE `{$this->getTable('belitsoft_survey/category')}`
	ADD `category_meta_keywords` TEXT NOT NULL;

ALTER TABLE `{$this->getTable('belitsoft_survey/category')}`
	ADD `category_meta_description` TEXT NOT NULL;

ALTER TABLE {$this->getTable('belitsoft_survey/survey')}
	ADD `start_date` DATETIME NOT NULL AFTER `update_date`;

ALTER TABLE {$this->getTable('belitsoft_survey/survey')}
	ADD `only_for_registered` tinyint(1) NOT NULL DEFAULT 0;

ALTER TABLE `{$this->getTable('belitsoft_survey/survey')}`
	ADD `survey_url_key` TEXT NOT NULL;

ALTER TABLE `{$this->getTable('belitsoft_survey/survey')}`
	ADD `survey_meta_keywords` TEXT NOT NULL;

ALTER TABLE `{$this->getTable('belitsoft_survey/survey')}`
	ADD `survey_meta_description` TEXT NOT NULL;

ALTER TABLE {$this->getTable('belitsoft_survey/answer')}
	ADD `customer_id` int(10) unsigned NOT NULL AFTER `answer_id`;


CREATE TABLE IF NOT EXISTS `{$this->getTable('belitsoft_survey/category_customer_group')}` (
	`category_id`			int(10) unsigned NOT NULL,
	`customer_group_id`		smallint(5) unsigned NOT NULL,
	PRIMARY KEY (`category_id`,`customer_group_id`),
	CONSTRAINT `FK_SURVEY_CATEGORY_CUSTOMER_GROUP_CATEGORY_ID` FOREIGN KEY (`category_id`) REFERENCES `{$this->getTable('belitsoft_survey/category')}` (`category_id`) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT `FK_SURVEY_CATEGORY_CUSTOMER_GROUP_CUSTOMER_GROUP_ID` FOREIGN KEY (`customer_group_id`) REFERENCES `{$this->getTable('customer_group')}` (`customer_group_id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB COMMENT='Customer Groups and Categories Relations';

CREATE TABLE IF NOT EXISTS `{$this->getTable('belitsoft_survey/survey_customer_group')}` (
	`survey_id`				int(10) unsigned NOT NULL,
	`customer_group_id`		smallint(5) unsigned NOT NULL,
	PRIMARY KEY (`survey_id`,`customer_group_id`),
	CONSTRAINT `FK_SURVEY_SURVEY_CUSTOMER_GROUP_SURVEY_ID` FOREIGN KEY (`survey_id`) REFERENCES `{$this->getTable('belitsoft_survey/survey')}` (`survey_id`) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT `FK_SURVEY_SURVEY_CUSTOMER_GROUP_CUSTOMER_GROUP_ID` FOREIGN KEY (`customer_group_id`) REFERENCES `{$this->getTable('customer_group')}` (`customer_group_id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB COMMENT='Customer Groups and Surveys Relations';

INSERT IGNORE INTO {$this->getTable('belitsoft_survey/config')}
	(`name`, `value`)
VALUES
	('url_prefix', 'survey'),
	('meta_keywords', ''),
	('meta_description', ''),
	('enable_user_edit', '0'),
	('enable_user_view', '0')
;
";

$installer = $this;
$installer->startSetup();
$installer->run($query);
$installer->endSetup();