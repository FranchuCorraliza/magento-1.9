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



class Mirasvit_Fpc_Model_Blockmf_Productviewed extends Mirasvit_Fpc_Model_Blockmf_Abstract
{
    const COOKIE_NAME = 'FPC_PRODUCT_VIEWED';

    /**
     * @param Varien_Object $params
     * @return bool
     */
    public static function registerViewedProduct($params)
    {
        if ($params->getId()) {
            $cookie = Mage::app()->getCookie();

            $productId = $params->getId();
            $ids = explode(',', $cookie->get(self::COOKIE_NAME));

            if (!in_array($productId, $ids)) {
                array_unshift($ids, $productId);
            }
            $ids = array_slice(
                array_unique($ids),
                0,
                Mage::getStoreConfig(Mage_Reports_Block_Product_Viewed::XML_PATH_RECENTLY_VIEWED_COUNT)
            );

            $cookie->set(self::COOKIE_NAME, implode(',', $ids));
        }

        return true;
    }

    /**
     * @return array
     */
    protected function _getProductIds()
    {
        $result = Mage::app()->getCookie()->get(self::COOKIE_NAME, array());
        $result = explode(',', $result);

        foreach ($result as $idx => $value) {
            if (intval($value) == 0) {
                unset($result[$idx]);
            }
        }

        //remove current product
        if (Mage::registry('current_product') && in_array(Mage::registry('current_product')->getId(), $result)) {
            $productId = Mage::registry('current_product')->getId();
            foreach ($result as $key => $value) {
                if ($value == $productId) {
                    unset($result[$key]);
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * @return string
     */
    protected function _getIdentifier()
    {
        $productIds = $this->_getProductIds();
        if ($productIds) {
            return implode('_', $productIds);
        }

        return '';
    }

    /**
     * @return bool
     */
    public function loadCache()
    {
        return false;
    }
}
