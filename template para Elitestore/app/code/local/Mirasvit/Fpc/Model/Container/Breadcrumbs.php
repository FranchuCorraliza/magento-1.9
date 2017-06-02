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



class Mirasvit_Fpc_Model_Container_Breadcrumbs extends Mirasvit_Fpc_Model_Container_Abstract
{
    protected $_placeholderBlock;
    protected $_currentCategory;
    protected $_categoryPath;

    /**
     * Get cache identifier.
     *
     * @return string
     */
    protected function _getCacheId()
    {
        $categoryId = $this->_getCategoryId();
        $productId = $this->_getProductId();

        if ($categoryId || $productId) {
            $cacheSubKey = '_'.$categoryId.'_'.$productId;
        } else {
            $cacheSubKey = null;
        }

        return 'CONTAINER_BREADCRUMBS_'.md5($cacheSubKey);
    }

    public function addCrumb($crumbName, $crumbInfo, $after = false)
    {
        $this->_prepareArray($crumbInfo, array('label', 'title', 'link', 'first', 'last', 'readonly'));

        return $this;
    }

    /**
     * Set required array elements.
     *
     * @param array $arr
     * @param array $elements
     *
     * @return array
     */
    protected function _prepareArray(&$arr, array $elements = array())
    {
        foreach ($elements as $element) {
            if (!isset($arr[$element])) {
                $arr[$element] = null;
            }
        }

        return $arr;
    }

    public function applyToContent(&$content, $withoutBlockUpdate = false)
    {
        $startTime = microtime(true);
        $pattern = '/'.preg_quote($this->_getStartReplacerTag(), '/').'(.*?)'.preg_quote($this->_getEndReplacerTag(), '/').'/ims';
        $html = $this->_renderBlock();

        if ($html === null) {
            return true;
        } elseif ($html !== false) {
            ini_set('pcre.backtrack_limit', 100000000);
            Mage::helper('fpc/debug')->appendDebugInformationToBlock($html, $this, 0, $startTime);
            $content = preg_replace($pattern, str_replace('$', '\\$', $html), $content, 1);

            return true;
        } else {
            return false;
        }
    }

    protected function _renderBlock()
    {
        $categoryId = $this->_getCategoryId();
        $productId = $this->_getProductId();

        //No need breadcrumbs on CMS pages
        if (!$productId && !$categoryId) {
            return null;
        }

        //cookie
        if (!$productId && $categoryId) {
            Mage::getModel('core/cookie')->set('mfpcbreadcrumb', Mage::registry('current_category_id'), time() + 86400, '/');
        }
        if ($productId && $categoryId) {
            if (isset($_COOKIE['mfpcbreadcrumb'])) {
                Mage::getModel('core/cookie')->delete('mfpcbreadcrumb');
            }
        }

        $breadcrumbsBlock = $this->_getPlaceHolderBlock();
        $breadcrumbsBlock->setNameInLayout('breadcrumbs');
        $breadcrumbsBlock->addCrumb('home', array(
                'label' => Mage::helper('catalog')->__('Home'),
                'title' => Mage::helper('catalog')->__('Go to Home Page'),
                'link' => Mage::getBaseUrl(),
            ));
        if ($productId && $categoryId) {
            $path = $this->_getBreadcrumbPath($categoryId);
        } else {
            $path = Mage::helper('catalog')->getBreadcrumbPath();
        }

        foreach ($path as $name => $breadcrumb) {
            $breadcrumbsBlock->addCrumb($name, $breadcrumb);
        }

        return $breadcrumbsBlock->toHtml();
    }

    protected function _getBreadcrumbPath($categoryId)
    {
        if (!$this->_categoryPath) {
            $path = array();
            if ($category = $this->_getCategory($categoryId)) {
                $pathInStore = $category->getPathInStore();
                $pathIds = array_reverse(explode(',', $pathInStore));

                $categories = $category->getParentCategories();

                // add category path breadcrumb
                foreach ($pathIds as $categoryId) {
                    if (isset($categories[$categoryId]) && $categories[$categoryId]->getName()) {
                        $path['category'.$categoryId] = array(
                            'label' => $categories[$categoryId]->getName(),
                            'link' => $this->_isCategoryLink($categoryId) ? $categories[$categoryId]->getUrl() : '',
                        );
                    }
                }
            }

            if ($this->_getProduct()) {
                $path['product'] = array('label' => $this->_getProduct()->getName());
            }

            $this->_categoryPath = $path;
        }

        return $this->_categoryPath;
    }

