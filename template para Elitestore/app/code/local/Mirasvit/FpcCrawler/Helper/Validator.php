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



class Mirasvit_FpcCrawler_Helper_Validator extends Mirasvit_MstCore_Helper_Validator_Abstract
{
    public function testMirasvitCrc()
    {
        $modules = array('FpcCrawler');

        return Mage::helper('mstcore/validator_crc')->testMirasvitCrc($modules);
    }

    public function testTablesExists()
    {
        $result = self::SUCCESS;
        $title = 'FPCCrawler: Required tables exist';
        $description = array();

        $tables = array(
            'fpccrawler/crawler_url',
            'fpccrawler/crawlerlogged_url',
        );

        foreach ($tables as $table) {
            if (!$this->dbTableExists($table)) {
                $description[] = "Table '$table' does not exist";
                $result = self::FAILED;
            }
        }

        return array($result, $title, $description);
    }
}
