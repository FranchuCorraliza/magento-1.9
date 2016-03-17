
<h2>Programmatically Re-Indexing in Magento</h2>

<a href="reindex.php?reindex_type=all">Reindex All</a></br>
<a href="reindex.php?reindex_type=product_attributes">Reindex Product Attributes</a></br>
<a href="reindex.php?reindex_type=product_prices">Reindex Product Prices</a></br>
<a href="reindex.php?reindex_type=catalog_url_rewrites">Reindex Catalog URL Rewrites</a></br>
<a href="reindex.php?reindex_type=product_flat_data">Reindex Product Flat Data</a></br>
<a href="reindex.php?reindex_type=category_flat_data">Reindex Category Flat Data</a></br>
<a href="reindex.php?reindex_type=category_products">Reindex Category Products</a></br>
<a href="reindex.php?reindex_type=catalog_search_index">Reindex Catalog Search Index</a></br>
<a href="reindex.php?reindex_type=stock_status">Reindex Stock Status</a></br>
<a href="reindex.php?reindex_type=tag_aggregation_data">Reindex Tag Aggregation Data</a></br></br>
<a href="reindex.php">Refresh</a></br></br>
<?php
require_once 'app/Mage.php';
$app = Mage::app('admin');
umask(0);

$reindex_which = $_GET["reindex_type"];

$index_ids = array(1,2,3,4,5,6,7,8,9);

switch ($reindex_which) {
    case "all":
        foreach($index_ids as $id)
  {
   try
   {
    $process = Mage::getModel('index/process')->load($id);
    //MODE_REAL_TIME or MODE_MANUAL
    //$process->setMode(Mage_Index_Model_Process::MODE_MANUAL)->save(); 
    $process->reindexAll();
    echo "Indexing Process is done for all<br />";
   }
   catch(Exception $e)
   {
   echo $e->getMessage();
   }
  }
        break;
    case "product_attributes":
         try
   {
    $process = Mage::getModel('index/process')->load(1);
    //MODE_REAL_TIME or MODE_MANUAL
    //$process->setMode(Mage_Index_Model_Process::MODE_MANUAL)->save(); 
    $process->reindexAll();
    echo "Indexing Process is done for Product Attributes<br />";
   }
   catch(Exception $e)
   {
   echo $e->getMessage();
   }
        break;
    case "product_prices":
         try
   {
    $process = Mage::getModel('index/process')->load(2);
    //MODE_REAL_TIME or MODE_MANUAL
    //$process->setMode(Mage_Index_Model_Process::MODE_MANUAL)->save(); 
    $process->reindexAll();
    echo "Indexing Process is done for Product Prices<br />";
   }
   catch(Exception $e)
   {
   echo $e->getMessage();
   }
        break;
    case "catalog_url_rewrites":
         try
   {
    $process = Mage::getModel('index/process')->load(3);
    //MODE_REAL_TIME or MODE_MANUAL
    //$process->setMode(Mage_Index_Model_Process::MODE_MANUAL)->save(); 
    $process->reindexAll();
    echo "Indexing Process is done for Catalog URL Rewrites<br />";
   }
   catch(Exception $e)
   {
   echo $e->getMessage();
   }
        break;  
    case "product_flat_data":
         try
   {
    $process = Mage::getModel('index/process')->load(4);
    //MODE_REAL_TIME or MODE_MANUAL
    //$process->setMode(Mage_Index_Model_Process::MODE_MANUAL)->save(); 
    $process->reindexAll();
    echo "Indexing Process is done for Product Flat Data<br />";
   }
   catch(Exception $e)
   {
   echo $e->getMessage();
   }
        break;      
    case "category_flat_data":
         try
   {
    $process = Mage::getModel('index/process')->load(5);
    //MODE_REAL_TIME or MODE_MANUAL
    //$process->setMode(Mage_Index_Model_Process::MODE_MANUAL)->save(); 
    $process->reindexAll();
    echo "Indexing Process is done for Category Flat Data<br />";
   }
   catch(Exception $e)
   {
   echo $e->getMessage();
   }
        break;    
    case "category_products":
         try
   {
    $process = Mage::getModel('index/process')->load(6);
    //MODE_REAL_TIME or MODE_MANUAL
    //$process->setMode(Mage_Index_Model_Process::MODE_MANUAL)->save(); 
    $process->reindexAll();
    echo "Indexing Process is done for Category Products<br />";
   }
   catch(Exception $e)
   {
   echo $e->getMessage();
   }
        break;
    case "catalog_search_index":
         try
   {
    $process = Mage::getModel('index/process')->load(7);
    //MODE_REAL_TIME or MODE_MANUAL
    //$process->setMode(Mage_Index_Model_Process::MODE_MANUAL)->save(); 
    $process->reindexAll();
    echo "Indexing Process is done for Catalog Search Index<br />";
   }
   catch(Exception $e)
   {
   echo $e->getMessage();
   }
        break;        
    case "stock_status":
         try
   {
    $process = Mage::getModel('index/process')->load(8);
    //MODE_REAL_TIME or MODE_MANUAL
    //$process->setMode(Mage_Index_Model_Process::MODE_MANUAL)->save(); 
    $process->reindexAll();
    echo "Indexing Process is done for Stock Status<br />";
   }
   catch(Exception $e)
   {
   echo $e->getMessage();
   }
        break;    
    case "tag_aggregation_data":
         try
   {
    $process = Mage::getModel('index/process')->load(9);
    //MODE_REAL_TIME or MODE_MANUAL
    //$process->setMode(Mage_Index_Model_Process::MODE_MANUAL)->save(); 
    $process->reindexAll();
    echo "Indexing Process is done for Tag Aggregation Data<br />";
   }
   catch(Exception $e)
   {
   echo $e->getMessage();
   }
        break; 
}
?>