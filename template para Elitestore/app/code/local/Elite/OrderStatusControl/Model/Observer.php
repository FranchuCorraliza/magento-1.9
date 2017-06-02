<?php

class Elite_OrderStatusControl_Model_Observer {



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
	
	public function orderStatusControl($observer) {
		$order = $observer->getEvent()->getOrder();
		$status = $order->getStatus();
		$customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
		$customerGroupId = $order->getCustomerGroupId();
		$shippingAddress=$order->getShippingAddress();
		$checkAddress=$this->checkAddress($shippingAddress);
		$isAdmin=Mage::getSingleton('admin/session')->getUser();
		
		if ($checkAddress && !$isAdmin && ($status=='confirmar_pago')){  //Si la pasarela de pagos guarda un pedido en estado 02.-Pendiente Facturación y cliente es dudoso
 			if ($customer->getId()) {
				$customer->setGroupId(7);
				$customer->save();
    		}

			$state = 'processing';
			$status = 'processing'; //el código processing pertenece al estado "02.- Confirmar Pago
			$isCustomerNotified = false;
			$order->setState($state, $status,$isCustomerNotified);
			$order->save();
		
		} elseif ($checkAddress && !$isAdmin && ($status=='processing')){ // Si la pasarela de pagos guarda un pedido en estado 01.-Confirmar Pago y cliente es dudoso
			if ($customer->getId()) {
						                $customer->setGroupId(7);
						                $customer->save();
			}
		
		} elseif ($checkAddress && $isAdmin && $status=='confirmar_pago'){ //Si un usuario cambia un pedido dudoso a 02.- Pendiente de Facturación
			if ($customer->getId()) {
						                $customer->setGroupId(9);
						                $customer->save();
    		}
		}
		switch ($status) {
			case "rechazado":
				if ($customer->getId()) {
					$customer->setGroupId(10);
					$customer->save();
					$order->setCustomerGroupId($customer->getGroupId());
					//$order->save();
				}
				break;
			case "entregado":
				if($customerGroupId==1){
					if ($customer->getId()) {
						$customer->setGroupId(8);
						$customer->save();
						$order->setCustomerGroupId($customer->getGroupId());
						//$order->save();
					}
				}
				break;
			case "reservar_tienda":
				$this->generarFactura($order);
				$this->enviarNotificacionTiendas($order);
				break;
			case "processing": //el código processing pertenece al estado 01.- Confirmar Pago										
				if (!$checkAddress && $customer->getGroupId()!='12'):
					if ($order->getPayment()->getMethodInstance()->getCode()=='paypal_express'):
							$countryId=$order->getShippingAddress()->getCountryId();
							$historial=$order->getStatusHistoryCollection();
							$payment_reviews=false;
								
							foreach ($historial as $comment){
								if ($comment->getStatus()=='payment_reviews'){
									$paymentreviews=true;
								}
									
							}
							if ($order->getPayment()->getAdditionalInformation('paypal_payer_status')=="verified" && !($countryId=='GB' || $countryId=='IE' || $countryId=='CA') && !$paymentreviews): //Confirmamos estado verificado y pais de envío distinto de GB e IE o si el pedido ha pasado por Payment Reviews
									$state = 'processing';
									$status = 'confirmar_pago'; //el código confirmar_pago pertenece al estado "02.- Pendiente Facturación
									$isCustomerNotified = false;
									$order->setState($state, $status,$isCustomerNotified);
									$order->save(); 
							endif;
						endif;
					endif;
				break;
				case "uncleared":
					$this->generarFactura($order);
					$this->enviarNotificacionTiendas($order);
					$state = 'processing';
					$status = 'processing'; //cambiamos a confirmar pago
					$isCustomerNotified = false;
					$order->setState($state, $status,$isCustomerNotified);
					$order->save(); 
		}
	}
		
	private function generarFactura($order){
		$customerId=$order->getCustomerId();
		
		if ($customerId!=1470){ //Si es un pedido para Mojgan no queremos crear factura
			//Creamos la factura para que envíe el albarán a MANAGER
			try {
				if(!$order->canInvoice()){
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
			}catch (Mage_Core_Exception $e) {
				Mage::logException($e);
				return false;
			}
		}						
		return true;
	}
	
	private function enviarNotificacionTiendas($order){
		$storeId = $order->getStoreId();
		$helper = Mage::helper('inchoo_adminOrderNotifier');
		try {
			if ($customerId==1470){
					$templateId=82;
			}else{
				$templateId = $helper->getEmailTemplate($storeId);
			}
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
			return false;
		}
		return true;
	}
	
	
	private function checkAddress($shippingAddress){
		$result=false;
		if ($shippingAddress){
			$resource = Mage::getSingleton('core/resource');
			$readConnection = $resource->getConnection('core_read');
			$query = "SELECT `at_postcode`.`value` AS `postcode`, `at_country`.`value` AS `country` FROM `customer_address_entity` AS `e`
				INNER JOIN `customer_address_entity_varchar` AS `at_postcode` ON (`at_postcode`.`entity_id` = `e`.`entity_id`) AND (`at_postcode`.`attribute_id` = '30')
				INNER JOIN `customer_address_entity_varchar` AS `at_country` ON (`at_country`.`entity_id` = `e`.`entity_id`) AND (`at_country`.`attribute_id` = '27')
				INNER JOIN `customer_entity` AS `customer` ON (customer.entity_id =e.parent_id) WHERE (`e`.`entity_type_id` = '2') AND (customer.group_id = 10) 
				AND (`at_postcode`.`value`='".$shippingAddress->getPostcode()."')  AND (`at_country`.`value`='".$shippingAddress->getCountry()."')";
			$result=$readConnection->fetchAll($query);
		}
		return $result;
		
	}
}