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
class Elite_Canonical_block_Canonical extends Mage_Page_Block_Html
{

    /**
     * @var string
     */
    public function getCanonicalTag(){
		$currentStoreId=Mage::app()->getStore()->getStoreId();
		if ($currentStoreId%2==0){
			$storeId=2;
		}else{
			$storeId=1;
		}
		
		if ($_product = Mage::registry('current_product')) {
			$url= $_product->getUrlKey().".html";
		}elseif ($_category = Mage::registry('current_category')) {
			if ($this->getRequest()->getParam("sc")){
				$manufacturerId=$this->getRequest()->getParam("manufacturer");
				$manufacturerUrlKey = Mage::getModel("manufacturer/manufacturer")->getUrlKeyByOptionId($manufacturerId);
				$url= $manufacturerUrlKey."/".$_category->getUrlPath();
			}else{
				$url= $_category->getUrlPath();
			}
		}else{
			$url=$this->getUrl('*/*/*', array('_use_rewrite' => true, '_forced_secure' => true));
			$baseUrl=$this->getBaseUrl();
			$url=str_replace($baseUrl,'',$url);
		}
		
		return '<link rel="canonical" href="'.Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK) . $url . '" />';
	}

}