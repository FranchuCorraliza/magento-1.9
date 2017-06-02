<?php

/**
 * @author Marcelo Jacobus <marcelo.jacobus@gmail.com>
 *
 * Paths follow the following pattern:
 * /:module/:controller/:action
 *
 * :module is what matches package/module/config.xml
 *         config.fronten.routers.{module}.args.frontName value,
 *         and in this case 'helloworld'
 *
 * :controller defaults to index
 * :action defaults to index
 *
 */
class Elite_Ajaxcontrol_IndexController extends Mage_Core_Controller_Front_Action
{

    /**
     * paths: /helloworld/
     *        /helloworld/index
     *        /helloworld/index/index
     *
     * @return void
     */
    public function indexAction()
    {
        // Loads layouts inside app/design/frontend/base/default/layout
        // The layout of this page is in the helloworld.xml
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * path: /helloworld/index/goodbye
     * @return void
     */
    public function goodByeAction()
    {
        $this->loadLayout()->renderLayout();
    }
	
	public function sugerenciasAction()
    {
        $this->loadLayout()->renderLayout();
    }
	public function menuAction()
    {
        $this->loadLayout()->renderLayout();
    }
	public function estilismoAction()
    {
        $this->loadLayout()->renderLayout();
    }
    public function updatecartAction()
    {
        $this->loadLayout()->renderLayout();
    }
	public function updateloginAction()
	{
		$this->loadLayout()->renderLayout();
	}
	public function ourstoresAction()
	{
		$this->loadLayout()->renderLayout();
	}
	public function quickloginAction()
    {
        $this->loadLayout()->renderLayout();
    }
	protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }
	protected function _getQuote()
    {
        return $this->_getCart()->getQuote();
    }
	public function estimateshippingAction()
	{
		$country    = (string) $this->getRequest()->getParam('country_id');
        $postcode   = (string) $this->getRequest()->getParam('estimate_postcode');
        $city       = (string) $this->getRequest()->getParam('estimate_city');
        $regionId   = (string) $this->getRequest()->getParam('region_id');
        $region     = (string) $this->getRequest()->getParam('region');

        $this->_getQuote()->getShippingAddress()
            ->setCountryId($country)
            ->setCity($city)
            ->setPostcode($postcode)
            ->setRegionId($regionId)
            ->setRegion($region)
            ->setCollectShippingRates(true);
        $this->_getQuote()->save();
		$this->_getQuote()->getShippingAddress()->collectShippingRates()->save();
    	$this->loadLayout()->renderLayout();
	}
}