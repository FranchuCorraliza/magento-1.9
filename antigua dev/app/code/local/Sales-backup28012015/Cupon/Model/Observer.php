<?
/*help?
* save errors in events.log magento:  Mage::log('ERROR TEXT', null, 'events.log', true);
* console: tail events.log or tail -f events.log
*/


class Sales_Cupon_Model_Observer {



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
  public function CuponRule($observer) {

	    $rule = $observer->getEvent()->getRule();
	    $status = $rule->getStatus();
	    $couponCode = $rule->getCouponCode();
	    $ruleId = $rule->getRuleId();
	    $cuponText = $rule->getCode();//obtenemos el codigo introducido para el cupón

		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');

		$query = 'SELECT * FROM salesrule_coupon WHERE code=\'' . $cuponText . '\' AND created_at < DATE_SUB(NOW(), INTERVAL 7 DAY)';

		$results = $readConnection->fetchAll($query);

	    		if($results && $ruleId=="102")
	    		{
	    		   Mage::getSingleton('core/session')->addError('Sorry, you coupon are expired');
		           Mage::getSingleton('checkout/cart')->getQuote()->setCouponCode('aaaaaaaaaaa');
	    		}		            
	  }
	public function CancelOrder($observer) {

			$order = $observer->getEvent()->getOrder();
			        if ($code = $order->getCouponCode()) {
			            $coupon = Mage::getModel('salesrule/coupon')->load($code, 'code');
			            if ($coupon->getTimesUsed() > 0) {
			                $coupon->setTimesUsed($coupon->getTimesUsed() - 1);
			                $coupon->save();
			            }

			            $rule = Mage::getModel('salesrule/rule')->load($coupon->getRuleId());
			            error_log("\nrule times used=" . $rule->getTimesUsed(), 3, "var/log/debug.log");
			            if ($rule->getTimesUsed() > 0) {
			                $rule->setTimesUsed($rule->getTimesUsed()-1);
			                $rule->save();
			            }

			            if ($customerId = $order->getCustomerId()) {
			                if ($customerCoupon = Mage::getModel('salesrule/rule_customer')->loadByCustomerRule($customerId, $rule->getId())) {
			                    $couponUsage = new Varien_Object();
			                    Mage::getResourceModel('salesrule/coupon_usage')->loadByCustomerCoupon($couponUsage, $customerId, $coupon->getId());
								$laquesea=$couponUsage->getTimesUsed();
			                    if ($laquesea >= 1) {
			                        /* I can't find any #@$!@$ interface to do anything but increment a coupon_usage record */
			                        $resource = Mage::getSingleton('core/resource');
			                        $writeConnection = $resource->getConnection('core_write');
			                        $tableName = $resource->getTableName('salesrule_coupon_usage');

			                        $query = "UPDATE {$tableName} SET times_used = times_used-1 "
			                            .  "WHERE coupon_id = {$coupon->getId()} AND customer_id = {$customerId} AND times_used > 0";

			                        $writeConnection->query($query);
									$laquesea--;
									if ($laquesea==0) {
										$query = "DELETE FROM {$tableName} WHERE coupon_id = {$coupon->getId()} AND customer_id = {$customerId}"; 	
				                        $writeConnection->query($query);
									}
									
			                    }

			                    if ($customerCoupon->getTimesUsed() > 0) {
			                        $customerCoupon->setTimesUsed($customerCoupon->getTimesUsed()-1);
			                        $customerCoupon->save();
			                    }
			                }
			            }
			        }
	  }
}

?>