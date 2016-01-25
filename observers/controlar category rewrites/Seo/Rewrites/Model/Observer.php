<?
/*help?
* save errors in events.log magento:  Mage::log('ERROR TEXT', null, 'events.log', true);
* console: tail events.log or tail -f events.log
*/


class Seo_Rewrites_Model_Observer {



/**
 * orderView private method observer magento
 *
 * @param array $observer
 * @return Mage_Sales_Model_Order
 */
 
public function __construct()
{

}
  public function categoryRewrites($observer) {

		Mage::log('Se ha guardado una categoria', null, 'observador.log');
	    $category = $observer->getEvent()->getCategory();
		Mage::log('La categoria guardada es:'.$category->getName(), null, 'observador.log');		


	  }
	  public function manufacturerRewrites($observer) {

	    $product = $observer->getEvent()->getProduct();

	  }
}

?>