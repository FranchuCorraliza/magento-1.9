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



class Mirasvit_Fpc_Block_Adminhtml_Log extends Mage_Adminhtml_Block_Template
{
    public function getChartData()
    {
        $adapter = Mage::getSingleton('core/resource');
        $conn = $adapter->getConnection('core_read');

        if (($tableName = $adapter->getTableName('fpc/log_aggregated')) //check if table m_fpc_log_aggregated exist
            && Mage::getSingleton('core/resource')->getConnection('core_write')->showTableStatus($tableName) !== false) {
                $data = $conn->fetchAll('SELECT * FROM '.$adapter->getTableName('fpc/log_aggregated').' ORDER BY period ASC');
        } else {
            $data = $conn->fetchAll('SELECT * FROM '.$adapter->getTableName('fpc/log_aggregated_daily').' ORDER BY period ASC');
        }

        $result = array();

        foreach ($data as $item) {
            if (!isset($result[$item['period']])) {
                $result[$item['period']] = array(
                    'period' => $item['period'],
                    'response_without' => null,
                    'response_with' => null,
                    'hits' => null,
                    'miss' => null,
                );
            }

            if ($item['from_cache'] == 0) {
                $result[$item['period']]['response_without'] = $item['response_time'];
                $result[$item['period']]['miss'] = $item['hits'];
            } else {
                $result[$item['period']]['response_with'] = $item['response_time'];
                $result[$item['period']]['hits'] = $item['hits'];
            }
        }

        return $result;
    }

    public function getCacheInfo()
    {
        $info = array(
            'count' => 0,
            'size' => 0,
        );

        $html = array();

        $cache = Mage::getSingleton('fpc/cache')->getCacheInstance();
        $frontend = $cache->getFrontend();
        $backend = $frontend->getBackend();

        if ($backend instanceof Zend_Cache_Backend_File) {
            $html[] = __('Cache Type: <b>%s</b>', 'File');
        } elseif ($backend instanceof Zend_Cache_Backend_TwoLevels) {
            $html[] = __('Cache Configuration: <b>%s</b>', 'Two-level cache');

            $matches = array();
            preg_match('/\[slow_backend\] => (.*)/', print_r($backend, true), $matches);
            if (count($matches) == 2) {
                $html[] = __('"Slow" Backend Type: <b>%s</b>', $matches[1]);
            }

            $matches = array();
            preg_match('/\[fast_backend\] => (.*)/', print_r($backend, true), $matches);
            if (count($matches) == 2) {
                $html[] = __('"Fast" Backend Type: <b>%s</b>', $matches[1]);
            }
        } else {
            $html[] = __('Cache Type: <b>%s</b>', get_class($backend));
        }

        if ($backend instanceof Zend_Cache_Backend_File) {
            $html[] = __('Cache size: <b>%s</b> Mb', round(Mage::helper('fpc')->getCacheSize() / 1024 / 1024, 1));
            $html[] = __('Number of cache files: <b>%s</b>', Mage::helper('fpc')->getCacheNumber());
        }

        return implode('<br>', $html);
    }

    public function getCronStatus()
    {
        return Mage::helper('fpc')->showCronStatusError(true);
    }

    public function getExtensionDisabledInfo()
    {
        return Mage::helper('fpc')->showExtensionDisabledInfo(true);
    }

    public function getFreeHddSpace()
    {
        return Mage::helper('fpc')->showFreeHddSpace(true, false);
    }
}
