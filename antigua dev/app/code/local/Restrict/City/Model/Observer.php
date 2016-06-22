<?
/*help?
* save errors in events.log magento:  Mage::log('ERROR TEXT', null, 'events.log', true);
* console: tail events.log or tail -f events.log
*/


class Restrict_City_Model_Observer {



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
 public function NoShop($observer) {

		$billing = Mage::log(print_r(Mage::app()->getRequest()->getPost(), true));;
		
		$city = Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress();
        //foreach(Mage::getSingleton('checkout/session')->getQuote()->getAllItems() as $item){
	echo "hi";
            if($city=='MX'){
                Mage::throwException("You can't buy this product because you're shipping to the wrong place!");
            }   
     
	}
}

?>