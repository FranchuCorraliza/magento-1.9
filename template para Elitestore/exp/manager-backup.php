<?php

error_reporting(E_ALL);
ini_set('max_execution_time', 300);

//Url de la conexión
$client = new SoapClient("http://www.elitestore.es/api/soap?wsdl");
//Login
$session = $client->login("facturas", "123456");

$host = 'localhost';
$user = 'startElite';
$pass = '4TMgTY3CkAyv8';
$db = 'elitestore2016';
$table = 'eav_entity_store';

//MySQL
//$link = mysql_connect($host, $user, $pass) or die("Can not connect." . mysql_error());
//mysql_select_db($db) or die("Can not connect.");
//MySQLI
$link = new mysqli($host, $user, $pass, $db);

function sz_cElement($root, $parent, $name, $value) {
    $parent->appendChild($root->createElement($name))->appendChild($root->createTextNode($value));
}


function Encrypt($sValue)
{
	$Pass = "97545461862354841954846789784849";
	$iv = "45286432578154275649518724561354";

    return rtrim(
        base64_encode(
            mcrypt_encrypt(
                MCRYPT_RIJNDAEL_256,
                $Pass, $sValue, 
                MCRYPT_MODE_ECB, 
                $iv
                )
            ), "\0"
        );
}

function Decrypt($sValue)
{
	$Pass = "97545461862354841954846789784849";
	$iv = "45286432578154275649518724561354";
	
    return rtrim(
        mcrypt_decrypt(
            MCRYPT_RIJNDAEL_256, 
            $Pass, 
            base64_decode($sValue), 
            MCRYPT_MODE_ECB,
            $iv
        ), "\0"
    );
}




$dom = new DomDocument('1.0', 'UTF-8'); 
$orders = $dom->appendChild($dom->createElement('orders'));
$killer = true;

$last_fact_id = 5150;
if( isset( $_GET["last_fact_id"] ) ){
	$last_fact_id = $_GET["last_fact_id"];
}
 
$contador = 0;

$salir_bucle = false;


//MySQL
//$result = mysql_query("SELECT increment_id FROM sales_flat_invoice WHERE entity_id  >= ". $last_fact_id  ." ");
//MySQLI
$result = $link->query("SELECT increment_id FROM sales_flat_invoice WHERE entity_id  >= ". $last_fact_id  ." ");

//MySQL
 //while( $fila = mysql_fetch_array($result, MYSQL_NUM) ) {
