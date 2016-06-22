<?php
require_once 'Mage/Checkout/controllers/OnepageController.php';
class Pook_CollectInStore_OnepageController extends Mage_Checkout_OnepageController
{
	/**
     * Rewrite to allow for collect from store shipping method
     */
    public function saveBillingAction()
    {
        /* Collect In Store Method */
        $carrier = Mage::getModel('pook_collectinstore/Carrier_CollectInStore');

        if ( $this->_expireAjax() ) {
            return;
        }

        if( $this->getRequest()->isPost() ) {

            $data = $this->getRequest()->getPost('billing', array());
            $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);

            if( isset( $data['email'] ) ) {
                $data['email'] = trim($data['email']);
            }
            $result = $this->getOnepage()->saveBilling($data, $customerAddressId);

            if(!isset($result['error'])) {
                /* check quote for virtual */
                if( $this->getOnepage()->getQuote()->isVirtual() ) {
                    $result['goto_section'] = 'payment';
                    $result['update_section'] = array(
                        'name' => 'payment-method',
                        'html' => $this->_getPaymentMethodsHtml()
                    );
                }
                elseif(isset($data['use_for_shipping']) && $data['use_for_shipping'] == 1) {
                    $result['goto_section'] = 'shipping_method';
                    $result['update_section'] = array(
                        'name' => 'shipping-method',
                        'html' => $this->_getShippingMethodsHtml()
                    );

                    $result['allow_sections'] = array('shipping');
                    $result['duplicateBillingInfo'] = 'true';

                    /** Clear out shipping method incase it was previously collectinstore */
                    if ($carrier->isActive()) {
                        $this->getOnepage()->getQuote()->getShippingAddress()->setShippingMethod(false);
                        $this->getOnepage()->getQuote()->collectTotals()->save();
                    }
                }
                elseif(isset($data['use_for_shipping']) && $data['use_for_shipping'] == 2) {

                    /* Set shipping address to configured store address... */
                    $data = array(
                        'address_id'            => null,
                        'firstname'             => $carrier->getConfigData('address_firstname'),
                        'lastname'              => $carrier->getConfigData('address_lastname'),
                        'company'               => $carrier->getConfigData('address_company'),
                        'street'                => array(
                                                        $carrier->getConfigData('address_line1'), 
                                                        $carrier->getConfigData('address_line2')
                                                    ),
                        'city'                  => $carrier->getConfigData('address_city'),
                        'region_id'             => 1,
                        'region'                => $carrier->getConfigData('address_region'),
                        'postcode'              => $carrier->getConfigData('address_postcode'),
                        'country_id'            => $carrier->getConfigData('address_country'),
                        'telephone'             => $carrier->getConfigData('address_telephone'),
                        'save_in_address_book'  => 0,
                        'same_as_billing'       => 1
                    );

                    /* 
                    *  Set TotalsCollectedFlag so the totals aren't calculated unneccessarily 
                    *  before adding the shipping method.
                    */
                    $this->getOnepage()->getQuote()->setTotalsCollectedFlag(true);
                    $this->getOnepage()->saveShipping($data, false);
                    $this->getOnepage()->getQuote()->getShippingAddress()->collectShippingRates();

                    /* Set shipping method to collectinstore... */
                    $method = $carrier->getCode() . '_' . $carrier->getCode();

                    /* Now reset TotalsCollectedFlag so the Shipping/shippingMethod totals are calculated. */
                    $this->getOnepage()->getQuote()->setTotalsCollectedFlag(false);
                    $this->getOnepage()->saveShippingMethod($method);

                    $this->getRequest()->setParam('shipping_method', $method);
                    Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method',
                        array( 
                            'request'   => $this->getRequest(),
                            'quote'     => $this->getOnepage()->getQuote() 
                        ) 
                    );

                    $this->getOnepage()->getQuote()->collectTotals()->save();

                    /* Jump straight to payment section... */
                    $result['goto_section'] = 'payment';
                    $result['update_section'] = array(
                        'name' => 'payment-method',
                        'html' => $this->_getPaymentMethodsHtml()
                    );
                }
                else {
                    $result['goto_section'] = 'shipping';
                }
            }

            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }
}