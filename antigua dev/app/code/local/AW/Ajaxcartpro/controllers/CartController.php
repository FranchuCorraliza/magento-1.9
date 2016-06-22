<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Ajaxcartpro
 * @version    2.5.1
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */

class AW_Ajaxcartpro_CartController extends Mage_Core_Controller_Front_Action
{
    public function removeAction()
    {
        $response = Mage::getModel('ajaxcartpro/response');
        $id =  $this->getRequest()->getParam('id');
        Mage::getSingleton('checkout/cart')->removeItem($id)->save();
        if($this->getRequest()->getParam('is_checkout')) {
            $response->setCart(Mage::helper('ajaxcartpro')->renderBigCart());
            if(AW_Ajaxcartpro_Helper_Data::RETURN_CARTBAR_ON_BIGCART)
                $response->setCartbar(Mage::helper('ajaxcartpro')->renderCart());
        } else {
            $response->setCart(Mage::helper('ajaxcartpro')->renderCart());
        }
        $response->setLinks(Mage::helper('ajaxcartpro')->renderTopCartLinkTitle());
        $_quote = Mage::getSingleton('checkout/session')->getQuote();
        if($_quote && $_quote->getHasError()) $response->setError('quote error');
        $response->send();
    }
}
