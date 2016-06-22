<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */

$query = "
CREATE TABLE IF NOT EXISTS `{$this->getTable('belitsoft_survey/category')}` (
	`category_id`			int(10) unsigned NOT NULL AUTO_INCREMENT,
	`parent_id`				int(10) unsigned NULL,
	`category_name`			varchar(255) NOT NULL,
	`category_description`	text NOT NULL,
	`creation_date`			datetime NOT NULL,
	`update_date`			datetime NOT NULL,
	`is_active`				tinyint(1) NOT NULL DEFAULT 1,
	PRIMARY KEY (`category_id`),
	CONSTRAINT `FK_SURVEY_CATEGORY_PARENT_ID` FOREIGN KEY (`parent_id`) REFERENCES `{$this->getTable('belitsoft_survey/category')}` (`category_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=UTF8 COMMENT='Survey Categories';

CREATE TABLE IF NOT EXISTS `{$this->getTable('belitsoft_survey/category_store')}` (
	`category_id`			int(10) unsigned NOT NULL,
	`store_id`				smallint(5) unsigned NOT NULL,
	PRIMARY KEY (`category_id`,`store_id`),
	CONSTRAINT `FK_SURVEY_CATEGORY_STORE_CATEGORY_ID` FOREIGN KEY (`category_id`) REFERENCES `{$this->getTable('belitsoft_survey/category')}` (`category_id`) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT `FK_SURVEY_CATEGORY_STORE_STORE_ID` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core/store')}` (`store_id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB COMMENT='Stores and Categories Relations';

CREATE TABLE IF NOT EXISTS {$this->getTable('belitsoft_survey/survey')} (
	`survey_id`				int(10) unsigned NOT NULL AUTO_INCREMENT,
	`survey_name`			varchar(255) NOT NULL,
	`multipage`				tinyint(1) NOT NULL DEFAULT '0',
	`survey_description`	text NOT NULL DEFAULT '',
	`survey_final_page_text`	text NOT NULL DEFAULT '',
	`creation_date`			datetime DEFAULT NULL,
	`update_date`			datetime DEFAULT NULL,
	`expired_date`			datetime DEFAULT NULL,
	`is_active`				tinyint(1) NOT NULL DEFAULT '1',
	PRIMARY KEY (`survey_id`)
) ENGINE=InnoDB DEFAULT CHARSET=UTF8 COMMENT='Surveys';

CREATE TABLE IF NOT EXISTS `{$this->getTable('belitsoft_survey/survey_store')}` (
	`survey_id`				int(10) unsigned NOT NULL,
	`store_id`				smallint(5) unsigned NOT NULL,
	PRIMARY KEY (`survey_id`,`store_id`),
	CONSTRAINT `FK_SURVEY_SURVEY_STORE_SURVEY_ID` FOREIGN KEY (`survey_id`) REFERENCES `{$this->getTable('belitsoft_survey/survey')}` (`survey_id`) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT `FK_SURVEY_SURVEY_STORE_STORE_ID` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core/store')}` (`store_id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB COMMENT='Stores and Surveys Relations';

CREATE TABLE IF NOT EXISTS `{$this->getTable('belitsoft_survey/category_survey')}` (
	`category_id`			int(10) unsigned NOT NULL,
	`survey_id`				int(10) unsigned NOT NULL,
	PRIMARY KEY (`category_id`,`survey_id`),
	CONSTRAINT `FK_SURVEY_CATEGORY_SURVEY_CATEGORY_ID` FOREIGN KEY (`category_id`) REFERENCES `{$this->getTable('belitsoft_survey/category')}` (`category_id`) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT `FK_SURVEY_CATEGORY_SURVEY_SURVEY_ID` FOREIGN KEY (`survey_id`) REFERENCES `{$this->getTable('belitsoft_survey/survey')}` (`survey_id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB COMMENT='Categories and Surveys Relations';

CREATE TABLE IF NOT EXISTS `{$this->getTable('belitsoft_survey/question')}` (
	`question_id`			int(10) unsigned NOT NULL AUTO_INCREMENT,
	`survey_id`				int(10) unsigned NOT NULL,
	`question_type`			varchar(20) NOT NULL,
	`question_text`			text NOT NULL DEFAULT '',
	`creation_date`			datetime DEFAULT NULL,
	`update_date`			datetime DEFAULT NULL,
	`sort_order`			int(10) unsigned NOT NULL DEFAULT '0',
	`is_active`				tinyint(1) NOT NULL DEFAULT 1,
	PRIMARY KEY (`question_id`),
	CONSTRAINT `FK_SURVEY_QUESTION_SURVEY_ID` FOREIGN KEY (`survey_id`) REFERENCES `{$this->getTable('belitsoft_survey/survey')}` (`survey_id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Survey Questions';

CREATE TABLE IF NOT EXISTS `{$this->getTable('belitsoft_survey/field')}` (
	`field_id`				int(10) unsigned NOT NULL AUTO_INCREMENT,
	`question_id`			int(10) unsigned NOT NULL,
	`field_text`			text NOT NULL DEFAULT '',
	`is_main`				tinyint(1) NOT NULL DEFAULT 1,
	`sort_order`			int(10) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`field_id`),
	CONSTRAINT `FK_SURVEY_FIELD_QUESTION_ID` FOREIGN KEY (`question_id`) REFERENCES `{$this->getTable('belitsoft_survey/question')}` (`question_id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Question Fields';

CREATE TABLE IF NOT EXISTS `{$this->getTable('belitsoft_survey/answer')}` (
	`answer_id`				int(10) unsigned NOT NULL AUTO_INCREMENT,
	`start_id`				int(10) unsigned NOT NULL,
	`survey_id`				int(10) unsigned NOT NULL,
	`question_id`			int(10) unsigned NOT NULL,
	`answer`				int(10) unsigned NOT NULL,
	`answer_field`			int(10) unsigned NOT NULL DEFAULT '0',
	`answer_text`			text NOT NULL DEFAULT '',
	`creation_date`			datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`answer_id`),
	KEY `start_id` (`start_id`),
	CONSTRAINT `FK_SURVEY_ANSWER_SURVEY_ID` FOREIGN KEY (`survey_id`) REFERENCES `{$this->getTable('belitsoft_survey/survey')}` (`survey_id`) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT `FK_SURVEY_ANSWER_QUESTION_ID` FOREIGN KEY (`question_id`) REFERENCES `{$this->getTable('belitsoft_survey/question')}` (`question_id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='User Answers';

";

$installer = $this;
$installer->startSetup();
$installer->run($query);
$installer->endSetup();