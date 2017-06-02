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
	public function getManufacturerImageUrl($manufacturer)
	{	
		if($manufacturer->getImage())
		{
			$url = Mage::helper('manufacturer')->getUrlImagePath($manufacturer->getName()) .'/'. $manufacturer->getImage();
		
		
			return $url;
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
			
			echo("<li class='manufacturer_char_filter_li filters'>". $lists[$i-1] . "</li>");
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
		$category = $this->getRequest()->getParam("category");
		$gender = $this->getRequest()->getParam("gender");
		$letter = $this->getRequest()->getParam("letter");

		$manufacturers = Mage::getModel("manufacturer/manufacturer")->getManufacturers($category, $gender, $letter);
		//$manufacturers->load();
		
		return $manufacturers;
	}
}