<?
/*help?
* save errors in events.log magento:  Mage::log('ERROR TEXT', null, 'events.log', true);
* console: tail events.log or tail -f events.log
*/


class Webwow_Advancedcatalog_Model_Advancedcatalog_Observer extends Varien_Event_Observer{



/**
 * orderView private method observer magento
 *
 * @param array $observer
 * @return Mage_Sales_Model_Order
 */
 
public function __construct()
{

}
  public function speciaCupon($observer) {

		$item = $observer->getEvent()->getItem();
		$qty = $item->getQty();
			$totalDiscountAmount = 0;
		if($qty>=10)
		{
			$totalDiscountAmount = ($item->getPrice() * 10)*0.9;
		}
		if($qty>=100)
		{
			$totalDiscountAmount = ($item->getPrice() * 100)*0.8;
		}
		if($qty>=1000)
		{
			$totalDiscountAmount = ($item->getPrice() * 1000)*0.8;
		}

		$result = $observer->getResult();

		$result->setDiscountAmount($totalDiscountAmount);

		$result->setBaseDiscountAmount($totalDiscountAmount);




	  }
}

?>