//MySQLI
 while( $fila = $result->fetch_array() ) {
	$contador = $contador + 1;
	if( $contador >= 9999 ) {
		// $killer=true;
		$salir_bucle = true;
		break;
	} else{   
		try {
			$currentinvoice = $fila[0];
   
			$invoice = $client->call($session, 'sales_order_invoice.info', $currentinvoice);
			if( $invoice == null  ){
				// ERROR AL RECUPERAR FACTURA
				echo "ERROR AL RECUPERAR SIGUIENTE FACTURA: ".  $currentinvoice ."<br/>\n";
				continue;
			}
			if( ! isset( $invoice[order_increment_id] ) ){
				echo "ERROR NO EXISTE EL PEDIDO DE LA FACTURA: ".  $currentinvoice ." [order_increment_id]<br/>\n";
				continue;
			}
			$order_mag = $client->call($session, 'sales_order.info', $invoice[order_increment_id]);
			
			if( $order_mag == null  ){
				// ERROR AL RECUPERAR EL ORDER MANAGER
				echo "ERROR AL RECUPERAR EL PEDIDO ".  $currentinvoice ."<br/>\n";
				continue;
			}
			
			$order = $orders->appendChild($dom->createElement('order'));
			sz_cElement($dom, $order, 'id', $invoice[invoice_id]);
			sz_cElement($dom, $order, 'id_factura', $invoice[increment_id]);
			sz_cElement($dom, $order, 'id_pedido', $invoice[order_increment_id]);
			sz_cElement($dom, $order, 'fecha_factura', $invoice[created_at]);
			sz_cElement($dom, $order, 'estado', $order_mag[status]);
			sz_cElement($dom, $order, 'facturacion_nombre', $order_mag[billing_address][firstname]);
			if ($order_mag[billing_address][company]){
				sz_cElement($dom, $order, 'facturacion_apellidos', $order_mag[billing_address][lastname]." - ".$order_mag[billing_address][company]);
			}else{
				sz_cElement($dom, $order, 'facturacion_apellidos', $order_mag[billing_address][lastname]);
			}
			sz_cElement($dom, $order, 'facturacion_dni', $order_mag[customer_taxvat]);
			sz_cElement($dom, $order, 'facturacion_mail', $order_mag[billing_address][email]);
			sz_cElement($dom, $order, 'facturacion_ciudad', $order_mag[billing_address][city]);
			sz_cElement($dom, $order, 'facturacion_direccion', $order_mag[billing_address][street]);
			sz_cElement($dom, $order, 'facturacion_direccion_2', $order_mag[billing_address][street2]);
			sz_cElement($dom, $order, 'facturacion_provincia', $order_mag[billing_address][region]);
			sz_cElement($dom, $order, 'facturacion_estado', $order_mag[billing_address][country_id]);
			sz_cElement($dom, $order, 'factura_codigo_postal', $order_mag[billing_address][postcode]);
			sz_cElement($dom, $order, 'factura_telefono', $order_mag[billing_address][telephone]);
			sz_cElement($dom, $order, 'envio_nombre', $order_mag[shipping_address][firstname]);
			sz_cElement($dom, $order, 'envio_apellidos', $order_mag[shipping_address][lastname]);
			sz_cElement($dom, $order, 'envio_ciudad', $order_mag[shipping_address][city]);
			sz_cElement($dom, $order, 'envio_direccion', $order_mag[shipping_address][street]);
			sz_cElement($dom, $order, 'envio_direccion_2', $order_mag[shipping_address][street2]);
			sz_cElement($dom, $order, 'envio_provincia', $order_mag[shipping_address][region]);
			sz_cElement($dom, $order, 'envio_estado',  $order_mag[shipping_address][country_id]);
			sz_cElement($dom, $order, 'envio_codigo_postal', $order_mag[shipping_address][postcode]);
			sz_cElement($dom, $order, 'envio_telefono', $order_mag[shipping_address][telephone]);

			$clienten = $order_mag[shipping_address][firstname];

			$orderitems = $order->appendChild($dom->createElement('order_items'));
			$i = 0;		
			foreach ($invoice[items] as $item) {
				if ($item) {
				
					$orderitem = $orderitems->appendChild($dom->createElement('order_item'));  
					
					//Tallas colores
					$theProduct = array();
					$theProduct['info'] = $client->call($session, 'catalog_product.info', $invoice[items][$i][product_id]);
					sz_cElement($dom, $orderitem, 'referencia', $theProduct['info'][sku]);
					sz_cElement($dom, $orderitem, 'type_id', $theProduct['info'][type_id]);

					$tallaid = $theProduct['info'][talla];
					$valoresAtributoTalla = $client->call($session, 'product_attribute.info', 134);	
					$j = 0;
					$contadorTalla = 0;
					foreach ($valoresAtributoTalla[options] as $valorAtributoTalla) {
						if ( $valoresAtributoTalla[options][$j][value] == $tallaid ) {
							sz_cElement($dom, $orderitem, 'talla', $valoresAtributoTalla[options][$j][label]);
							$contadorTalla++;
							break;
						}
						$j++;
					}
					if ( $contadorTalla == 0 ) {
						sz_cElement($dom, $orderitem, 'talla', '');
					}
					
					$colorid = $theProduct['info'][color];
					$valoresAtributoColor = $client->call($session, 'product_attribute.info', 92);	
					//echo '<pre>'; print_r($valoresAtributo[options] ); echo '</pre>';				
					$j = 0;
					$contadorColor = 0;
					foreach ($valoresAtributoColor[options] as $valorAtributoColor) {
						if ( $valoresAtributoColor[options][$j][value] == $colorid ) {
							sz_cElement($dom, $orderitem, 'color', $valoresAtributoColor[options][$j][label]);
							$contadorColor++;
							break;
						}
						$j++;
					}
					if ( $contadorColor == 0 ) {
						sz_cElement($dom, $orderitem, 'color', '');
					}
					
					sz_cElement($dom, $orderitem, 'order_item_Sku', $invoice[items][$i][sku]);
					sz_cElement($dom, $orderitem, 'order_producto_nombre', $invoice[items][$i][name]);
					sz_cElement($dom, $orderitem, 'order_precio_producto', $invoice[items][$i][base_price]);
					sz_cElement($dom, $orderitem, 'order_descuento', $invoice[items][$i][base_discount_amount]);
					sz_cElement($dom, $orderitem, 'order_ivadelproducto', $invoice[items][$i][base_tax_amount]);
					sz_cElement($dom, $orderitem, 'order_precio_producto_con_iva', $invoice[items][$i][base_price_incl_tax]);
					sz_cElement($dom, $orderitem, 'order_item_cantidad', $invoice[items][$i][qty]);
					sz_cElement($dom, $orderitem, 'order_item_cantidad_devuelta', $order_mag[items][$i][qty_refunded]);			

					$i++;
				}
			}
			echo "Factura procesada <br/>\n";
			echo "::::<br />\n";
			
			sz_cElement($dom, $order, 'order_metodo_pago', $order_mag[payment][method]); 
			sz_cElement($dom, $order, 'order_metodo_pago_ampliado', $order_mag[payment][additional_information][paypal_payer_status]); 
			sz_cElement($dom, $order, 'order_subtotal', $invoice[base_subtotal]);   
			sz_cElement($dom, $order, 'order_coste_envio', $invoice[base_shipping_amount]);
			sz_cElement($dom, $order, 'order_descuento', $invoice[base_discount_amount]);
			sz_cElement($dom, $order, 'order_impuestos', $invoice[base_tax_amount]);
			sz_cElement($dom, $order, 'total_devolucion', $order_mag[base_total_refunded]);
			sz_cElement($dom, $order, 'order_total', $invoice[base_grand_total]);    
			
		}catch ( Exception $e) {
			echo "ERROR AL PROCESAR FACTURA ".  $currentinvoice ."; ". $e->getMessage() ."<br/>\n";
		}
	
		echo $currentinvoice. "<br />\n";
	}
}

$client->endSession($session);


try {
	echo "<br> Escribiendo Fichero: XML.<br/>\n";

	$dom->formatOutput = true;                  
	$fichero = $dom->saveXML(); 
	$filename="export_facturas.xml";

	echo "<br>El archivo se llama: ".$filename;
	
	//echo "<br><br>Original: ".$fichero;
	
	$ficherocifrado = Encrypt($fichero);
		
	//echo "<br><br>Cifrado: ".$ficherocifrado;
		
	$nfile = fopen($filename, 'w');
	fwrite($nfile, $ficherocifrado);
	fclose($nfile);
	
	//echo "<br><br>Descifrado: ".Decrypt($ficherocifrado);
	
	echo "<br>Todo Ok";
	
}catch ( Exception $e) {
	echo "<br>ERROR AL CIFRAR: ". $e->getMessage() ."<br/>\n";
}


echo "<br>Factura actual: ".$currentinvoice;

$fecha = time ();
echo "<br>".date ( "h:i:s" , $fecha ); 

?>
