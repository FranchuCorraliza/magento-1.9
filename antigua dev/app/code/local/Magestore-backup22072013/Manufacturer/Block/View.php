<?php
class Magestore_Manufacturer_Block_View extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getManufacturer()     
     { 
        if (!$this->hasData('manufacturer')) {
			$manufacturer = Mage::getModel("manufacturer/manufacturer");
			$adminManufacturerID = $this->getRequest()->getParam("id");
			$manufacturerID = $manufacturer->getManufacturerID($adminManufacturerID);
			$manufacturer->load($manufacturerID,"manufacturer_id");
			$manufacturer = Mage::getModel('manufacturer/manufacturer')->loadDataManufacturer($manufacturer);
            $manufacturer = $manufacturer->loadDataManufacturer($manufacturer);
			$this->setData('manufacturer',$manufacturer);
        }
		if($manufacturer->getData('status'))
		
			return $this->getData('manufacturer');
		else
		
			return null;
    }
	

	
	public function getManufacturerDetailUrl($manufacturer)
	{
		$url = $this->getUrl($manufacturer->getUrlKey(), array());
	
		return $url;	
	}
	
	public function getManufacturerImage($manufacturer)
	{
		if($manufacturer->getImage())
		{
			$url = Mage::helper('manufacturer')->getUrlImagePath($manufacturer->getName()) .'/'. $manufacturer->getImage();
		
			$img = "<img  src='". $url . "' title='". $manufacturer->getName()."' border='0' align='left' style='padding-right:10px'/>";
		
			return $img;
		} else{
		
			return null;
		}
	}
	

	 public function getProductListHtml()
    {
        return $this->getChildHtml('search_result_list');
    }
	
    public function generateCategoryTree($manufacturer)
	{
		$categories = Mage::getModel('manufacturer/manufacturer')->getManufacturerCategories($manufacturer);
	
		//var_dump($categories);die();
		
		$this->setLevelList($categories);
		
		$this->setCategories($categories);
		
		$category_level = $this->getLevelList();
		
		$html = "";
		
		for($i = 0; $i<count($category_level ); $i++)
		$html .= $this->buildTree($manufacturer,  $i,0);
		
		
		return $html;
		
	}
	
	public function setLevelList($categories)
	{
		$category_level = array();
		foreach($categories as $category)
		{
			
			$level = $category->getLevel();
			if(!in_array($level ,$category_level))
			{
				$category_level[] = $level;
			}
		}
		
		sort($category_level,SORT_NUMERIC);
		
		
		
		$this->setData('category_level',$category_level);
	}
	
	
	public function getLevelList()
	{
		return $this->getData('category_level');
	}
	
	public function setCategories($categories)
	{
		$this->setData('categories',$categories);
	}
	
	public function getCategories()
	{
		return $this->getData('categories');
	}
	
	public function buildTree($manufacturer, $level, $parrent_id)
	{
		$current_catid = $this->getRequest()->getParam("catid");
		$categories = $this->getCategories();
		
	
		
		$category_level = $this->getLevelList();
		
		if($level == count($category_level))
		{
			return "";
		}
		
		$html ="";
		
		for($i = 0; $i < count($categories); $i++)
		{
			if($categories[$i]->getListed())
			{
				
				continue;
			}
			$category_path = $categories[$i]->getPath();
			
			
			$category_path = explode("/",$category_path );
			
			if($parrent_id)
			{
				if(is_array($category_path) && in_array($parrent_id, $category_path))
				{
					$is_child = true;
				}
				else
				{
					$is_child = false;
				}
			}
			else
			{
				$is_child = true;
			}
			
			if( $categories[$i]->getLevel() == $category_level[$level] && $is_child)
			{								
				$categories[$i]->listed = 1;
				
				$this->setCategories($categories);
				
				//var_dump($categories[$i]->getPath());die();
				
				if($current_catid == $categories[$i]->getId())
				{
					$html .= "<li><a href='". $this->getManufacturerDetailUrl($manufacturer)."?catid=".$categories[$i]->getId()."' class='active'>".$categories[$i]->getName()."</a>" ;
				}
				else
				{
					$html .= "<li><a href='". $this->getManufacturerDetailUrl($manufacturer)."?catid=".$categories[$i]->getId()."'>".$categories[$i]->getName()."</a>" ;
				}
				$html .= $this->buildTree($manufacturer,$level+1, $categories[$i]->getId());
				
				
				
				$html .= "</li>";
			}	
            			
		}
		
		if($html)
		{
			$html = "<ul>". $html . "</ul>";
		}
		
		return $html;
	}
	
	public function getResultCount()
    {    
        $size = Mage::getModel('manufacturer/manufacturer')->getProductCollection()->getSize();
              
        return $size;
    }
	
	
	public function setListCollection() {
      
		$this->getChild('search_result_list')
           ->setCollection($this->_getProductCollection());
    }

    protected function _getProductCollection(){
        return $this->getSearchModel()->getProductCollection();
    }
	
	public function getSearchModel()
    {
        return Mage::getSingleton('manufacturer/manufacturer');
    }
	
	
}