<?php
class Magestore_Manufacturer_Block_Manufacturer extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getManufacturer()     
     { 
        if (!$this->hasData('manufacturer')) {
            $this->setData('manufacturer', Mage::registry('manufacturer'));
        }
        return $this->getData('manufacturer');        
    }
	
	public function getFeaturedManufacturer()
	{
		$featureManufacturers = Mage::getModel("manufacturer/manufacturer")->getFeaturedManufacturer();
		//$featureManufacturers->load();
		
		return $featureManufacturers;
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
		
			$img = "<img src='". $url . "' title='". $manufacturer->getName()."' border='0'/>";
		
			return $img;
		} else {
		
			return null;
		}
	}
	
	public function generateListCharacter()
	{
		$lists = array('#','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');		
		
	    echo("<ul class='manufacturer_char_filter'>");
		for($i = 1; $i <= count($lists); $i++)
		{	
			
			echo("<li class='manufacturer_char_filter_li' onclick=\"filtraLetra('".$lists[$i-1]."')\">". $lists[$i-1] . "</li>");
			if(fmod($i,5)==0){
				 echo("</ul><ul class='manufacturer_char_filter'>");
			}
		}
		
		echo("</ul>");
		
	}
	
	public function getCharSearchUrl($begin)
	{
		$lists = array('0-9','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','W','U','V','X','Y','Z');		
		if(!in_array($begin,$lists))
		{
			return $url = $this->getUrl("manufacturer/index/index", array());
		}
		
		return $this->getUrl("manufacturer/index/index", array("begin"=>$begin));

	}
	
	public function getManufacturers()
	{
		$begin = $this->getRequest()->getParam("begin");
		$manufacturers = Mage::getModel("manufacturer/manufacturer")->getManufacturers($begin);
		//$manufacturers->load();
		
		return $manufacturers;
	}
	
	
	// Devuelve array con los nombres de todos los manufacturers que tienen algún artículo en stock
	
	public function getManufacturersInStock()
	{
		$productCollection = Mage::getResourceModel('catalog/product_collection')
                        ->addAttributeToSelect("*")
						->addAttributeToFilter('type_id', 'configurable')
                        ->addAttributeToFilter('status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED));

		Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($productCollection);
		
		$manufacturers_in_stock = array();
		foreach($productCollection as $product){
			if (!in_array($product->getData('manufacturer'),$manufacturers_in_stock)){ //si no esta en el listado de marcas
				$manufacturers_in_stock[]=$product->getData('manufacturer'); //insertamos marca del articulo con stock
			}
		}
		return $manufacturers_in_stock;
	}

	
}