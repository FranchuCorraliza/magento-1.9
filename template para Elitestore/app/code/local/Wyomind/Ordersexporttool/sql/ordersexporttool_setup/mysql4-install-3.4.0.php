<?php

$installer = $this;

$installer->startSetup();

$installer->run('DROP TABLE IF EXISTS ' . $this->getTable('ordersexporttool_profiles'));


$installer->run('



CREATE TABLE IF NOT EXISTS `' . $this->getTable('ordersexporttool_profiles') . '` (
  `file_id` int(11) NOT NULL auto_increment,
  `file_name` varchar(90) NOT NULL,
  `file_type` tinyint(3) NOT NULL,
  `file_encoding` varchar(40) NOT NULL,
  `file_path` varchar(255) NOT NULL default \'/export/orders/\',
  `file_product_type` varchar(150) NULL,
  `file_store_id` varchar(255) NOT NULL default \'0\',
  `file_flag` int(1) NOT NULL default \'0\',
  `file_single_export` int(1) NOT NULL default \'0\',
  `file_updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `file_last_exported_id` VARCHAR(20) DEFAULT \'0\',
  `file_first_exported_id` int(9) NULL ,
  `file_automatically_update_last_order_id` int(1) NOT NULL default \'1\',
   file_update_status INT(1) NOT NULL DEFAULT \'0\',
   file_update_status_to VARCHAR(255),
   file_update_status_message VARCHAR(500),
  `file_date_format` varchar(50) NOT NULL default \'yyyy-mm-dd\',
  `file_include_header` int(1) NOT NULL default \'0\',
  `file_product_relation` varchar(12) default "all",
  `file_repeat_for_each` int (1) NOT NULL default \'0\',
  `file_repeat_for_each_increment` int (1)  NULL ,
  `file_order_by` int (1) NOT NULL default \'0\',
  `file_order_by_field` int (3) NULL ,
  `file_incremential_column` INT (1) NOT NULL default \'0\',
  `file_incremential_column_name` VARCHAR (50) NULL ,
  `file_header` text,
  `file_extra_header` text,
  `file_body` text,
  `file_footer` text,
  `file_separator` char(3) default NULL,
  `file_protector` char(1) default NULL,
  `file_enclose_data` int(1) NOT NULL default \'1\',
  `file_attributes` text,
  `file_states` text,
  `file_customer_groups` text,
  `file_scheduled_task` varchar(900) NOT NULL DEFAULT \'{"days":["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"],"hours":["00:00","04:00","08:00","12:00","16:00","20:00"]}\',
  `file_ftp_enabled` INT(1) DEFAULT \'0\',
  `file_ftp_host` VARCHAR(300) DEFAULT NULL,
  `file_ftp_login` VARCHAR(300) DEFAULT NULL,
  `file_ftp_password` VARCHAR(300) DEFAULT NULL,
  `file_ftp_active` INT(1) DEFAULT \'0\',
  `file_ftp_dir` VARCHAR(300) DEFAULT NULL,
  `file_use_sftp` VARCHAR(300) DEFAULT NULL,
  PRIMARY KEY  (`file_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;
');


$installer->run('DROP TABLE IF EXISTS ' . $this->getTable('ordersexporttool_attributes'));


$installer->run('

CREATE TABLE IF NOT EXISTS `' . $this->getTable('ordersexporttool_attributes') . '` (
    `attribute_id` int(11) NOT NULL auto_increment,
    `attribute_name` varchar(100) NOT NULL,
    `attribute_order_item` text,
    `attribute_order_address` text,
    `attribute_order_payment` text,
    `attribute_invoice` text,
    `attribute_shipment` text,
    `attribute_creditmemo` text,
   
    `attribute_script` text,
    PRIMARY KEY  (`attribute_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;
');



$installer->getConnection()->exec('INSERT into `' . $this->getTable('ordersexporttool_profiles') . '` 

(`file_id`,`file_name`,`file_type`,`file_encoding`,`file_path`,`file_product_type`,`file_store_id`,`file_flag`,`file_single_export`,`file_updated_at`,`file_last_exported_id`,`file_first_exported_id`,`file_automatically_update_last_order_id`,`file_date_format`,`file_include_header`,`file_product_relation`,`file_repeat_for_each`,`file_repeat_for_each_increment`,`file_order_by`,`file_order_by_field`,`file_incremential_column`,`file_incremential_column_name`,`file_header`,`file_extra_header`,`file_body`,`file_footer`,`file_separator`,`file_protector`,`file_enclose_data`,`file_attributes`,`file_states`,`file_customer_groups`,`file_scheduled_task`,`file_ftp_enabled`,`file_ftp_host`,`file_ftp_login`,`file_ftp_password`,`file_ftp_active`,`file_ftp_dir`,`file_use_sftp`) values (1,\'default\',1,\'\',\'/var/export/\',null,\'1\',1,0,\'2014-02-10 11:22:07\',\'0\',null,0,\'{f}\',0,\'all\',0,null,0,null,0,null,\'<orders>\',null,\'        <order id="{entity_id order}" no="{increment_id}">
		<customer>
		{customer_lastname,[strtoupper]} {customer_firstname,[strtolower],[ucfirst]}
		</customer>
		<billing>
                    {firstname billing} {lastname billing} 
                    {postcode billing} {street billing,[implode]} 
                    {city billing} {country_id billing}
		</billing>
		<shipping>
                    {firstname shipping} {lastname shipping} 
                    {postcode shipping} {street shipping,[implode]}
                    {city shipping} {country_id shipping}
		</shipping>
		<items>
                    {product::start}
                    <item id="{item_id product}">{name product}</item>
                    <weight>{weight product}</weight>
                    {product::end}
		</items>
		<payments>
                    {payment::start}
                    <payment id="{entity_id payment}">
			{method payment}
                    </payment>
                    {payment::end}
		</payments>
		<invoices>
                    {invoice::start}
                    <invoice id="{entity_id invoice}">
				{base_grand_total invoice}{base_currency_code invoice}
                    </invoice>
                    {invoice::end}
		</invoices>
		<shipments>
                    {shipment::start}
                    <shipment id="{entity_id shipment}">
				{shipment_status shipment}
                    </shipment>
                    {shipment::end}
		</shipments>
		<creditmemos>
                    {creditmemo::start}
                    <creditmemo id="{entity_id creditmemo}">
			{amount_canceled payment}
                    </creditmemo>
                    {creditmemo::end}
		</creditmemos>
	</order>\',\'</orders>\',\';\',\'\',1,\'[]\',\'complete,pending,processing\',\'0,1\',\'{"days": ["Wednesday", "Thursday"], "hours": ["03:00", "04:00", "05:00", "06:00"]}\',0,null,null,null,0,null,null)
 ,(2,\'default\',3,\'\',\'/var/export/\',null,\'1\',1,0,\'2014-02-10 11:22:07\',\'0\',null,0,\'{f}\',1,\'all\',0,null,0,null,0,null,\'{"header":["order#", "customer", "Shipping Address", "Product name", "Product sku", "Product price", "Total price"]}\',null,\'{"order":["{increment_id}", "{customer_firstname,[strtolower],[ucfirst]} {customer_lastname,[strtoupper]}", "{firstname shipping} {lastname shipping} {middlename shipping}{city shipping} {postcode shipping}{region shipping} {street shipping,[implode]} ", "{name product}", "{sku product}", "{price product,[float],[2]}", "{base_grand_total,[float],[2]}"]}\',\'\',\';\',\'\',0,\'[]\',\'complete,pending,processing\',\'0,1\',\'{"days":[],"hours":[]}\',0,null,null,null,0,\'/\',null)
 ,(3,\'consolidated\',3,\'\',\'/var/export/\',null,\'1\',1,0,\'2014-02-10 11:22:07\',\'0\',null,0,\'{f}\',0,\'all\',0,null,0,null,0,null,\'{"header":["order #", "total_weight", "total_cost", "customer firstname", "customer lastname"]}\',null,\'{"order":["{increment_id}", "{consolided_weight,[float],[2]}", "{consolided_cost,[float],[2]}", "{customer_firstname}", "{customer_lastname}"]}\',\'\',\';\',\'\',0,\'[]\',\'complete,pending,processing\',\'0,1\',\'{"days":[],"hours":[]}\',0,null,null,null,0,null,null)
 ,(4,\'GoogleTrustedStoresShipment\',2,\'\',\'/var/export/\',null,\'1\',1,0,\'2014-02-10 11:22:07\',\'0\',null,0,\'Y-m-d {f}\',1,\'all\',0,null,0,null,0,null,\'{"header":["merchant order id", "tracking number", "carrier code", "other carrier name", "ship date"]}\',null,\'{"order":["{increment_id}", "", "Others", "{shipping_description}", "{created_at shipment,[date],[Y-m-d]}"]}\',\'\',\'	\',\'\',0,\'[{"line": "0", "checked": true, "code": "shipment.entity_id", "condition": "notnull", "value": ""}, {"line": "1", "checked": false, "code": "order.adjustment_negative", "condition": "eq", "value": ""}, {"line": "2", "checked": false, "code": "order.adjustment_negative", "condition": "eq", "value": ""}, {"line": "3", "checked": false, "code": "order.adjustment_negative", "condition": "eq", "value": ""}, {"line": "4", "checked": false, "code": "order.adjustment_negative", "condition": "eq", "value": ""}, {"line": "5", "checked": false, "code": "order.adjustment_negative", "condition": "eq", "value": ""}, {"line": "6", "checked": false, "code": "order.adjustment_negative", "condition": "eq", "value": ""}, {"line": "7", "checked": false, "code": "order.adjustment_negative", "condition": "eq", "value": ""}, {"line": "8", "checked": false, "code": "order.adjustment_negative", "condition": "eq", "value": ""}, {"line": "9", "checked": false, "code": "order.adjustment_negative", "condition": "eq", "value": ""}, {"line": "10", "checked": false, "code": "order.adjustment_negative", "condition": "eq", "value": ""}]\',\'complete,pending,processing\',\'0,1\',\'{"days": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"], "hours": ["00:00"]}\',0,\'\',\'\',\'\',0,\'\',null)
 ,(5,\'GoogleTrustedStoreCancellation\',2,\'\',\'/var/export/\',null,\'1\',1,0,\'2014-02-10 11:22:07\',\'0\',null,1,\'Y-m-d {f}\',1,\'all\',0,null,0,null,0,null,\'{"header":["merchant order id", "reason"]}\',null,\'{"order":["{increment_id}", "{cancelation_type_for_google}"]}\',\'\',\'	\',\'\',0,\'[]\',\'canceled\',\'0,1\',\'{"days": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"], "hours": ["00:00"]}\',0,null,null,null,0,null,null)
 ,(6,\'YahooStore\',1,\'UTF-8\',\'/var/export/\',\'simple,configurable,grouped_parent,bundle_parent,bundle_children\',\'1,3,2\',1,1,\'2014-02-10 11:24:02\',null,null,1,\'{f}\',0,\'all\',0,null,0,null,0,null,\'<?xml version="1.0" encoding="UTF-8"?>
http://store.yahoo.com/doc/dtd/OrderList2.dtd
<OrderList StoreAccountName="mystorename">\',null,\'<Order currency="{base_currency_code}" id="{increment_id}">
  <Time>{created_at,[date],[D M n H:i:s Y \\G\\M\\T]}</Time>
  <NumericTime><? return strtotime("{created_at}") ?></NumericTime>
  <Referer></Referer>
  <Entry-Point></Entry-Point>
  <AddressInfo type="ship">
   <Name>
    <First>{customer_firstname}</First>
    <Last>{customer_lastname}</Last>
    <Full>{customer_firstname} {customer_lastname}</Full>
   </Name>
   <Company>{company shipping}</Company>
   <Address1>{street shipping,[split],[0]}</Address1>
   <Address2>{street shipping,[split],[1]}</Address2>
   <City>{city shipping}</City>
   <State>{region shipping}</State>
   <Country>{country shipping}</Country>
   <Zip>{postcode shipping}</Zip>
   <Phone>{telephone shipping}</Phone>
   <Email>{email shipping}</Email>
  </AddressInfo>
  <AddressInfo type="bill">
     <Name>
    <First>{customer_firstname}</First>
    <Last>{customer_lastname}</Last>
    <Full>{customer_firstname} {customer_lastname}</Full>
   </Name>
   <Company>{company billing}</Company>
   <Address1>{street billing,[split],[0]}</Address1>
   <Address2>{street billing,[split],[1]}</Address2>
   <City>{city billing}</City>
   <State>{region billing}</State>
   <Country>{country billing}</Country>
   <Zip>{postcode billing}</Zip>
   <Phone>{telephone billing}</Phone>
   <Email>{email billing}</Email>
  </AddressInfo>
  <IPAddress>{remote_ip}</IPAddress>
  <Shipping>{shipping_description}</Shipping>
 
{product::start}
  <Item num="0">
   <Id>{product_id product}</Id>
   <Code>{sku product}</Code>
   <Quantity>{qty_ordered product}</Quantity>
   <Unit-Price>{price product}</Unit-Price>
   <Description>{description product}</Description>
   <Url><? return Mage::getModel(\'\'catalog/product\'\')->load({product_id product})->getProductUrl();?></Url>
   <Taxable>YES</Taxable>
   <Thumb></Thumb>
  </Item>
{product::end}
  <Total>
   <Line type="Subtotal" name="Subtotal">{subtotal}</Line>
   <Line type="Shipping" name="Shipping">{shipping_amount}</Line>
   <Line type="Tax" name="Tax">{tax_amount}</Line>
   <Line type="Total" name="Total">{grand_total}</Line>
  </Total>
 </Order>\',\'</OrderList>\',\';\',\'\',0,\'[]\',\'complete\',\'0,1,2,3,4\',\'{"days":[],"hours":[]}\',0,null,null,null,0,null,null);


');

$installer->getConnection()->exec('insert into `' . $this->getTable('ordersexporttool_attributes') . '`
(`attribute_id`,`attribute_name`,`attribute_order_item`,`attribute_order_address`,`attribute_order_payment`,`attribute_invoice`,`attribute_shipment`,`attribute_creditmemo`,`attribute_script`) values
 (1,\'consolidated_cost\',\'base_cost,qty_ordered\',null,null,null,null,null,\' /* This is a simple function to get the total cost of all products in the current order  */ 
 $cost = 0;
 // $data can be use to get all data (products, payments, invoices, shipments, creditmemos) related to the current order:
 foreach ($data[\'\'products\'\'] as $product) {
 	$cost+= $product->getBaseCost() * $product->getQtyOrdered();
 }
 return $cost;\')
, (2,\'cancelation_type_for_google\',null,null,null,null,null,null,\'switch($item->getStatus()){
  case \'\'canceled\'\': $value= \'\'MerchantCanceled\'\'; break;
  default : $value= \'\'BuyerCanceled\'\';
}
\')
, (3,\'concatened_skus\',null,null,null,null,null,null,\' $skus = array();
 foreach ($data[\'\'products\'\'] as $product) {
 	$skus[]= $product->getSku();
 }
 return implode(\'\',\'\',$skus);\')
 ,(4,\'consolidated_weight\',\'weight\',null,null,null,\'\',null,\' /* This is a simple function to get the total weight of all prodcuts in the current order  */ 
 $weight = 0;
 // $data can be use to get all data (products, payments, invoices, shipments, creditmemos) related to the current order
 foreach ($data[\'\'products\'\'] as $product) {
 	$weight+= $product->getWeight();
 }
 return $weight;
\')
, (5,\'carrier\',null,null,null,null,null,null,\'$shipmentCollection = Mage::getResourceModel(\'\'sales/order_shipment_collection\'\')->setOrderFilter($order)->load();
foreach ($shipmentCollection as $shipment){
    foreach($shipment->getAllTracks() as $tracknum){
        $tracknums=$tracknum->getNumber();
        $ship_col = Mage::getResourceModel(\'\'sales/order_shipment_track_collection\'\')->addAttributeToFilter(\'\'track_number\'\', $tracknums)->load();
            foreach($ship_col as $sc){
                  $trackMethod[] = $sc->getCarrier_name();
            }
    }

}
return implode(\'\',\'\',$trackMethod);
 


\')
, (6,\'nth\',null,null,null,null,null,null,\'// use {nth product} in the pattern
$ids=array();
foreach($data[\'\'products\'\'] as $p){
  $ids[]=$p->getProductId();
}
return array_search($item->getProductId(),$ids);
\')
, (7,\'comments\',null,null,null,null,null,null,\'$collection=Mage::getModel("sales/order_status_history")->getCollection()->addAttributeToFilter("parent_id", $order->getEntityId());
foreach($collection as $comment ){
	$comments[]=$comment->getComment();
}
return implode(\'\',\'\',$comments);\')
, (8,\'gift_message\',null,null,null,null,null,null,\'// per product message : {gift_message product}
return Mage::getModel(\'\'giftmessage/message\'\')->load($item->getGiftMessageId())->getMessage();
// per order message : {gift_message product}
//return Mage::getModel(\'\'giftmessage/message\'\')->load($order->getGiftMessageId())->getMessage();\')
,(9,\'tracking\',null,null,null,null,null,null,\'$shipmentCollection = Mage::getResourceModel(\'\'sales/order_shipment_collection\'\')->setOrderFilter($order)->load();
        foreach ($shipmentCollection as $shipment){
            foreach($shipment->getAllTracks() as $tracknum)
            {
                $tracknums[]=$tracknum->getNumber();
            }
        }
return implode(\'\',\'\',$tracknums);
\')
,(10,\'track_number\',null,null,null,null,null,null,\'// use {track_number shipment} in the patter,
return Mage::getModel(\'\'sales/flat_shipment_track\'\')->load($item->getEntityId())->getNumber();\')


');

// ajout du champ assignation dans les commandes
if (version_compare(Mage::getVersion(), '1.4.0', '<')) {
    $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'), 'export_flag', 'varchar(100) NOT NULL DEFAULT 0;'
    );
    $attribute = new Mage_Eav_Model_Entity_Setup('core_setup');
    $attribute->addAttribute('order', 'export_flag', array('type' => 'static', 'visible' => true));
} else {
    $installer->getConnection()->addColumn(
            $installer->getTable('sales_flat_order'), 'export_flag', 'varchar(100) NOT NULL DEFAULT 0;'
    );
    $installer->getConnection()->addColumn(
            $installer->getTable('sales/order_grid'), 'export_flag', 'varchar(100) NOT NULL DEFAULT 0;'
    );
}




if (!strpos($_SERVER['HTTP_HOST'], "wyomind.com")) {
    //Mage::app()->getCache()->clean();
    //Mage::app()->cleanAllSessions();
}
$installer->endSetup();

