<?php
require_once "../app/Mage.php";
Mage::app();
umask(0);
ob_end_clean();

$orderIds=array('10100000681','10100000683','10100000684');

foreach ($orderIds as $orderIncrementId){
	$order=Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
	enviarNotificacionTiendas($order);
	echo ("Enviada Notificacion del pedido $orderIncrementId");
	echo "<hr>";
}

function enviarNotificacionTiendas($order){
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
			echo "<hr>";
			echo "Error";
			var_dump($e);
			echo "<hr>";
			return false;
		}
		return true;
	}