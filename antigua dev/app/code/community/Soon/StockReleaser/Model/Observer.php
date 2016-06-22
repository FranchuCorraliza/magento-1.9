<?php

/**
 * Agence Soon
 *
 * @category    Soon
 * @package     Soon_StockReleaser
 * @copyright   Copyright (c) 2011 Agence Soon. (http://www.agence-soon.fr)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author      Hervé Guétin
 */
class Soon_StockReleaser_Model_Observer extends Mage_Core_Model_Abstract {

    /**
     * Retrieve order object from observer and register cancellation date
     * 
     * @param Varien_Event_Observer $observer
     * @return Soon_StockReleaser_Model_Observer
     */
    public function setAutoCancelData($observer) {
        $order = $observer->getEvent()->getOrder();
        Mage::getModel('stockreleaser/cancel')->registerCancel($order);
        return $this;
    }

    /**
     * Update "Back" button on order view page to make it link to 
     * stockreleaser_adminhtml_sales_order_canceled_grid
     * instead of core Orders grid.
     * 
     * @return Soon_StockReleaser_Model_Observer
     */
    public function updateOrderViewGetBackButton() {
        if (Mage::app()->getRequest()->getParam('rel')) { // If we come from the canceled orders grid, there is a 'rel' param
            $layout = Mage::app()->getLayout();
            $buttonsBlock = $layout->getBlock('sales_order_edit');

            $newUrl = Mage::helper('adminhtml')->getUrl('adminhtml/sales_order_canceled/');

            $buttonData = array(
                'label' => Mage::helper('adminhtml')->__('Back'),
                'onclick' => 'setLocation(\'' . $newUrl . '\')',
                'class' => 'back',
            );

            $buttonsBlock->updateButton('back', null, $buttonData);
        }

        return $this;
    }

}
