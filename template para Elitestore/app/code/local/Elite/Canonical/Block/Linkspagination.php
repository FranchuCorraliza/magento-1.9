<?php

/**
 * @author Marcelo Jacobus <marcelo.jacobus@gmail.com>
 *
 *
 * This block name is helloworld/helloWorld, as per the module config.xml file
 * under session global.blocks.<i>helloworld</i>.class
 *
 * In order to override the rendering of this class, the protected method
 * _toHtml() should be overriden.
 */
class Elite_Canonical_block_Linkspagination extends Mage_Page_Block_Html
{

    /**
     * @var string
     */
    public function getPaginationTag(){
		$link="";
		
		$storeId = Mage::app()->getStore()->getStoreId();
		if ($storeId==1 || $storeId==2){  // Solo insertamos los links para la store global
			$currentCategory=Mage::registry('current_category');
			if ($currentCategory){ // Comprobamos si estamos en una categoría
				$prodCol = $currentCategory->getProductCollection()->addAttributeToFilter('status', 1)->addAttributeToFilter('visibility', array('in' => array(Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG, Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)));
				if($this->getRequest()->getParam('sc') && $this->getRequest()->getParam('manufacturer')){ //Comprobamos si estamos en una categoría de diseñador
					$prodCol->addAttributeToFilter('manufacturer',array('eq' => $this->getRequest()->getParam('manufacturer')));
				}
				Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($prodCol);
				$tool = $this->getLayout()->createBlock('page/html_pager')->setLimit($this->getLayout()->createBlock('catalog/product_list_toolbar')->getLimit())->setCollection($prodCol);
				
				$linkPrev = false;
				$linkNext = false;
				if ($tool->getCollection()->getSelectCountSql()) {
					if ($tool->getLastPageNum() > 1) {
						if (!$tool->isFirstPage()) {
							$linkPrev = true;
							if ($tool->getCurrentPage() == 2) {
								$url = explode('?', $tool->getPreviousPageUrl());
								$prevUrl = @$url[0];
							}
							else {
								$prevUrl = $tool->getPreviousPageUrl();
							}
						}
						if (!$tool->isLastPage()) {
							$linkNext = true;
							$nextUrl = $tool->getNextPageUrl();
						}
					}
				}
				if ($linkPrev) $link.= '<link rel="prev" href="' . $prevUrl . '" />';
				if ($linkNext) $link.= '<link rel="next" href="' . $nextUrl . '" />';
			}
		}
		
		return $link;
	}

}