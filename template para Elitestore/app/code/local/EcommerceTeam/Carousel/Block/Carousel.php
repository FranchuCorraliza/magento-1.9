<?php
/**
 * Products Carousel - Magento Extension
 *
 * @package:     ProductsCarousel
 * @category:    EcommerceTeam
 * @copyright:   Copyright 2012 EcommerceTeam Inc. (http://www.ecommerce-team.com)
 * @version:     1.0.0
 */
    
class EcommerceTeam_Carousel_Block_Carousel
    extends Mage_Core_Block_Template{

    protected $collection;
    protected $productBlock;

    public function getProductCollection()
    {
        if (is_null($this->collection)) {
            $collection = Mage::getResourceModel('catalog/product_collection');
            Mage::getSingleton('catalog/layer')->prepareProductCollection($collection);

            $collection
                ->addAttributeToSelect('small_image')
                ->addAttributeToFilter('home_pic', true);
			
			Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection( $collection );
            $this->collection = $collection;
			
        }

        return $this->collection;
    }

    public function getProductHtml(Mage_Catalog_Model_Product $product, $position = false)
    {
        if (is_null($this->productBlock)) {
            $this->productBlock = $this->getLayout()->createBlock('ecommerceteam_carousel/carousel_product')->setTemplate('ecommerceteam/carousel/product.phtml');
        }
        return $this->productBlock->setProduct($product)->toHtml();
    }
}
