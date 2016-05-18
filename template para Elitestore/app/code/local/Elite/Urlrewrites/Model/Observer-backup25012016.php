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
		
		$product = $observer->getEvent()->getProduct();
		$manufacturerName= $product->getAttributeText('manufacturer');
		$manufacturers= Mage::getModel("manufacturer/manufacturer")->getManufacturers('');
		foreach ($manufacturers as $manufacturer):
			if ($manufacturer->getName()==$manufacturerName):
				$manufacturerUrlKey=$manufacturer->getData('url_key');
				$manufacturerId=$manufacturer->getId();
				break;
			endif;
		endforeach;
		$categories = $product->getCategoryIds();
		foreach ($categories as $categoryId):
			$category=Mage::getModel("catalog/category")->load($categoryId);
			$categoryUrlPath=$category->getUrlPath();
			$id_path="manufacturer/".$manufacturerId.'/'.$categoryId;
			$targetPath='catalog/category/view/id/10?manufacturer=413&sc=1manufacturer/index/list/id/'.$manufacturerId.'?cat='.$categoryId;
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
		endforeach;
		//$id_path='manufacturer/'.$categoria_padre->getId().'/'.$category->getId();
		//$targetPath=$category->getUrlPath();
		//$requestPath=$categoria_padre->getUrlPath().'?cat='.$category->getId();
		

	}
}