<?php
class Yoast_Filter_Block_Result extends Mage_Catalog_Block_Product_List
{
  
  
  protected function _getProductCollection()
  {
    if (is_null($this->_productCollection)) {
      $collection = Mage::getResourceModel('catalog/product_collection');
      Mage::getSingleton('catalog/layer')->prepareProductCollection($collection);
      
		
    	if ($this->getValue()){
    		$value = $this->getValue();
    	} else{
    		$value = $this->getRequest()->getParam('filterValue', 0);
    	}
    
    	if ($this->getCategory())
    	{
    		$categoryId = $this->getCategory();	
    	} 
    	else
    	{
    		 $categoryId = $this->getRequest()->getParam('filterCategory', 0);
    	}

    	if ($this->getAttributeName()){
    		$attribute = $this->getAttributeName();		
    	} else {
    		 $attribute = 'color' ;
    	}
		
		if ($attribute == 'rebajas'){
			$todayDate  = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
	        $tomorrow = mktime(0, 0, 0, date('m'), date('d')+1, date('y'));
    	    $dateTomorrow = date('m/d/y', $tomorrow);
			$collection->addFinalPrice()->getSelect()->where('price_index.final_price < price_index.price');	
/*			$collection->addAttributeToFilter('special_price', array('gt' => 0))
			->addAttributeToFilter('special_from_date', array('date' => true, 'to' => $todayDate)) 
        	->addAttributeToFilter('special_to_date', array('or'=> array(
				0 => array('date' => true, 'from' => $dateTomorrow), 
				1 => array('is' => new Zend_Db_Expr('null')))), 'left');
 */	
//			$collection->addAttributeToFilter('special_price', array('gt' => 0));
//			->addAttributeToFilter('special_to_date',array('date'=> true, 'from' => $todayDate));

//			$collection->addAttributeToFilter('special_price', array('gt' => 0));
//            $collection->addAttributeToFilter('special_to_date', array('date' => true, 'to' => $todayDate));
		} elseif ($attribute == 'extra20womensale'){
			$catIds=array(23,59,61,24,45,206,104,116,158);
			$allIds=array();
			foreach ($catIds as $catId){
				$collection1== clone $collection;
				$cat = Mage::getModel('catalog/category')->load($catId);
		        $collection1->addCategoryFilter($cat, true);
				if (!$allIds):	
					$allIds=$collection1->getAllIds();
					
				else:
					$allIds=array_merge($allIds,$collection1->getAllIds());
				endif;
			}
			$allIds_unique=array_unique($allIds);
			$collection->addFieldToFilter('entity_id', array('in' => $allIds_unique));
			
//			$todayDate  = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
//	        $tomorrow = mktime(0, 0, 0, date('m'), date('d')+1, date('y'));
//    	    $dateTomorrow = date('m/d/y', $tomorrow);
//			$collection->addFinalPrice()->getSelect()->where('price_index.final_price < price_index.price');
			
		} elseif ($attribute =='newin'){
			$collection->addAttributeToFilter(array(
        array('attribute'=> 'tipo','like' => '%'.$value.'%'),
        array('attribute'=> 'tipo','like' => '1000')));
		}else{
    		$collection->addAttributeToFilter($attribute, array('like' => $value));
		}
		
		$collection->addAttributeToSelect('attribute_set_ids');
    	
      $_filters = Mage::getSingleton('Yoast_Filter/Layer')->getState()->getFilters();
      foreach ($_filters as $_filter) {
        if($_filter->getFilter()->getRequestVar() == "price") {
          $arr = $_filter->getValue();
          $max_value = $arr[0] * $arr[1];
          $min_value = $max_value - $arr[1];
          
          $collection->addAttributeToFilter($_filter->getFilter()->getRequestVar(), array('gt' => $min_value))
                     ->addAttributeToFilter($_filter->getFilter()->getRequestVar(), array('lt' => $max_value));
        } else if($_filter->getFilter()->getRequestVar() == "cat") {    
          $category = Mage::getModel('catalog/category')->load($_filter->getValue());               
          $collection->addCategoryFilter($category, true);
        } else if($_filter->getFilter()->getRequestVar() == "talla") { /* Para filtrar por tallas generamos una consulta que devuelve los ids de los items configurables que tienen algún hijo con stock y talla igual a la talla filtrada. Despues filtramos la colección con estos items*/
			
			$resource = Mage::getSingleton('core/resource');
			$readConnection = $resource->getConnection('core_read');
			$query = 'SELECT 1 AS `status`, `e`.`entity_id`, `e`.`type_id`, `e`.`attribute_set_id`, `stock_table`.`product_id`, `stock_table`.`is_in_stock`, `link_table`.`product_id`, `link_table`.`parent_id` FROM `catalog_product_flat_2` AS `e` LEFT JOIN `cataloginventory_stock_item` AS `stock_table` ON stock_table.product_id = e.entity_id LEFT JOIN `catalog_product_super_link` AS `link_table` ON link_table.product_id = e.entity_id WHERE ((`e`.`talla` LIKE \''.$_filter->getValue().'\')) AND ((`stock_table`.`is_in_stock` = 1)) GROUP BY `link_table`.`parent_id`';
			$results = $readConnection->fetchAll($query);
			$articulos= array();
			foreach ($results as $item):
				$padre=$item['parent_id'];
				if (!in_array($articulos,$padre)):
					$articulos[]=$item['parent_id'];
				endif;	
			endforeach;
			$collection->addAttributeToFilter('entity_id', array('in' => $articulos));
		} else {
		  	$collection->addAttributeToFilter($_filter->getFilter()->getRequestVar(), $_filter->getValue());
        }
        
      }  	
      
	    if ($categoryId) {
        $category = Mage::getModel('catalog/category')->load($categoryId);
        $collection->addCategoryFilter($category, true);
      }
 	  /*$collection->addAttributeToSort('prioridad', 'desc')
                    ->addAttributeToSort('created_at', 'desc');	*/
      $this->_productCollection = $collection;
      Mage::getSingleton('Yoast_Filter/Layer')->setProductCollection($this->_productCollection);
    }

    return $this->_productCollection;
  }
}