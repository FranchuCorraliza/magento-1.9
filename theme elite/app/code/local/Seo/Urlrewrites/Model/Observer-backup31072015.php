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
		if($customerGroupId==1)
		{
		    switch ($status) {
			    case "rechazado":
			        $order = $observer->getEvent()->getOrder();
	    			$customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
						    if ($customer->getId()) {
						                $customer->setGroupId(12);
						                $customer->save();
						                $order->setCustomerGroupId($customer->getGroupId());
						    		}
			        break;
			    case "entregado":
			        $order = $observer->getEvent()->getOrder();
	    			$customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
						    if ($customer->getId()) {
						                $customer->setGroupId(13);
						                $customer->save();
						                $order->setCustomerGroupId($customer->getGroupId());
						    		}
			        break;
			}
		}
	  }
}

?>