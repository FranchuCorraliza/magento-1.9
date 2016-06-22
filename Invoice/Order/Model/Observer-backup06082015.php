<?
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
		
		    switch ($status) {
			    case "rechazado":
					if($customerGroupId==1){
						$order = $observer->getEvent()->getOrder();
						$customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
								if ($customer->getId()) {
						                $customer->setGroupId(12);
						                $customer->save();
						                $order->setCustomerGroupId($customer->getGroupId());
						    		}
						}
			        break;
			    case "entregado":
					if($customerGroupId==1){
				        $order = $observer->getEvent()->getOrder();
	    				$customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
						    if ($customer->getId()) {
						                $customer->setGroupId(13);
						                $customer->save();
						                $order->setCustomerGroupId($customer->getGroupId());
						    		}
					}
			        break;
			    case "reservar_tienda":
			        			$order = $observer->getEvent()->getOrder();
						        $storeId = $order->getStoreId();
								
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
						        $order->setState('processing','processing',false);
						        $order->save();
			        break;
					case "processing":
						$order = $observer->getEvent()->getOrder();
						/*if ($order->getPayment()->getAdditionalInformation('paypal_payer_status')=="verified"):
						        $state = 'processing';
				                $status = 'confirmar_pago';
                				$isCustomerNotified = false;
                				$order->setState($state, $status,$isCustomerNotified);
				                $order->save(); 
						endif;
						*/
			        break;
			}
		}
	  }

?>