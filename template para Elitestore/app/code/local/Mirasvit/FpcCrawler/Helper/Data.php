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



class Mirasvit_FpcCrawler_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getOrderSql($orderByAttribute = false, $logged = null)
    {
        $order = array();
        $actions = array();
        $pageActions = $logged ? Mage::getSingleton('fpccrawler/config')->getSortByPageType(true) : Mage::getSingleton('fpccrawler/config')->getSortByPageType();
        foreach ($pageActions as $action) {
            $actions[] = "'".$action->getActionOption()."'";
        }
        krsort($actions);
        $order[] = new Zend_Db_Expr('FIELD(sort_by_page_type, '.implode(',', $actions).') desc');
        if ($orderByAttribute) {
            $order[] = 'sort_by_product_attribute asc';
        }
        $order[] = 'rate desc';

        return $order;
    }

    public function setVariable($key, $value)
    {
        $variable = Mage::getModel('core/variable');
        $variable = $variable->loadByCode('fpc_'.$key);

        $variable->setPlainValue($value)
            ->setHtmlValue(Mage::getSingleton('core/date')->gmtTimestamp())
            ->setName($key)
            ->setCode('fpc_'.$key)
            ->save();

        return $variable;
    }

    public function getVariable($key)
    {
        $variable = Mage::getModel('core/variable')->loadByCode('fpc_'.$key);

        return $variable->getPlainValue();
    }

    public function getUserAgent($customerGroupId, $storeId, $currency)
    {
        if ($customerGroupId) {
            $userAgent = Mirasvit_FpcCrawler_Model_Crawlerlogged_Crawl::USER_AGENT;
            $userAgent .= Mirasvit_FpcCrawler_Model_Config::USER_AGENT_BEGIN_LABEL . $customerGroupId . Mirasvit_FpcCrawler_Model_Config::USER_AGENT_END_LABEL;
        } else {
            $userAgent = Mirasvit_FpcCrawler_Model_Crawler_Crawl::USER_AGENT;
        }
        $userAgent .= Mirasvit_FpcCrawler_Model_Config::STORE_ID_BEGIN_LABEL . $storeId . Mirasvit_FpcCrawler_Model_Config::STORE_ID_END_LABEL;
        $userAgent .= Mirasvit_FpcCrawler_Model_Config::CURRENCY_BEGIN_LABEL . $currency . Mirasvit_FpcCrawler_Model_Config::CURRENCY_END_LABEL;

        return $userAgent;
    }
}
