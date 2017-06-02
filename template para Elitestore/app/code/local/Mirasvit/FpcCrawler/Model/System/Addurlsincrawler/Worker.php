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


class Mirasvit_FpcCrawler_Model_System_Addurlsincrawler_Worker extends Varien_Object
{
    protected $maxPerStep = 150;
    protected $totalNumber;
    protected $isEnterprise = false;
    protected $cacheId = 'FPC_REQUEST_';
    protected $categoryActionType = 'catalog/category_view';
    protected $productActionType = 'catalog/product_view';
    protected $crawlCustomerGroupIds = null;

    public function run() {
        set_time_limit(0);
        $this->totalNumber = $this->getTotalUrlsNumber();
        if (($this->getStep()-1)*$this->maxPerStep >= $this->totalNumber) {
            return false;
        }
        $this->process();
        return true;
    }

    protected function getTotalUrlsNumber()
    {
        $totalUrlsNumber = 0;
        foreach ($this->getAllStoresIds() as $storeId) {
            $productCollection = $this->getProductCollection($storeId);
            $totalUrlsNumber += $productCollection->getSize();

            $rootCategoryId = Mage::app()->getStore($storeId)->getRootCategoryId();
            $categoryCollection = $this->getCategoryCollection($storeId);
            $totalUrlsNumber += $categoryCollection->getSize();
        }

        return $totalUrlsNumber;
    }

    protected function getCategoryCollection($storeId) {
        $rootCategoryId = Mage::app()->getStore($storeId)->getRootCategoryId();
        $categoryCollection = Mage::getModel('catalog/category')
                                ->getCollection()
                                ->setStoreId($storeId)
                                ->addFieldToFilter('is_active', 1)
                                ->addAttributeToFilter('path', array('like' => "1/{$rootCategoryId}/%"))
                                ->addAttributeToSelect('*')
                                ;

        return $categoryCollection;
    }

    protected function getProductCollection($storeId) {
        $productCollection = Mage::getModel('catalog/product')->getCollection()
                                ->addAttributeToSelect('*')
                                ->addStoreFilter($storeId)
                                ->addAttributeToFilter('status', 1)
                                ->addAttributeToFilter('visibility', array(Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG, Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH))
                                ->addUrlRewrite()
                                ;

        return $productCollection;
    }

    public function getMaxPerStep() {
        return $this->maxPerStep;
    }

    public function getCurrentNumber() {
        $c = $this->getStep() * $this->getMaxPerStep();
        if ($c > $this->totalNumber) {
            return $this->totalNumber;
        } else {
            return $c;
        }
    }

    public function getTotalNumber() {
        return $this->totalNumber;
    }

    protected function getAllStoresIds()
    {
        $storeIds = array();
        $allStores = Mage::app()->getStores();
        foreach ($allStores as $storeId => $store)
        {
            // pr(get_class_methods($store)); die;
            if ($store->getIsActive()) {
                $storeIds[] = Mage::app()->getStore($storeId)->getId();
            }
        }
        return $storeIds;
    }

    public function process() {

        foreach ($this->getAllStoresIds() as $storeId) {
            //category
            $categoryCollection = $this->getCategoryCollection($storeId);
            $categoryCollection = $categoryCollection
                                    ->setCurPage($this->getStep())
                                    ->setPageSize($this->maxPerStep);
            foreach ($categoryCollection as $item) {
                $this->addCategoryUrlInCrawler($item, $storeId);
            }

            //product
            $productCollection = $this->getProductCollection($storeId);
            $productCollection = $productCollection
                                    ->setCurPage($this->getStep())
                                    ->setPageSize($this->maxPerStep);
            foreach ($productCollection as $item) {
                $this->addProductUrlInCrawler($this->getProductUrl($item->getId(), $item->getCategoryIds(), $storeId), $storeId);
            }
        }
    }

    protected function addCategoryUrlInCrawler($item, $storeId)
    {
        $url = Mage::app()->getStore($storeId)->getBaseUrl() . $item->getUrlPath();
        $line = '||' . trim($url) . '|' . $this->cacheId . '|' . $this->categoryActionType . '|1000' . '||' . $storeId . '|' . $this->getStoreCurrency($storeId) . '|' . Mirasvit_FpcCrawler_Model_Config::COMPUTER_GROUP;
        $line = explode('|', $line);
        Mage::getSingleton('fpccrawler/crawler_url')->saveUrl($line, 0);
        foreach ($this->getCrawlCustomerGroupIds() as $customerGroupId) {
            $line = '||' . trim($url) . '|' . $this->cacheId . '|' . $this->categoryActionType . '|1000' . '|' . $customerGroupId . '|' . $storeId . '|' . $this->getStoreCurrency($storeId) . '|' . Mirasvit_FpcCrawler_Model_Config::COMPUTER_GROUP;
            $line = explode('|', $line);
            Mage::getSingleton('fpccrawler/crawlerlogged_url')->saveUrl($line, 0);
        }

    }

    protected function addProductUrlInCrawler($urls, $storeId)
    {
        foreach ($urls as $url) {
            if ($url) {
                $url = Mage::app()->getStore($storeId)->getBaseUrl() . $url;
                $line = '||' . trim($url) . '|' . $this->cacheId . '|' . $this->productActionType . '|1000' . '||' . $storeId . '|' . $this->getStoreCurrency($storeId) . '|' . Mirasvit_FpcCrawler_Model_Config::COMPUTER_GROUP;
                $line = explode('|', $line);
                Mage::getSingleton('fpccrawler/crawler_url')->saveUrl($line, 0);
                foreach ($this->getCrawlCustomerGroupIds() as $customerGroupId) {
                    $line = '||' . trim($url) . '|' . $this->cacheId . '|' . $this->productActionType . '|1000' . '|' . $customerGroupId . '|' . $storeId . '|' . $this->getStoreCurrency($storeId) . '|' . Mirasvit_FpcCrawler_Model_Config::COMPUTER_GROUP;
                    $line = explode('|', $line);
                    Mage::getSingleton('fpccrawler/crawlerlogged_url')->saveUrl($line, 0);
                }
            }
        }
    }

    protected function getStoreCurrency($storeId)
    {
        return Mage::app()->getStore($storeId)->getCurrentCurrencyCode();
    }

    protected function getProductUrl($productId, $categoryIds, $storeId)
    {
        $urls = array();
        $idPath = sprintf('product/%d', $productId);
        $urls[] = $this->getUrlByIdPath($idPath, $storeId);
        foreach ($categoryIds as $categoryId) {
            if ($categoryId) {
                $idPath = sprintf('%s/%d', $idPath, $categoryId);
                $urls[] = $this->getUrlByIdPath($idPath, $storeId);
            }
        }

        return $urls;
    }

    protected function getUrlByIdPath($idPath, $storeId)
    {
        $url = false;
        $urlRewriteObject = Mage::getModel('core/url_rewrite')->setStoreId($storeId)->loadByIdPath($idPath);
        if ($urlRewriteObject) {
            $url = $urlRewriteObject->getRequestPath();
        }

        return $url;
    }

    protected function getCrawlCustomerGroupIds() {
        if (!$this->crawlCustomerGroupIds) {
            $this->crawlCustomerGroupIds = Mage::getSingleton('fpccrawler/config')->getCrawlCustomerGroupIds();
        }

        return $this->crawlCustomerGroupIds;
    }
}