    protected function _getCategory($categoryId)
    {
        if (!$this->_currentCategory) {
            $this->_currentCategory = Mage::getModel('catalog/category')->setStoreId(Mage::app()->getStore()->getStoreId())->load($categoryId);
        }

        return $this->_currentCategory;
    }

    protected function _getProduct()
    {
        return Mage::registry('current_product');
    }

    protected function _isCategoryLink($categoryId)
    {
        if ($this->_getProduct()) {
            return true;
        }
        if ($categoryId != $this->_getCategory($categoryId)->getId()) {
            return true;
        }

        return false;
    }

    protected function _getPlaceHolderBlock()
    {
        if (null === $this->_placeholderBlock) {
            if (Mage::helper('mstcore')->isModuleInstalled('Mirasvit_Seo')) {
                $blockName = 'Mirasvit_Seo_Block_Breadcrumbs';
                $blockTemplate = 'seo/breadcrumbs.phtml';
            } else {
                $blockName = 'Mage_Page_Block_Html_Breadcrumbs';
                $blockTemplate = 'page/html/breadcrumbs.phtml';
            }
            $this->_placeholderBlock = new $blockName();
            $this->_placeholderBlock->setTemplate($blockTemplate);
            $this->_placeholderBlock->setLayout(Mage::app()->getLayout());
            $this->_placeholderBlock->setSkipRenderTag(true);
        }

        return $this->_placeholderBlock;
    }

    protected function _getCategoryId()
    {
        $categoryId = false;

        if ($this->_getProductId() && isset($_SERVER['HTTP_REFERER'])) {
            if ($referer = $_SERVER['HTTP_REFERER']) {
                $referer = str_replace('https://', 'http://', $referer);
                $path = str_replace(Mage::getBaseUrl(), '', $referer);
                $rewrite = Mage::getModel('core/url_rewrite')->setStoreId(Mage::app()->getStore()->getStoreId())->loadByRequestPath($path);
                if ($rewrite && $rewrite->getTargetPath()) {
                    if (strpos($rewrite->getTargetPath(), 'catalog/category/view/id/') !== false) {
                        preg_match('/\d{1,9}/', $rewrite->getTargetPath(), $matches);
                        if (isset($matches[0])) {
                            $categoryId = $matches[0];

                            return $categoryId;
                        }
                    }
                }
            }
        }

        if (Mage::registry('current_category')) {
            $categoryId = Mage::registry('current_category')->getId();
        }

        if (!$categoryId && Mage::registry('current_category_id')) {
            $categoryId = Mage::registry('current_category_id');
        }

        if (!$categoryId && $this->_getProductId()) {
            if (Mage::registry('current_product')) {
                $product = Mage::registry('current_product');
            } else {
                $product = Mage::getModel('catalog/product')->setStore(Mage::app()->getStore()->getStoreId())->load($this->_getProductId());
            }

            $categoryIds = $product->getCategoryIds();
            $rootCategoryId = Mage::app()->getStore()->getRootCategoryId();
            array_unshift($categoryIds, $rootCategoryId);
            $categoryIds = array_reverse($categoryIds);
            if (isset($categoryIds[0])) {
                $categoryId = $categoryIds[0];
            }
        }

        if ($this->_getProductId() && !$categoryId) {
            //cookie
            if ($categoryId = Mage::getModel('core/cookie')->get('mfpcbreadcrumb')) {
                return $categoryId;
            }
        }

        return $categoryId;
    }

    protected function _getProductId()
    {
        $productId = false;

        if (Mage::registry('current_product')) {
            $productId = Mage::registry('current_product')->getId();
        }

        if (!$productId && Mage::registry('current_product_id')) {
            $productId = Mage::registry('current_product_id');
        }

        return $productId;
    }
}
