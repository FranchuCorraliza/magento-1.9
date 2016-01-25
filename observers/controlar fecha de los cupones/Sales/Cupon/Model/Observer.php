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

	    		if($results && $ruleId==102)
	    		{
	    		   Mage::getSingleton('core/session')->addError('Sorry, you coupon are expired');
		           Mage::getSingleton('checkout/cart')->getQuote()->setCouponCode('aaaaaaaaaaa');
	    		}
	    		

//SELECT * FROM salesrule_coupon WHERE code='Y1ZCH1SS' AND created_at > DATE_SUB(NOW(), INTERVAL 7 DAY)	    		
		               
	  }
}

?>