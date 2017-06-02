<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Full Page Cache
 * @version   1.0.32
 * @build     662
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_Fpc_Helper_Processor_Cartexclude extends Mage_Core_Helper_Abstract
{
    /**
     * Add excluded from cache cart blocks to container
     *
     * @param array $containers
     * @param string $blockType
     * @param string $blockName
     * @return array
     */
    public function addCartContainerToExclude($containers, $blockType, $blockName)
    {
        $ignoredBlock = array(
            'ajaxcart/hidden_inject_template',   //Ophirah_Qquoteadv
            'amcart/config',                     //Ophirah_Qquoteadv
            'ajaxcart/hidden_inject_product',    //Ophirah_Qquoteadv
            'ajaxcart/hidden_inject_top',        //Ophirah_Qquoteadv
            'qquoteadv/checkout_cart_miniquote', //Ophirah_Qquoteadv
            'mgx_cartoucheplus/catalog_category_view_filter_brand', //Magestix_Cartoucheplus
            'ajax_cart/addtocart', //Alioze_AjaxCart
            'checkout/cart_item_renderer',
        );
        if ((strpos($blockType, 'checkout') !== false
                || strpos($blockType, 'cart') !== false)
            && !in_array($blockType, $ignoredBlock)
        ) {
            $newContainerRow[$blockType][$blockName] = array(
                'container'      => 'Mirasvit_Fpc_Model_Container_Base',
                'block'          => $blockType,
                'cache_lifetime' => 0,
                'name'           => $blockName,
                'in_register'    => false,
                'depends'        => 'store,cart,customer,customer_group',
                'in_session'     => true,
                'in_app'         => 0
            );
            $containers = array_merge($containers, $newContainerRow);
        }

        return $containers;
    }
}