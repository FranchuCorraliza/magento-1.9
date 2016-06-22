<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Geoip
 */
class Amasty_Geoip_Model_Import extends Mage_Core_Model_Abstract
{
    protected static $_sessionKey = 'am_geoip_import_process_%key%';

    protected $_rowsPerTransaction = 10000;

    protected $_geoipRequiredFiles = array(
        'block' => 'GeoLiteCity-Blocks.csv',
        'location' => 'GeoLiteCity-Location.csv'
    );

    protected $_modelsCols = array(
        'block'    => array(
            'start_ip_num', 'end_ip_num', 'geoip_loc_id'
        ),
        'location' => array(
            'geoip_loc_id', 'country', 'region', 'city', 'postal_code',
            'latitude', 'longitude', 'dma_code', 'area_code'
        )
    );

    public function getRequiredFiles()
    {
        return $this->_geoipRequiredFiles;
    }

    public function filesAvailable()
    {
        $ret = TRUE;

        $varDir = Mage::getBaseDir('var');
        $dir = $varDir . DS . 'amasty' . DS . 'geoip';

        foreach ($this->_geoipRequiredFiles as $file) {
            if (!file_exists($dir . DS . $file)) {
                $ret = FALSE;
                break;
            }
        }

        return $ret;
    }

    public function isFileExist($filePath)
    {
        if (file_exists($filePath)) {
            return true;
        }
        return false;
    }

    public function getFilePath($type, $action)
    {
        $dir = $this->getDirPath($action);
        $file = $dir . DS . $this->_geoipRequiredFiles[$type];
        return $file;
    }

    public function getDirPath($action)
    {
        $varDir = Mage::getBaseDir('var');
        if ($action == 'download_and_import') {
            $dir = $varDir . DS . 'amasty' . DS . 'geoip' . DS . 'amasty_files';
        } else {
            $dir = $varDir . DS . 'amasty' . DS . 'geoip';
        }
        return $dir;
    }

    function startProcess($table, $filePath, $ignoredLines = 0)
    {
        $ret = array();

        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $query = 'SELECT table_name FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME LIKE \'%amasty_geoip_'. $table .'_%\'';
        $columns = $write->fetchCol($query);
        $oldTemporary = implode(', ', $columns);
        if (!empty($oldTemporary)) {
            $delete = "DROP TABLE IF EXISTS $oldTemporary";
            $write->query($delete);
        }


        $importProcess = array(
            'position'    => 0,
            'tmp_table'   => NULL,
            'rows_count'  => $this->_getRowsCount($filePath) - $ignoredLines,
            'current_row' => 0
        );

        if (($handle = fopen($filePath, "r")) !== FALSE) {
            $tmpTableName = $this->_prepareImport($table);

            while ($ignoredLines > 0 && ($data = fgetcsv($handle, 0, ",")) !== FALSE) {
                $ignoredLines--;
            }


            $importProcess['position'] = ftell($handle);
            $importProcess['tmp_table'] = $tmpTableName;
            $ret = $importProcess;
        }

        Mage::getSingleton('core/session')->setData(self::getSessionKey($table), $importProcess);

        return $ret;
    }

    function doProcess($table, $filePath)
    {
        $ret = array();
        if (($handle = fopen($filePath, "r")) !== FALSE) {
            $importProcess = Mage::getSingleton('core/session')->getData(self::getSessionKey($table));

            $write = Mage::getSingleton('core/resource')->getConnection('core_write');

            if ($importProcess) {
                $tmpTableName = $importProcess['tmp_table'];

                try {
                    $position = $importProcess['position'];

                    fseek($handle, $position);

                    $transactionIterator = 0;

                    $write->beginTransaction();

                    while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {

                        $this->_importItem($table, $tmpTableName, $data);

                        $transactionIterator++;

                        if ($transactionIterator >= $this->_rowsPerTransaction) {
                            break;
                        }
                    }

                    $write->commit();

                    $importProcess['current_row'] += $transactionIterator;

                    $importProcess['position'] = ftell($handle);

                    Mage::getSingleton('core/session')->setData(self::getSessionKey($table), $importProcess);

                    $ret = $importProcess;

                } catch (Exception $e) {
                    $write->rollback();

                    $this->_destroyImport($table, $tmpTableName);

                    throw new Exception($e->getMessage());
                }
            } else
                throw new Exception('run start before');
        }

        return $ret;
    }

    function commitProcess($table)
    {
        $ret = FALSE;
        $importProcess = Mage::getSingleton('core/session')->getData(self::getSessionKey($table));
        if ($importProcess) {
            $tmpTableName = $importProcess['tmp_table'];

            try {

                Mage::app()->getConfig()
                    ->saveConfig('amgeoip/import/' . $table, 1)
                    ->reinit()
                ;//clean cache

                $this->_doneImport($table, $tmpTableName);

            } catch (Exception $e) {
                $this->_destroyImport($table, $tmpTableName);

                throw new Exception($e->getMessage());
            }

            $this->_destroyImport($table, $tmpTableName);

            $ret = TRUE;
        } else
            throw new Exception('run start before');

        return $ret;
    }

    function isDone()
    {
        return Mage::getStoreConfig('amgeoip/import/block') == 1;

    }

    public function isImported()
    {
        $data = $this->getCollection();
    }

    static function getSessionKey($table)
    {
        return strtr(self::$_sessionKey, array(
            '%key%' => $table
        ));
    }

    protected function _getRowsCount($filePath)
    {
        $linecount = 0;
        $handle = fopen($filePath, "r");
        while(!feof($handle)){
            $line = fgets($handle);
            $linecount++;
        }
        return $linecount;

        $a = sizeof(file($filePath));
        return sizeof(file($filePath));
    }

    protected function _importItem($table, $tmpTableName, &$data)
    {
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');

        $query = 'insert into `' . $tmpTableName . '`' .
            '(`' . implode('`, `', $this->_modelsCols[$table]) . '`) VALUES ' .
            '(?)';

        $query = $write->quoteInto($query, $data);

        $write->query($query);
    }

    protected function _prepareImport($table)
    {
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');

        $targetTable = Mage::getSingleton('core/resource')
            ->getTableName('amgeoip/' . $table)
        ;

        $tmpTableName = uniqid($targetTable . '_');

        $query = 'create table ' . $tmpTableName . ' like ' . $targetTable;
        $write->query($query);

        $query = 'alter table ' . $tmpTableName . ' engine innodb';
        $write->query($query);

        return $tmpTableName;
    }

    protected function _doneImport($table, $tmpTableName)
    {
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');

        $targetTable = Mage::getSingleton('core/resource')
            ->getTableName('amgeoip/' . $table)
        ;

        $query = 'delete from ' . $targetTable;
        $write->query($query);

        $query = 'insert into ' . $targetTable . ' select * from ' . $tmpTableName;
        $write->query($query);

    }

    protected function _destroyImport($table, $tmpTableName)
    {
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');

        $query = 'drop table ' . $tmpTableName;
        $write->query($query);

        Mage::getSingleton('core/session')->setData(self::getSessionKey($table), NULL);
    }

}
