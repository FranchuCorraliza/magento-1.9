<?php

error_reporting(E_ALL);
ini_set('max_execution_time', 300);

//Cargamos Mage
require_once "../app/Mage.php";
Mage::app();
umask(0);


//Url de la conexión
//$client = new SoapClient("http://www.elitestore.es/api/soap?wsdl");
//Login
//$session = $client->login("facturas", "123456");

$host = 'localhost';
$user = 'magento';
$pass = 'elite2015';
$db = 'desarrollo';
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
$result = $link->query("SELECT entity_id FROM sales_flat_invoice WHERE entity_id  >= ". $last_fact_id  ." ");

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
			$invoice = Mage::getModel("sales/order_invoice")->load($currentinvoice);
			if(!$invoice->getId()){
				// ERROR AL RECUPERAR FACTURA
				echo "ERROR AL RECUPERAR SIGUIENTE FACTURA: ".  $currentinvoice ."<br/>\n";
				continue;
			}
			$pedido=$invoice->getOrder();
			if(!$pedido->getId()){
				echo "ERROR NO EXISTE EL PEDIDO DE LA FACTURA: ".  $currentinvoice ." [order_increment_id]<br/>\n";
				continue;
			}
			$invoiceId=$invoice->getId();
			$invoiceIncrementId=$invoice->getIncrementId();
			$orderIncrementId=$pedido->getIncrementId();
			$invoiceCreateAt=$invoice->getCreatedAt();
			$orderStatus=$pedido->getStatus();

			$billingAddress=$pedido->getBillingAddress();

			$facturacionNombre=$billingAddress->getFirstname();
			$facturacionApellidos=$billingAddress->getLastname();
			if ($billingAddress->getCompany()){
				$facturacionApellidos.=" - ".$billingAddress->getCompany();
			}
			$facturacionDni=$billingAddress->getVatId();
			$facturacionEmail=$billingAddress->getEmail();
			$facturacionCiudad=$billingAddress->getCity();
			$facturacionDireccion=$billingAddress->getStreet()[0];
			$facturacionDireccion2=$billingAddress->getStreet()[1];
			$facturacionProvincia=$billingAddress->getRegion();
			$facturacionEstado=$billingAddress->getCountry();
			$facturacionCodigoPostal=$billingAddress->getPostcode();
			$facturacionTelefono=$billingAddress->getTelephone();

			$shippingAddress=$pedido->getShippingAddress();

			$envioNombre=$shippingAddress->getFirstname();
			$envioApellidos=$shippingAddress->getLastname();
			$envioCiudad=$shippingAddress->getCity();
			$envioCalle=$shippingAddress->getStreet()[0];
			$envioCalle2=$shippingAddress->getStreet()[1];
			$envioProvincia=$shippingAddress->getRegion();
			$envioEstado=$shippingAddress->getCountry();
			$envioCodigoPostal=$shippingAddress->getPostcode();
			$envioTelefono=$shippingAddress->getTelephone();
			
			$order = $orders->appendChild($dom->createElement('order'));
			
			sz_cElement($dom, $order, 'id', $invoiceId);
			sz_cElement($dom, $order, 'id_factura', $invoiceIncrementId);
			sz_cElement($dom, $order, 'id_pedido', $orderIncrementId);
			sz_cElement($dom, $order, 'fecha_factura', $invoiceCreateAt);
			sz_cElement($dom, $order, 'estado', $orderStatus);
			sz_cElement($dom, $order, 'facturacion_nombre', $facturacionNombre);
			sz_cElement($dom, $order, 'facturacion_apellidos', $facturacionApellidos);
			
			sz_cElement($dom, $order, 'facturacion_dni', $facturacionDni);
			sz_cElement($dom, $order, 'facturacion_mail', $facturacionEmail);
			sz_cElement($dom, $order, 'facturacion_ciudad', $facturacionCiudad);
			sz_cElement($dom, $order, 'facturacion_direccion', $facturacionDireccion);
			sz_cElement($dom, $order, 'facturacion_direccion_2', $facturacionDireccion2);
			sz_cElement($dom, $order, 'facturacion_provincia', $facturacionProvincia);
			sz_cElement($dom, $order, 'facturacion_estado', $facturacionEstado);
			sz_cElement($dom, $order, 'factura_codigo_postal', $facturacionCodigoPostal);
			sz_cElement($dom, $order, 'factura_telefono', $facturacionTelefono);
			sz_cElement($dom, $order, 'envio_nombre', $envioNombre);
			sz_cElement($dom, $order, 'envio_apellidos', $envioApellidos);
			sz_cElement($dom, $order, 'envio_ciudad', $envioCiudad);
			sz_cElement($dom, $order, 'envio_direccion', $envioCalle);
			sz_cElement($dom, $order, 'envio_direccion_2', $envioCalle2);
			sz_cElement($dom, $order, 'envio_provincia', $envioProvincia);
			sz_cElement($dom, $order, 'envio_estado',  $envioEstado);
			sz_cElement($dom, $order, 'envio_codigo_postal', $envioCodigoPostal);
			sz_cElement($dom, $order, 'envio_telefono', $envioTelefono);
			$clienten = $envioNombre;

			$orderitems = $order->appendChild($dom->createElement('order_items'));
			$i = 0;		
			foreach ($pedido->getAllItems() as $item) {
				if ($item) {
					$_product=Mage::getModel('catalog/product')->load($item->getProductId());
					$productoSku=$_product->getSku();
					$productoTypeId=$item->getProductType();
					$tallaId=$item->getBuyRequest()->getSuperAttribute()[133];
					$attributeTalla=Mage::getModel('catalog/resource_eav_attribute')->load(133);
					$productoTalla=$attributeTalla->getSource()->getOptionText($tallaId);
					$productoColor=$_product->getData('color_simple');
					$orderItemSku=$item->getSku();
					$orderProductoNombre=$item->getName();
					$orderPrecioProducto=$item->getBasePrice();
					$orderDescuento=$item->getBaseDiscountAmount();
					$orderIvaDelProducto=$item->getBaseTaxAmount();
					$orderPrecioProductoConIva=$item->getBasePriceInclTax();
					$orderItemQty=$item->getQtyOrdered();
					$orderItemQtyDevuelta=$item->getQtyRefunded();
					
					$orderitem = $orderitems->appendChild($dom->createElement('order_item'));  
					
					sz_cElement($dom, $orderitem, 'referencia', $productoSku);
					sz_cElement($dom, $orderitem, 'type_id', $productoTypeId);
					sz_cElement($dom, $orderitem, 'talla', $productoTalla);
					sz_cElement($dom, $orderitem, 'color', $productoColor);
					sz_cElement($dom, $orderitem, 'order_item_Sku', $orderItemSku);
					sz_cElement($dom, $orderitem, 'order_producto_nombre', $orderProductoNombre);
					sz_cElement($dom, $orderitem, 'order_precio_producto', $orderPrecioProducto);
					sz_cElement($dom, $orderitem, 'order_descuento', $orderDescuento);
					sz_cElement($dom, $orderitem, 'order_ivadelproducto', $orderIvaDelProducto);
					sz_cElement($dom, $orderitem, 'order_precio_producto_con_iva', $orderPrecioProductoConIva);
					sz_cElement($dom, $orderitem, 'order_item_cantidad', $orderItemQty);
					sz_cElement($dom, $orderitem, 'order_item_cantidad_devuelta', $orderItemQtyDevuelta);			

					$i++;
				}
			}
			echo "Factura procesada <br/>\n";
			echo "::::<br />\n";
			
			$payment=$pedido->getPayment();
			$orderMetodoPago=$payment->getMethod();
			$orderMetodoPagoAmpliado=$payment->getAdditionalInformation('paypal_payer_status');
			$orderSubtotal=$invoice->getBaseSubtotal();
			$orderCosteEnvio=$invoice->getBaseShippingAmount();
			$orderDescuento=$invoice->getBaseDiscountAmount();
			$orderImpuestos=$invoice->getBaseTaxAmount();
			$totalDevolucion=$invoice->getBaseTotalRefunded();
			$orderTotal=$invoice->getBaseGrandTotal();
			
			
			sz_cElement($dom, $order, 'order_metodo_pago', $orderMetodoPago); 
			sz_cElement($dom, $order, 'order_metodo_pago_ampliado', $orderMetodoPagoAmpliado); 
			sz_cElement($dom, $order, 'order_subtotal', $orderSubtotal);   
			sz_cElement($dom, $order, 'order_coste_envio', $orderCosteEnvio);
			sz_cElement($dom, $order, 'order_descuento', $orderDescuento);
			sz_cElement($dom, $order, 'order_impuestos', $orderImpuestos);
			sz_cElement($dom, $order, 'total_devolucion', $totalDevolucion);
			sz_cElement($dom, $order, 'order_total', $orderTotal);    
			
		}catch ( Exception $e) {
			echo "ERROR AL PROCESAR FACTURA ".  $currentinvoice ."; ". $e->getMessage() ."<br/>\n";
		}
	
		echo $currentinvoice. "<br />\n";
	}
	
}




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


//echo "<br>Factura actual: ".$currentinvoice;

$fecha = time ();
echo "<br>".date ( "h:i:s" , $fecha ); 

?>
