<?php 

require_once "../app/Mage.php";
Mage::app();
umask(0);

Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$store_id = "1";

$ordersCollection = Mage::getModel('sales/order')->getCollection()
->addAttributeToSelect('*')
->addAttributeToFilter('status', array('in' =>array('enviado')));

echo 'recuento:'.count($ordersCollection);
$i=0;
foreach($ordersCollection as $order){
	$_order= Mage::getModel('sales/order')->load($order->getId());
	$hasidoentregado=false;
	$j=0;
	foreach ($_order->getStatusHistoryCollection(true) as $_item):
			if ($j==0){
				$fecha=$_item->getData('created_at');
			}
			$j++;
			if ($_item->getStatusLabel()=='07.- Entregado' || $_item->getStatusLabel()=='Delivered' || $_item->getStatusLabel()=='Entregado'):
				$hasidoentregado=true;

			endif;
			
			
	endforeach;
	$fecha_cambio=Date('2015-09-07');
	if ($hasidoentregado && ($fecha>=$fecha_cambio)){
		echo 'Pedido:'.$order->getIncrementId(). 'Fue Modificado en la fecha:'; 
		echo $fecha;
		echo'<br />';
		$state = 'complete';
		$status = 'entregado';
		$isCustomerNotified = false;
		$_order->setStatus($status,$isCustomerNotified);
		$_order->addStatusHistoryComment("Pedido modificado por error y corregido el 07/09/2015");
		$_order->save(); 
		$i++;
	}
	
	
}
echo '-->Recuento:'.$i;
	echo'<br />';
?>