<?php
require '../app/Mage.php';
Mage::app('default');

$indexCollection = Mage::getModel('index/process')->getCollection();
foreach ($indexCollection as $index) {
    $index->reindexAll();
}

Mage::log("Started Rebuilding Search Index At: " . date("d/m/y h:i:s"));
$sql = "truncate csr_catalogsearch_fulltext;";
$mysqli = Mage::getSingleton('core/resource')->getConnection('core_write');
$mysqli->query($sql);
for ($i = 1; $i <= 9; $i++) {
    $process = Mage::getModel('index/process')->load($i);
   $process->reindexAll();
}
echo Mage::log("Finished Rebuilding Search Index At: " . date("d/m/y h:i:s"));