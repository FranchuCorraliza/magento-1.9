<?php
/*help?
* save errors in events.log magento:  Mage::log('ERROR TEXT', null, 'events.log', true);
* console: tail events.log or tail -f events.log
*/


class Invoice_Order_Model_Observer {



/**
 * orderView private method observer magento
 *
 * @param array $observer
 * @return Mage_Sales_Model_Order
 */
	private static $_handleCustomerFirstOrderCounter = 1;
	public function __construct()
	{
	
	}
	public function orderView($observer) {
	
			$order = $observer->getEvent()->getOrder();
			$status = $order->getStatus();
	
			$customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
	
			$customerGroupId = $order->getCustomerGroupId();
			 
//-------------------------------------------------------------------------------------		
		

		$shippingAddress=$order->getShippingAddress();

		$resource = Mage::getSingleton('core/resource');
    	$readConnection = $resource->getConnection('core_read');
		$query = "SELECT `at_postcode`.`value` AS `postcode`, `at_country`.`value` AS `country` FROM `customer_address_entity` AS `e`
INNER JOIN `customer_address_entity_varchar` AS `at_postcode` ON (`at_postcode`.`entity_id` = `e`.`entity_id`) AND (`at_postcode`.`attribute_id` = '30')
INNER JOIN `customer_address_entity_varchar` AS `at_country` ON (`at_country`.`entity_id` = `e`.`entity_id`) AND (`at_country`.`attribute_id` = '27')
INNER JOIN `customer_entity` AS `customer` ON (customer.entity_id =e.parent_id)
WHERE (`e`.`entity_type_id` = '2') AND (customer.group_id = 12)
	AND (`at_postcode`.`value`='".$shippingAddress->getPostcode()."')  AND (`at_country`.`value`='".$shippingAddress->getCountry()."')";
		
		

		$result=$readConnection->fetchAll($query);
		$usuario=Mage::getSingleton('admin/session')->getUser();
		
		if ($result && !$usuario && ($status=='confirmar_pago')){  //Si la pasarela de pagos guarda un pedido en estado 02.-Pendiente Facturación y cliente es dudoso

 			if ($customer->getId()) {
						                $customer->setGroupId(20);
						                $customer->save();
    		}

			$state = 'processing';
			$status = 'processing'; //el código processing pertenece al estado "02.- Confirmar Pago
			$isCustomerNotified = false;
			$order->setState($state, $status,$isCustomerNotified);
			$order->save();
		
		} elseif ($result && !$usuario && ($status=='processing')){ // Si la pasarela de pagos guarda un pedido en estado 01.-Confirmar Pago y cliente es dudoso
			if ($customer->getId()) {
						                $customer->setGroupId(20);
						                $customer->save();
			}
		
		} elseif ($result && $usuario && $status=='confirmar_pago'){ //Si un usuario cambia un pedido dudoso a 02.- Pendiente de Facturación
			if ($customer->getId()) {
						                $customer->setGroupId(21);
						                $customer->save();
    		}
		}
		
		

//-------------------------------------------------------------------------------------		
	
	
				switch ($status) {
					case "rechazado":
							//$order = $observer->getEvent()->getOrder();
							//$customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
									if ($customer->getId()) {
											$customer->setGroupId(12);
											$customer->save();
											$order->setCustomerGroupId($customer->getGroupId());
										}
						break;
					case "entregado":
						if($customerGroupId==1){
							//$order = $observer->getEvent()->getOrder();
							//$customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
								if ($customer->getId()) {
											$customer->setGroupId(13);
											$customer->save();
											$order->setCustomerGroupId($customer->getGroupId());
										}
						}
						break;
					case "reservar_tienda":
							//		$order = $observer->getEvent()->getOrder();
									$storeId = $order->getStoreId();
									
									
									//Creamos la factura para que envíe el albarán a MANAGER
									try {
										if(!$order->canInvoice())
										{
											Mage::throwException(Mage::helper('core')->__('Cannot create an invoice.'));
										}
										 
										$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();
										 
										if (!$invoice->getTotalQty()) {
											Mage::throwException(Mage::helper('core')->__('Cannot create an invoice without products.'));
										}
										 
										$invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
										$invoice->register();
										$transactionSave = Mage::getModel('core/resource_transaction')
										->addObject($invoice)
										->addObject($invoice->getOrder());
										 
										$transactionSave->save();
									}
										catch (Mage_Core_Exception $e) {
										 
									}
									
									//Enviamos notificacion a las tiendas
									
									$helper = Mage::helper('inchoo_adminOrderNotifier');
									try {
										$templateId = $helper->getEmailTemplate($storeId);
	
										$mailer = Mage::getModel('core/email_template_mailer');
	
										if ($helper->getNotifyGeneralEmail()) {
											$emailInfo = Mage::getModel('core/email_info');
											$emailInfo->addTo($helper->getStoreEmailAddressSenderOption('general', 'email'), $helper->getStoreEmailAddressSenderOption('general', 'name'));
											$mailer->addEmailInfo($emailInfo);
										}
	
										if ($helper->getNotifySalesEmail()) {
											$emailInfo = Mage::getModel('core/email_info');
											$emailInfo->addTo($helper->getStoreEmailAddressSenderOption('sales', 'email'), $helper->getStoreEmailAddressSenderOption('sales', 'name'));
											$mailer->addEmailInfo($emailInfo);
										}
	
										if ($helper->getNotifySupportEmail()) {
											$emailInfo = Mage::getModel('core/email_info');
											$emailInfo->addTo($helper->getStoreEmailAddressSenderOption('support', 'email'), $helper->getStoreEmailAddressSenderOption('support', 'name'));
											$mailer->addEmailInfo($emailInfo);
										}
	
										if ($helper->getNotifyCustom1Email()) {
											$emailInfo = Mage::getModel('core/email_info');
											$emailInfo->addTo($helper->getStoreEmailAddressSenderOption('custom1', 'email'), $helper->getStoreEmailAddressSenderOption('custom1', 'name'));
											$mailer->addEmailInfo($emailInfo);
										}
	
										if ($helper->getNotifyCustom2Email()) {
											$emailInfo = Mage::getModel('core/email_info');
											$emailInfo->addTo($helper->getStoreEmailAddressSenderOption('custom2', 'email'), $helper->getStoreEmailAddressSenderOption('custom2', 'name'));
											$mailer->addEmailInfo($emailInfo);
										}
	
										foreach ($helper->getNotifyEmails() as $entry) {
											$emailInfo = Mage::getModel('core/email_info');
											$emailInfo->addTo($entry['email'], $entry['name']);
											$mailer->addEmailInfo($emailInfo);
										}
	
										$mailer->setSender(array(
											'name' => $helper->getStoreEmailAddressSenderOption('general', 'name'),
											'email' => $helper->getStoreEmailAddressSenderOption('general', 'email'),
										));
	
										$mailer->setStoreId($storeId);
										$mailer->setTemplateId($templateId);
										$mailer->setTemplateParams(array(
											'order' => $order,
										));
										
										
										
										$mailer->send();
									} catch (Exception $e) {
										Mage::logException($e);
									}
	
	
									
						break;
						case "processing": //el código processing pertenece al estado 01.- Confirmar Pago
												
							if (!$result && $customer->getGroupId()!='12'):
								if ($order->getPayment()->getMethodInstance()->getCode()=='paypal_express'):
									$countryId=$order->getShippingAddress()->getCountryId();
									if ($order->getPayment()->getAdditionalInformation('paypal_payer_status')=="verified" && !($countryId=='GB' || $countryId=='IE')): //Confirmamos estado verificado y pais de envío distinto de GB e IE
											$state = 'processing';
											$status = 'confirmar_pago'; //el código confirmar_pago pertenece al estado "02.- Pendiente Facturación
											$isCustomerNotified = false;
											$order->setState($state, $status,$isCustomerNotified);
											$order->save(); 
									endif;
								endif;
							endif;
															
						break;
	
						
		
	
				}




	}
	
	
	public function checkLimitedProducts($observer){
		$event = $observer->getEvent();
		$quoteItem=$observer->getQuoteItem();
		$carrito = $quoteItem->getQuote();
		$items=$carrito->getItemsCollection();
		$parentId=$quoteItem->getProduct()->getParentProductId();
		if (!$parentId){
			$parentId = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($quoteItem->getProduct()->getId())[0];
			
		}
		$maxQty=Mage::getModel('catalog/product')->load($parentId)->getData('max_qty');
		$currentQty=0;
		if($maxQty!=''){
			$vendidaQty=0;
			if (Mage::getSingleton('customer/session')->isLoggedIn()) {
				$orders= Mage::getModel('sales/order')->getCollection()
					->addAttributeToFilter('customer_id',Mage::getSingleton('customer/session')->getCustomer()->getId())
					->addFieldToFilter('state', array('nin' => array('canceled','closed')));
				foreach($orders as $eachOrder){
					$order = Mage::getModel("sales/order")->load($eachOrder->getId()); 
					$items2 = $order->getAllVisibleItems();
					
					foreach($items2 as $item2):
						if ($item2->getProductId()==$parentId){
							$vendidaqty=$vendidaqty+$item2->getQtyOrdered();
							
						}
						
					endforeach;
				}
			}
			foreach ($items as $item){
				if ($item->getProduct()->getId()==$parentId){
					$currentQty+=$item->getQty();
				}
			}
			if($maxQty<($vendidaqty+$currentQty)){
				$quoteItem->truncate();
			}
		}
	}
	
	public function checkLimitedProductsUpdate($observer){
		$cart=$observer->getCart()->getQuote();
		$items=$cart->getAllVisibleItems();
		$recuentos=array();
		foreach ($items as $item){
			$maxQty=Mage::getModel('catalog/product')->load($item->getProduct()->getId())->getData('max_qty');
			if ($maxQty!=''){
				$recuentos[$item->getProduct()->getId()]['max_qty']=$maxQty;
				$recuentos[$item->getProduct()->getId()]['current_qty']+=$item->getQty();
			}
		}
		foreach ($recuentos as $item){
			if ($item['max_qty']<$item['current_qty']){
				$cart->truncate();
			}
		}
		
	}
}
