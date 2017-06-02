<?php

class Elite_Canonical_block_Alternate extends Mage_Page_Block_Html
{

    /**
     * @var string
     */
    public function getAlternateTag(){
		$currentStoreId= Mage::app()->getStore()->getStoreId();
		if ($currentStoreId%2==0){
			$storeId=$currentStoreId-1;
			$lang='en';
		}else{
			$storeId=$currentStoreId+1;
			$lang='es';
		}
		
		if ($_product = Mage::registry('current_product')) {
			$url= $_product->getUrlKey().".html";
		}elseif ($_category = Mage::registry('current_category')) {
			$_category2= Mage::getModel('catalog/category')->setStoreId($storeId)->load($_category->getId());
			if ($this->getRequest()->getParam("sc")){
				$manufacturerId=$this->getRequest()->getParam("manufacturer");
				$manufacturerUrlKey = Mage::getModel("manufacturer/manufacturer")->getUrlKeyByOptionId($manufacturerId);
				$url= $manufacturerUrlKey."/".$_category2->getUrlPath();
			}else{
				
				$url= $_category2->getUrlPath();
			}
		}else{
			$url=$this->getUrl('*/*/*', array('_use_rewrite' => true, '_forced_secure' => true));
			$baseUrl=$this->getBaseUrl();
			$url=str_replace($baseUrl,'',$url);
		}
		
		return '<link rel="alternate" hreflang="'.$lang.'" href="'.Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK) . $url . '" />';
	}
}