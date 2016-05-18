<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */

$query = "
CREATE TABLE IF NOT EXISTS {$this->getTable('belitsoft_survey/config')} (
	`name` varchar(50) NOT NULL ,
	`value` text NOT NULL DEFAULT '',
	PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT IGNORE INTO {$this->getTable('belitsoft_survey/config')} (`name`, `value`) VALUES
('enable_user_check', '0'),
('cookie_lifetime', '0'),
('graphic_type', 'Pie'),
('pdf_font', 'freeserif')
;
";

$installer = $this;
$installer->startSetup();
$installer->run($query);
$installer->endSetup();