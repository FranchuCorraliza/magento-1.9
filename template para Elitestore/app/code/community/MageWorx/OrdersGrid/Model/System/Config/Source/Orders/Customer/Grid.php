<?php

/**
 * MageWorx
 * Admin Order Grid  extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersGrid
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_OrdersGrid_Model_System_Config_Source_Orders_Customer_Grid
{

    /**
     * @param bool|false $isMultiselect
     * @return array
     */
    public function toOptionArray($isMultiselect = false)
    {
        /** @var MageWorx_OrdersGrid_Helper_Data $helper */
        $helper = Mage::helper('mageworx_ordersgrid');

        $options = array(
            0 => array(
                'label' => 'Order Details',
                'value' => array(
                    array('value' => 'increment_id', 'label' => Mage::helper('customer')->__('Order #')),
                    array('value' => 'created_at', 'label' => Mage::helper('customer')->__('Purchase On')),
                    array('value' => 'qnty', 'label' => $helper->__('Qnty')),
                    array('value' => 'coupon_code', 'label' => $helper->__('Coupon Code')),
                    array('value' => 'order_comment', 'label' => $helper->__('Order Comment(s)')),
                    array('value' => 'order_group', 'label' => $helper->__('Group')),
                    array('value' => 'store_id', 'label' => $helper->__('Bought From')),
                    array('value' => 'is_edited', 'label' => $helper->__('Edited')),
                    array('value' => 'status', 'label' => Mage::helper('sales')->__('Status')),
                    array('value' => 'action', 'label' => Mage::helper('customer')->__('Action')),
                )
            ),
            1 => array(
                'label' => 'Order Amounts',
                'value' => array(
                    0 => array('value' => 'base_tax_amount', 'label' => $helper->__('Tax Amount (Base)')),
                    1 => array('value' => 'tax_amount', 'label' => $helper->__('Tax Amount (Purchased)')),
                    2 => array('value' => 'base_discount_amount', 'label' => $helper->__('Discount (Base)')),
                    3 => array('value' => 'discount_amount', 'label' => $helper->__('Discount (Purchased)')),
                    4 => array('value' => 'base_internal_credit', 'label' => $helper->__('Internal Credit (Base)')), // 20
                    5 => array('value' => 'internal_credit', 'label' => $helper->__('Internal Credit (Purchased)')), // 21
                    6 => array('value' => 'base_total_refunded', 'label' => $helper->__('Total Refunded (Base)')),
                    7 => array('value' => 'total_refunded', 'label' => $helper->__('Total Refunded (Purchased)')),
                    8 => array('value' => 'grand_total', 'label' => Mage::helper('customer')->__('Order Total')),
                )
            ),
            2 => array(
                'label' => 'Product Information',
                'value' => array(
                    array('value' => 'product_names', 'label' => $helper->__('Product Name(s)')),
                    array('value' => 'product_skus', 'label' => $helper->__('SKU(s)')),
                    array('value' => 'product_options', 'label' => $helper->__('Product Option(s)')),
                    array('value' => 'weight', 'label' => $helper->__('Weight')),
                )
            ),
            3 => array(
                'label' => 'Billing Information',
                'value' => array(
                    array('value' => 'payment_method', 'label' => $helper->__('Payment Method')),
                    array('value' => 'billing_name', 'label' => Mage::helper('customer')->__('Bill to Name')),
                    array('value' => 'billing_company', 'label' => $helper->__('Bill to Company')),
                    array('value' => 'billing_city', 'label' => $helper->__('Bill to City')),
                    array('value' => 'billing_postcode', 'label' => $helper->__('Billing Postcode')),
                )
            ),
            4 => array(
                'label' => 'Shipping Information',
                'value' => array(
                    array('value' => 'shipping_method', 'label' => $helper->__('Shipping Method')),
                    array('value' => 'tracking_number', 'label' => $helper->__('Tracking Number')),
                    array('value' => 'shipped', 'label' => $helper->__('Shipped')),
                    array('value' => 'shipping_name', 'label' => Mage::helper('customer')->__('Shipped to Name')),
                    array('value' => 'shipping_company', 'label' => $helper->__('Ship to Company')),
                    array('value' => 'shipping_city', 'label' => $helper->__('Ship to City')),
                    array('value' => 'shipping_postcode', 'label' => $helper->__('Shipping Postcode')),
                )
            ),
            5 => array(
                'label' => 'Customer Information',
                'value' => array(
                    array('value' => 'customer_email', 'label' => $helper->__('Customer Email')),
                    array('value' => 'customer_group', 'label' => $helper->__('Customer Group')),
                )
            ),
            6 => array(
                'label' => 'Invoice Information',
                'value' => array(
                    array('value' => 'invoice_increment_id', 'label' => $helper->__('Invoice(s)'))
                )
            ),
        );

        if (!Mage::getConfig()->getModuleConfig('MageWorx_CustomerCredit')->is('active', true)) {
            unset($options[1]['value'][4]); // Internal Credit (Base)
            unset($options[1]['value'][5]); // Internal Credit (Purchased)
        }

        return $options;
    }

    public function toArray()
    {

        $options = array(
            'increment_id',
            'created_at',
            'product_names',
            'product_skus',
            'product_options',
            'qnty',
            'weight',
            'billing_name',
            'shipping_name',
            'shipping_method',
            'tracking_number',
            'shipped',
            'customer_email',
            'customer_group',
            'payment_method',
            'base_tax_amount',
            'tax_amount',
            'coupon_code',
            'base_discount_amount',
            'discount_amount',
            'base_internal_credit',
            'internal_credit',
            'billing_company',
            'shipping_company',
            'billing_city',
            'shipping_city',
            'billing_postcode',
            'shipping_postcode',
            'base_total_refunded',
            'total_refunded',
            'grand_total',
            'order_comment',
            'order_group',
            'store_id',
            'is_edited',
            'status',
            'action',
            'invoice_increment_id'
        );

        if (!Mage::getConfig()->getModuleConfig('MageWorx_CustomerCredit')->is('active', true)) {
            unset($options[20]); // Internal Credit (Base)
            unset($options[21]); // Internal Credit (Purchased)
        }

        return $options;
    }
}