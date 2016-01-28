<?php 
class Elite_Urlrewrites_Model_Observer {
    public function categoryUrlrewrites($observer) {
    	
		//Al crear una nueva categoría creamos un redireccionamiento que redirige desde la categoría padre + filtro a esta nueva categoría
		
		$category = $observer->getEvent()->getCategory();
		$categoria_padre=$category->getParentCategory();
		$id_path='category/'.$categoria_padre->getId().'/'.$category->getId();
		$targetPath=$category->getUrlPath();
		$requestPath=$categoria_padre->getUrlPath().'?cat='.$category->getId();
		if (!(Mage::getModel('core/url_rewrite')->loadByIdPath($id_path)->getId())):
				Mage::getModel('core/url_rewrite')
					->setIsSystem(0)
					->setOptions('RP')
					->setIdPath($id_path)
					->setTargetPath($targetPath)
					->setRequestPath($requestPath)
					->save();
				
		endif;
		
    }
	
	
	
	
	public function productUrlrewrites($observer) {
		//Creamos una url que nos vincula Marca y categorias al guardar un producto
		$product = $observer->getEvent()->getProduct();
		$this->createProductRewrite($product);
		

	}
	
	public function massiveProductUrlrewrites($observer){
		//Creamos una url que nos vincula Marca y categorias de cada uno de los productos modificados masivamente.
		$productIds=$observer->getProductIds();
		$categories=array();
		foreach ($productIds as $productId):
			$product=Mage::getModel('catalog/product')->load($productId);
			$this->createProductRewrite($product);
		endforeach;
			
		
	}
	private function createProductRewrite($product){
		$attr = $product->getResource()->getAttribute("manufacturer");
			if ($attr->usesSource()) {
				$manufacturerId = $attr->getSource()->getOptionId($product->getAttributeText('manufacturer'));
			}
			$categories = $product->getCategoryIds();
		
			foreach ($categories as $categoryId):
				
				$this->createUrlPath($categoryId, $manufacturerId);
				
			endforeach;
	}
	
	private function createUrlPath($categoryId, $manufacturerId){
		if($manufacturerId!="")
		{
				$category=Mage::getModel("catalog/category")->load($categoryId);
			if ($category->getParentCategory()->getLevel()>1){
				$this->createUrlPath($category->getParentCategory()->getId(),$manufacturerId);
			}
			$resource = Mage::getSingleton('core/resource');
			$readConnection = $resource->getConnection('core_read');
			$query = 'SELECT url_key FROM ' . $resource->getTableName('manufacturer/manufacturer').' WHERE option_id='.$manufacturerId;
			$manufacturerUrlKey = $readConnection->fetchOne($query);
			$categoryUrlPath=$category->getUrlPath();
			$id_path="manufacturer/".$manufacturerId.'/'.$categoryId;
			$targetPath='catalog/category/view/id/'.$categoryId.'?manufacturer='.$manufacturerId.'&sc=1';
			$requestPath=$manufacturerUrlKey.'/'.$categoryUrlPath;
			if (!(Mage::getModel('core/url_rewrite')->loadByIdPath($id_path)->getId())): 
					Mage::getModel('core/url_rewrite')
						->setIsSystem(0)
						->setOptions()
						->setIdPath($id_path)
						->setTargetPath($targetPath)
						->setRequestPath($requestPath)
						->save();
					
			endif;
		}
		
	}
	
	
}