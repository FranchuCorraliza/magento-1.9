<?php
class Elite_LayerNavigation_Model_Catalog_Layer_Filter_Item extends Mage_Catalog_Model_Layer_Filter_Item
{
	public function getCategoryUrl($catId){
		return Mage::getModel('catalog/category')->load($catId)->getUrl();
	}
	public function getDesignerUrl($designerId){
		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');
		$query = 'SELECT url_key FROM ' . $resource->getTableName('manufacturer/manufacturer').' WHERE option_id='.$designerId;
		$manufacturerUrlKey = $readConnection->fetchOne($query);
		$category=Mage::registry('current_category');
		$categoryUrlPath=$category->getUrlPath();
		return Mage::getUrl().$manufacturerUrlKey.'/'.$categoryUrlPath;
	}
	
	public function getSeoUrl()
    {
        
		$parametro=$this->getFilter()->getRequestVar();
		if ($parametro=='cat'){
			return $this->getCategoryUrl($this->getValue());
		}elseif ($parametro=='manufacturer'){
			return $this->getDesignerUrl($this->getValue());
		}else{
			$query = array(
				$this->getFilter()->getRequestVar()=>$this->getValue(),
				Mage::getBlockSingleton('page/html_pager')->getPageVarName() => null // exclude current page from urls
				);
			return Mage::getUrl('*/*/*', array('_current'=>true, '_use_rewrite'=>true, '_query'=>$query));
		}
    }
	
	public function getFiltros($filtros,$catId)
    {
		
		if ($catId){
			$filtros['cat'][]=$catId;		
		}else{
			$parametro=$this->getFilter()->getRequestVar();
			$filtros[$parametro][]=$this->getValue();
		}
		return $filtros;
	}
}
		