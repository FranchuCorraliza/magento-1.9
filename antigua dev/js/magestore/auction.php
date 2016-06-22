<?php

// Global data
$data = array();

// Database connect
function getConnection() {
    global $data;
    if (isset($data['_connection']))
        return $data['_connection'];
    $conConfig = simplexml_load_file('../../app/etc/local.xml')->global->resources;
    $tablePrefix = $conConfig->db->table_prefix;
    $connection = $conConfig->default_setup->connection;

    $data['_table_prefix'] = $tablePrefix;
    try {
        $data['_connection'] = mysql_connect($connection->host, $connection->username, $connection->password);
        if ($data['_connection']) {
            // fix loi lay database ra da la uft8
            mysql_set_charset('utf8', $data['_connection']);
            mysql_select_db($connection->dbname, $data['_connection']);
        }
    } catch (Exception $e) {
        
    }

    return $data['_connection'];
}

function closeConnection() {
    global $data;
    if (isset($data['_connection'])) {
        try {
            mysql_close($data['_connection']);
        } catch (Exception $e) {
            
        }
    }
}

function getTable($tableName) {
    global $data;
    if (!isset($data['_connection']))
        getConnection();
    return $data['_table_prefix'] . $tableName;
}

function getLastBidInfo($auctionId) {
    $link = getConnection();
    $tableName = getTable('auction_bid');
    $sql = "SELECT * FROM `$tableName` WHERE `productauction_id`=$auctionId AND `status`<>'2' ORDER BY `auctionbid_id` DESC";
    $result = mysql_query($sql, $link);
    if ($result)
        return mysql_fetch_assoc($result);
    else
        return false;
}

// Request control
function getRequestParam($paramName) {
    return $_REQUEST[trim($paramName)];
}

// Process Request
function updateListAuction() {
    global $data;
    $auctionIds = explode(',', getRequestParam('ids'));
    $currentBids = explode(',', getRequestParam('current_bid_ids'));
    $auctions = array_combine($auctionIds, $currentBids);
    $result = array();
    foreach ($auctions as $auctionId => $currentBidId) {
        try {
            $lastBidInfo = getLastBidInfo($auctionId);
            if (!$lastBidInfo) {
                if ($currentBidId)
                    $result[$auctionId] = 2;
                continue;
            }
            if (isset($lastBidInfo['auctionbid_id']))
                if ($currentBidId != $lastBidInfo['auctionbid_id'])
                    $result[$auctionId] = $lastBidInfo['auctionlistinfo'];
            $lastBidInfo = array();
        } catch (Exception $e) {
            
        }
    }
    if (count($result)) {
        include_once('../../lib/Zend/Json.php');
        if (!function_exists('json_encode')) {
            include_once('../../lib/Zend/Json/Encoder.php');
        }
        echo Zend_Json::encode($result);
    }
}

function controllerAction() {
    clearCache();
    global $data;
    $template = getRequestParam('tmpl');
    if ($template == 'auctionlistinfo')
        return updateListAuction();
    $auctionId = getRequestParam('id');
    try {
        $lastBidInfo = getLastBidInfo($auctionId);
        if (!$lastBidInfo) {
            if (getRequestParam('current_bid_id'))
                echo '<div id="result_auction_reset"></div>';
            return;
        }
        if (isset($lastBidInfo['auctionbid_id'])) {
            $currentBid = getRequestParam('current_bid_id');
            if ($currentBid != $lastBidInfo['auctionbid_id']) {
                echo $lastBidInfo['auctioninfo'];
            }
        }
    } catch (Exception $e) {
        return;
    }
}

function clearCache() {
    header("Cache-Control: no-cache, must-revalidate");
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Content-Type: application/xml; charset=utf-8");
}

// Main execute
if (getConnection()) {
    controllerAction();
    closeConnection();
}