<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Shipping
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Free shipping model
 *
 * @category   Mage
 * @package    Mage_Shipping
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Shipping_Model_Carrier_Freeshipping
    extends Mage_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{

    protected $_code = 'freeshipping';
    protected $_isFixed = true;

    /**
     * FreeShipping Rates Collector
     *
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return Mage_Shipping_Model_Rate_Result
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $result = Mage::getModel('shipping/rate_result');

        $this->_updateFreeMethodQuote($request);
		
//	Captura el valor total del carrito para ver si se debe aplicar el free shipping
		$quote = Mage::getModel('checkout/session')->getQuote();
		$quoteData= $quote->getData();
		$grandTotal=$quoteData['grand_total'];
//	Convierte el Grand Total a la moneda por defecto, Euros		
		$baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode(); 
		$currentCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();
$allowedCurrencies = Mage::getModel('directory/currency')->getConfigAllowCurrencies();
		$rates = Mage::getModel('directory/currency')->getCurrencyRates($baseCurrencyCode, array_values($allowedCurrencies));
		$grandTotal= $grandTotal/$rates[$currentCurrencyCode];
		
		
		
//	Comprobamos si se está aplicando alguna promoción shopping cart excluyente de free shipping
		$promocionesExcluidas= array (102,96,55,57,87);
		$appliedRuleIds = $quote->getAppliedRuleIds();		
		$appliedRuleIds = explode(',', $appliedRuleIds);
		$excluir=false;
		foreach ($appliedRuleIds as $rule):
			if (in_array($rule,$promocionesExcluidas)):
				$excluir=true;
			endif;
		endforeach;
		
// Comprobamos si existe productos rebajados para quitarlos del recuento final
		$paisesUE = array ('DE','AT','BE','BG','CY','HR','DK','SK','SI','ES','EE','FI','FR','GR','HU','IE','IT','LV','LT','LU','MT','NL','PL','PT','UK','CZ','RO','SE','GB');
		$paisCustomer = $quote->getShippingAddress()->getCountryId();
		$miembroUE=(in_array($paisCustomer,$paisesUE));
		$articulos= $quote->getAllItems();
		$total=0;
    	$cart = Mage::getModel('checkout/cart')->getQuote();
		foreach ($cart->getAllItems() as $item):
			$product=$item->getProduct();
			if ($product->getTypeID()=='configurable'):
				$precioAplicado=$item->getPrice();
				//if ($product->getPrice()<=$precioAplicado):
					if ($miembroUE):
						$total+=$item->getPriceInclTax()*$item->getQty();
					else:
						$total+=$item->getPrice()*$item->getQty();
					endif;
				//endif;
			endif;
		endforeach;
		
		if ((($request->getFreeShipping()) || ($total >= $this->getConfigData('free_shipping_subtotal'))) && !$excluir) {
            $method = Mage::getModel('shipping/rate_result_method');

            $method->setCarrier('freeshipping');
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod('freeshipping');
            $method->setMethodTitle($this->getConfigData('name'));

            $method->setPrice('0.00');
            $method->setCost('0.00');

            $result->append($method);
        }

        return $result;
    }

    /**
     * Allows free shipping when all product items have free shipping (promotions etc.)
     *
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return void
     */
    protected function _updateFreeMethodQuote($request)
    {
        $freeShipping = false;
        $items = $request->getAllItems();
        $c = count($items);
        for ($i = 0; $i < $c; $i++) {
            if ($items[$i]->getProduct() instanceof Mage_Catalog_Model_Product) {
                if ($items[$i]->getFreeShipping()) {
                    $freeShipping = true;
                } else {
                    return;
                }
            }
        }
        if ($freeShipping) {
            $request->setFreeShipping(true);
        }
    }

    public function getAllowedMethods()
    {
        return array('freeshipping'=>$this->getConfigData('name'));
    }

}
