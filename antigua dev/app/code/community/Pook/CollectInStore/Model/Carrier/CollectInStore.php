<?php
/**
* Collect in store carrier model, always returns rate. Add logic in collectRates if restrictions required.
*/
class Pook_CollectInStore_Model_Carrier_CollectInStore extends Mage_Shipping_Model_Carrier_Abstract
{
	/**
	* shipping method identifier
	*/
	protected $_code = 'collectinstore';
	protected $_isFixed = true;

	/**
	* Collect rates for collect in store method based on information in $request
	*
	* @param Mage_Shipping_Model_Rate_Request $request
	* @return Mage_Shipping_Model_Rate_Result
	*/
	public function collectRates(Mage_Shipping_Model_Rate_Request $request)
	{
		if(!$this->getConfigData('active')) {
			return false;
		}

		$result = Mage::getModel('shipping/rate_result');

		$method = Mage::getModel('shipping/rate_result_method');
		$method->setCarrier($this->_code);
		$method->setCarrierTitle($this->getConfigData('title') );
		$method->setMethod($this->_code);
		$method->setMethodTitle($this->getConfigData('name') );
		$method->setPrice($this->getConfigData('price'));
		$method->setCost($this->getConfigData('price'));

		$result->append($method);

		return $result;
	}

	/**
	* Get the shipping method code.
	*
	* @return String
	*/
	public function getCode()
	{
		return $this->_code;
	}

	/**
	* This method is used when viewing / listing Shipping Methods with Codes programmatically
	*/
	public function getAllowedMethods()
	{
		return array($this->_code => $this->getConfigData('name'));
	}
}