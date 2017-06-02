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



require_once 'abstract.php';

class Mirasvit_Shell_Fpc extends Mage_Shell_Abstract
{
    public function run()
    {
        if ($this->getArg('crawl')) {
            $crawler = Mage::getModel('fpccrawler/crawler_crawl');
            $crawler->run(true);
        } elseif ($this->getArg('crawl_logged')) {
            $crawler = Mage::getModel('fpccrawler/crawlerlogged_crawl');
            $crawler->run(true);
        } elseif ($this->getArg('status')) {
            echo Mage::helper('fpccrawler')->getVariable('status').PHP_EOL;
        } elseif ($this->getArg('status_logged')) {
            echo Mage::helper('fpccrawler')->getVariable('status_logged').PHP_EOL;
        } elseif ($this->getArg('update_log')) {
            $log = Mage::getSingleton('fpc/log');
            $log->importFileLog();
            $log->getResource()->aggregate();
        } elseif ($this->getArg('update_log_logged')) {
            $log = Mage::getSingleton('fpc/log');
            $log->importFileLog(true);
            $log->getResource()->aggregate();
        } elseif ($this->getArg('clear_by_limits')) {
            Mage::getSingleton('fpc/cache')->cleanByLimits();
        } else {
            echo $this->usageHelp();
        }
    }

    public function _validate()
    {
    }

    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f fpc.php -- [options]

  crawl                 Run crawler for all allowed stores
  crawl_logged          Run crawler for all allowed stores (logged in users)
  status                Show crawler status
  status_logged         Show crawler for logged in users status
  update_log            Update log data (import urls to crawler, update chart data)
  update_log_logged     Update log data (import urls to crawler for logged in users)
  clear_by_limits       Clear all cache if limits were reached
  help                  This help

USAGE;
    }
}

$shell = new Mirasvit_Shell_Fpc();

$shell->run();
