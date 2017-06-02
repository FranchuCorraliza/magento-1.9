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



/**
 * The observer responsible for the customer/admin action,
 * which require cache clear.
 */
class Mirasvit_Fpc_Model_Observermf_Cachemf
{
    /**
     * @var Mirasvit_Fpc_Model_Configmf
     */
    protected $_config = null;

    /**
     * @var array
     */
    protected $_tags2Clean = array();

    public function __construct()
    {
        $this->_config = Mage::getSingleton('fpc/configmf');
    }

    public function onReviewSaveAfter($observer)
    {
        $review = $observer->getObject();

        $this->_addTag2Clean('CATALOG_PRODUCT_'.$review->getEntityPkValue());

        return $this;
    }

    public function onQuoteSubmitSuccess($observer)
    {
        $quote = $observer->getEvent()->getQuote();
        foreach ($quote->getAllItems() as $item) {
            $this->_addTag2Clean('CATALOG_PRODUCT_'.$item->getProductId());

            $children = $item->getChildrenItems();
            if ($children) {
                foreach ($children as $child) {
                    $this->_addTag2Clean('CATALOG_PRODUCT_'.$child->getProductId());
                }
            }
        }

        return $this;
    }

    public function onCataloginventoryStockItemSave($observer)
    {
        if ($observer->getDataObject() && $observer->getDataObject()->getProductId()) {
            $productId = $observer->getDataObject()->getProductId();

            $this->_addTag2Clean('CATALOG_PRODUCT_'.$productId);
        }

        return $this;
    }

    public function cleanCache($observer)
    {
        if ($observer->getEvent()->getType() == 'fpc') {
            Mirasvit_Fpc_Model_Cachemf::getCacheInstance()->clean(Mirasvit_Fpc_Model_Configmf::CACHE_TAG);
        }

        return $this;
    }

    /**
     * Before send response, we clear cache for all registerd tags.
     * Prevent situation with caching page with old data (during transaction).
     */
    public function onHttpResponseSendBefore($observer)
    {
        $this->_tags2Clean = array_unique($this->_tags2Clean);

        if (count($this->_tags2Clean)) {
            Mirasvit_Fpc_Model_Cachemf::getCacheInstance()->clean($this->_tags2Clean);
        }

        return $this;
    }

    /**
     * @param string $tag
     * @return void
     */
    protected function _addTag2Clean($tag)
    {
        $this->_tags2Clean[] = $tag;

        return $this;
    }
}
