<?php
class Elite_LayerNavigation_Model_Catalog_Layer_Filter_Category extends Mage_Catalog_Model_Layer_Filter_Category
{
	protected function _getItemsData()
    {
		$key = $this->getLayer()->getStateKey().'_SUBCATEGORIES';
        $data = $this->getLayer()->getAggregator()->getCacheData($key);

        if ($data === null) {
            $categoty   = $this->getCategory();
            /** @var $categoty Mage_Catalog_Model_Categeory */
            $categories = $categoty->getChildrenCategories();
			$this->getLayer()->getProductCollection()
                ->addCountToCategories($categories);
			
            $data = array();
            foreach ($categories as $category) {
                if ($category->getIsActive()) {
					$catId=$category->getId();
                    $data[] = array(
                        'label' => Mage::helper('core')->escapeHtml($category->getName()),
                        'value' => $category->getId(),
                        'count' => $category->getProductCount(),
						'children' =>$this->_getChildrenItemsData($category->getId())
                    );
                }
            }
            $tags = $this->getLayer()->getStateTags();
            $this->getLayer()->getAggregator()->saveCacheData($data, $key, $tags);
        }
		return $data;
    }
	
	protected function _getChildrenItemsData($catId)
	{
        $categoty   = Mage::getModel('catalog/category')->load($catId);
        /** @var $categoty Mage_Catalog_Model_Categeory */
        $categories = $categoty->getChildrenCategories();
		$this->getLayer()->getProductCollection()
			->addCountToCategories($categories);
		$data = array();
		foreach ($categories as $category) {
			if ($category->getIsActive()) {
				$hijoId=$category->getId();
				$data[] = array(
					'label' => Mage::helper('core')->escapeHtml($category->getName()),
					'value' => $category->getId(),
					'count' => $category->getProductCount(),
					'children' =>$this->_getChildrenItemsData($category->getId())
				);
			}
		}
		
        return $data;
    }
	
	protected function _initItems()
    {
		$data = $this->_getItemsData();
        $items=array();
        foreach ($data as $itemData) {
            $items[] = $this->_createItem(
                $itemData['label'],
                $itemData['value'],
                $itemData['count'],
				$itemData['children']
            );
        }
        $this->_items = $items;
		return $this;
    }
	
	protected function _createItem($label, $value, $count=0, $children)
    {
        return Mage::getModel('catalog/layer_filter_item')
            ->setFilter($this)
            ->setLabel($label)
            ->setValue($value)
            ->setCount($count)
			->setchildren($children);
    }
	
}
		