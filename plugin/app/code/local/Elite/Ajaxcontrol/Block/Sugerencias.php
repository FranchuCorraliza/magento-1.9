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
class Elite_Ajaxcontrol_Block_Sugerencias extends Mage_Core_Block_Template
{
	public function getProductosRecientes()
	{
		return Mage::getBlockSingleton('reports/product_viewed')->getItemsCollection();
	}
	
    public function getProductosSugeridos($sku)
    {
		// OJO Cambiar listado de categorias cuando se actualicen las categorías correctas
		$categoriasAdmitidas=array(532, 536, 557, 558, 559, 560, 561, 562, 537, 538, 539, 563, 564, 565, 566, 567, 568, 569, 540, 541, 570, 571, 572, 573, 533, 542, 543, 574, 575, 576, 544, 545, 546, 577, 578, 579, 534, 547, 548, 549, 550, 551, 552, 535, 553, 580, 581, 582, 583, 596, 597, 598, 599, 600, 584, 585, 586, 587, 554, 588, 589, 590, 591, 592, 555, 556, 593, 594, 595,15, 16, 19, 18, 17, 117, 20, 118, 119, 120, 121, 122, 123, 89, 124, 125, 126, 127, 128, 90, 101, 102, 103, 104, 105, 106, 107, 108, 91, 92, 93, 94, 95, 96, 97, 98, 99, 100, 65, 87, 66, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 67, 71, 68, 69, 70, 24, 28, 29, 25, 48, 49, 50, 51, 52, 26, 56, 57, 58, 59, 60, 27, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45);
		
		
		$storeId = Mage::app()->getStore()->getStoreId();
		$retorno = "";
		$_product = Mage::getModel('catalog/product')->setStoreId($storeId)->loadByAttribute('sku', $sku);
		
		$productSeason= $_product->getAttributeText('season');
		$categorias = $_product->getCategoryIds();
		
		$categoriasAptas = array_intersect($categorias,$categoriasAdmitidas);
		
		$categoriasBuscadas=array();
		
		foreach ($categoriasAptas as $categoriaId):
			$categoria= Mage::getModel('catalog/category')->load($categoriaId);
			$padre=$categoria->getParentCategory();
			$categoriasBuscadas=array_merge(explode(',',$padre->getChildren()),$categoriasBuscadas);
		endforeach;
		$loqueseatio = array();
		foreach($categoriasBuscadas as $id):
			$loqueseatio[] = array('attribute'=>'category_id', "finset" => $id);
		endforeach;
		$productCollection = Mage::getResourceModel('catalog/product_collection')
								->addAttributeToSelect("*");
		if ($productSeason!=''){
								$productCollection ->addAttributeToFilter('tipo', array ('eq' => $productSeason));
		}
		$productCollection ->addAttributeToFilter('status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED))
								->addAttributeToFilter('entity_id', array ('neq' => $_product->getId()))
								->joinField('category_id','catalog/category_product','category_id','product_id=entity_id',null,'left');
								
		$productCollection->addAttributeToFilter($loqueseatio);
		
								
		$productCollection->getSelect()->group('entity_id');
		
		Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($productCollection);
		Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($productCollection);
		$productosHermanos= array();
		$productosPrimos=array();
		foreach ($productCollection as $product):
			$producto= Mage::getModel('catalog/product')->load($product->getId());
			if (count(array_intersect($categoriasAptas,$producto->getCategories()))):
				$productosHermanos[]=$producto;
			else:
				$productosPrimos[]=$producto;
			endif;
		endforeach;
		
		$productosSugeridos=$productosHermanos;
		$i=0;
		while(isset($productosPrimos[$i]) && count($productosSugeridos)<5):
			$productosSugeridos[]=$productosPrimos[$i];
			$i++;
		endwhile;
		
        return $productosSugeridos;
    }

	public function getProductosSugeridosCheckuout()
    {
		// OJO Cambiar listado de categorias cuando se actualicen las categorías correctas
		
		$productCollection = Mage::getResourceModel('catalog/product_collection')
                        ->addAttributeToSelect("*")
						->addAttributeToFilter('checkout_pic', true)
						->addAttributeToFilter('type_id', 'configurable')
                        ->addAttributeToFilter('status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED));

		Mage::getSingleton('cataloginventory/stock')
    ->addInStockFilterToCollection($productCollection);
		
		return $productCollection;
    }



}