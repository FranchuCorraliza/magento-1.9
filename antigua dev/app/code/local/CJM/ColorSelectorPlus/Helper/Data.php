<?php

class CJM_ColorSelectorPlus_Helper_Data extends Mage_Core_Helper_Abstract
{	
	public function getImageSizes()
	{
		$theSizes = array();
		
		$b = str_replace(" ", "", Mage::getStoreConfig('color_selector_plus/templatesettings/prodimgsize', Mage::app()->getStore()));
		$b = !$b ? '265x265' : strtolower($b);
		$exploder = preg_match("/x/i", $b) ? $b : $b.'x'.$b;
		$sizes = explode("x", $exploder);
        $theSizes['base']['width'] = $sizes[0];
        $theSizes['base']['height'] = $sizes[1];
        
        $m = str_replace(" ", "", Mage::getStoreConfig('color_selector_plus/templatesettings/moreimgsize', Mage::app()->getStore()));
		$m = !$m ? '56x56' : strtolower($m);
		$exploder = preg_match("/x/i", $m) ? $m : $m.'x'.$m;
		$sizes = explode("x", $exploder);
        $theSizes['more']['width'] = $sizes[0];
        $theSizes['more']['height'] = $sizes[1];
        
        $l = str_replace(" ", "", Mage::getStoreConfig('color_selector_plus/templatesettings/listimgsize', Mage::app()->getStore()));
		$l = !$l ? '135x135' : strtolower($l);
		$exploder = preg_match("/x/i", $l) ? $l : $l.'x'.$l;
		$sizes = explode("x", $exploder);
        $theSizes['list']['width'] = $sizes[0];
        $theSizes['list']['height'] = $sizes[1];

  		return $theSizes;
	}
	
	public function getUsesSwatchAttribs($_product)
	{
		$swatch_attributes = Mage::helper('colorselectorplus')->getSwatchAttributes();
		$confAttributes = $_product->getTypeInstance(true)->getConfigurableAttributesAsArray($_product);
        foreach($confAttributes as $confAttribute):				
			$thecode = $confAttribute["attribute_code"];
			if(in_array($thecode, $swatch_attributes)) {
				return 'yes';} 
		endforeach;
		
		return 'no';	
	}
	
	public function getConfigQueryString($_options, $confAttributes)
	{
		$swatch_attributes = Mage::helper('colorselectorplus')->getSwatchAttributes();
		$query = array();
		
		foreach($confAttributes as $confAttribute) :
			if(in_array($confAttribute['attribute_code'], $swatch_attributes))
			{ 
				$attributeCode = $confAttribute['attribute_code'];
				$attributeName = $confAttribute['label'];
					
				foreach($_options as $_option) :
					if($attributeName == $_option['label']) {
						$attributeoption = $_option['value']; }
				endforeach;
						
				if($attributeoption) {
					$query[$attributeCode] = $attributeoption; }
			}
		endforeach;	 
		
		return '?'.http_build_query($query);
	}
	
	public function getSortedByPosition($array)
	{
        $new = '';
        $new1 = '';
        foreach ($array as $k=>$na)
            $new[$k] = serialize($na);
        $uniq = array_unique($new);
        foreach($uniq as $k=>$ser)
            $new1[$k] = unserialize($ser);
        if(isset($new1)){
        	return $new1; }
        else {
        	return '';}
    }
	
	public function getAssociatedOptions($product, $att)
	{
		$swatch_attributes = Mage::helper('colorselectorplus')->getSwatchAttributes();
		$confAttributes = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);
        $availattribs = array();
		$thecode = '';
		$html = '';  
       	
		foreach ($confAttributes as $confAttribute) {
			$thecode = $confAttribute["attribute_code"];
			if(in_array($thecode, $swatch_attributes))
			{
				$attributeCode = $confAttribute['attribute_code'];
				$attributeName = $confAttribute['label'];
				$options = $confAttribute["values"];
				
				foreach($options as $option) {
					$string = $option["label"];
					$label = trim(substr($string, 0, strpos("$string#", "#")));
					$optvalue = $option["value_index"]; 
					$availattribs['value'][] = $optvalue;
					$availattribs['label'][] = $label;;							
               	}
			}
		}
		
		if($availattribs) {
			$count = count($availattribs['value']);
		} else {
			$count = 0; }
		
		if($count < 1) {
			$html .= '<select class="'.$att.'" id="'.$att.'__value_id__" name="'.$att.'[__value_id__]" disabled="disabled" style="width:100px;">'; }
		else {
			$html .= '<select class="'.$att.'" id="'.$att.'__value_id__" name="'.$att.'[__value_id__]" style="width:100px;">';
			if($att == 'cjm_moreviews') {
				$html .= '<option value="">';
				$html .= Mage::helper('colorselectorplus')->__('*Always Visible*');
				$html .= '</option>'; 
				$html .= '<option value="none">';
				$html .= Mage::helper('colorselectorplus')->__('*None*');
				$html .= '</option>'; }
			else {
				$html .= '<option value="">&nbsp;</option>'; }
			for($s = 0; $s < $count; $s++) {
    			$html .= '<option value="'.$availattribs['value'][$s].'">'.$availattribs['label'][$s].'</option>'; }
			$html .= '</select><br />'; 
		}

		return $html;	
	}
	
	public function getMoreViews($_product)
	{
		$html = '';
		$html_first = '';
		$onloads = '';
		$defaults = '';
		$defaultmoreviews = 0;
		$product_base = Mage::helper('colorselectorplus')->decodeImages($_product);
		$theSizes = Mage::helper('colorselectorplus')->getImageSizes();
		if($product_base):
			$swatch_attributes = Mage::helper('colorselectorplus')->getSwatchAttributes();
			$confAttributes = $_product->getTypeInstance(true)->getConfigurableAttributesAsArray($_product);
			$images = $product_base['image'];
			
			foreach($images as $key => $image):
				if($product_base['morev'][$key] != ''):
					$defaultmoreviews = 1;
					$product_image = $product_base['full'][$key];
					$product_thumb = $product_base['thumb'][$key];
					$product_label = $product_base['label'][$key];
					$product_imagezoom = $product_base['image_zoom'][$key];
					$onloads .= '<div  class="slide onload-moreviews"><a  class="zoom-thumbnail" href="'.$product_image.'" data-easyzoom-source="'.$product_imagezoom.'"><img src="'.$product_thumb.'" width="'.$theSizes['more']['width'].'" height="'.$theSizes['more']['height'].'" alt="'.$product_label.'" title="'.$product_label.'" /></a></div>';	
				elseif($product_base['morev'][$key] == ''):
					$defaultmoreviews = 1;
					$product_image = $product_base['full'][$key];
					$product_thumb = $product_base['thumb'][$key];
					$product_label = $product_base['label'][$key];
					$product_imagezoom = $product_base['image_zoom'][$key];
					$defaults .= '<div class="slide default-moreviews"><a  class="zoom-thumbnail"  href="'.$product_image.'" data-easyzoom-source="'.$product_imagezoom.'"><img src="'.$product_thumb.'" width="'.$theSizes['more']['width'].'" height="'.$theSizes['more']['height'].'" alt="'.$product_label.'" title="'.$product_label.'" /></a></div>';
				endif;
			endforeach;
			
			/*if($defaultmoreviews == 1) {
				$html_first = '<h2 id="moreviews-title">'.Mage::helper('colorselectorplus')->__('More Views').'</h2><ul id="ul-moreviews">'; }
			else {
				$html_first = '<h2 id="moreviews-title" style="display:none;">'.Mage::helper('colorselectorplus')->__('More Views').'</h2><ul id="ul-moreviews">'; }*/
			
			$html = $html_first .= $html;
			
			if($onloads){ $html .= $onloads; }
			if($defaults){ $html .= $defaults; }
		
			foreach($confAttributes as $confAttribute):				
				$thecode = $confAttribute["attribute_code"];
				if(in_array($thecode, $swatch_attributes)): 
					$options = $confAttribute["values"];
					foreach($options as $option):
						$value = $option['value_index'];
						$i=0;
						foreach($images as $key => $image):
							if($product_base['morev'][$key] == $value):
								$product_image = $product_base['full'][$key];
								$product_thumb = $product_base['thumb'][$key];
								$product_label = $product_base['label'][$key];
								$product_imagezoom = $product_base['image_zoom'][$key];
								$html .= '<div class="slide moreview'.$value.'" style="display:none;"><a id="moreview'. $value.'-'.$i.'" class="zoom-thumbnail" href="'.$product_image.'" data-easyzoom-source="'.$product_imagezoom.'"><img src="'.$product_thumb.'" width="'.$theSizes['more']['width'].'" height="'.$theSizes['more']['height'].'" alt="'.$product_label.'" title="'.$product_label.'" /></a></div>';
								$i++;
							endif;
							
						endforeach;
               		endforeach;
				endif;
			endforeach;
		
			$html .= '</ul>';
			return $html;
		else:
			return '';
		endif;
	}
	
	public function getMoreViewsZoom($_product)
	{
		$html = '';
		$html_first = '';
		$onloads = '';
		$defaults = '';
		$defaultmoreviews = 0;
		$product_base = Mage::helper('colorselectorplus')->decodeImages($_product);
		$theSizes = Mage::helper('colorselectorplus')->getImageSizes();
		
		if($product_base):
			$swatch_attributes = Mage::helper('colorselectorplus')->getSwatchAttributes();
			$confAttributes = $_product->getTypeInstance(true)->getConfigurableAttributesAsArray($_product);
			$images = $product_base['image'];
			foreach($images as $key => $image):
				if($product_base['morev'][$key] != ''):
					$defaultmoreviews = 1;
					$product_image = $product_base['image'][$key];
					$product_thumb = $product_base['thumb'][$key];
					$product_label = $product_base['label'][$key];
					$product_gallery = Mage::helper('colorselectorplus')->getGalUrl($product_base['id'][$key], $_product->getId());
					$onloads .= '<div class="slide onload-moreviews"><a class="zoom-thumbnail" href="'.$product_base['full'][$key].'" rel="popupWin:\''.$product_gallery.'\', useZoom: \'cloudZoom\', smallImage: \''.$product_image.'\'" class="cloud-zoom-gallery" title="'.$product_label.'"><img src="'.$product_thumb.'" width="'.$theSizes['more']['width'].'" height="'.$theSizes['more']['height'].'" alt="'.$product_label.'" title="'.$product_label.'" /></a></div>';	
				elseif($product_base['morev'][$key] == ''):
					$defaultmoreviews = 1;
					$product_image = $product_base['image'][$key];
					$product_thumb = $product_base['thumb'][$key];
					$product_label = $product_base['label'][$key];
					$product_gallery = Mage::helper('colorselectorplus')->getGalUrl($product_base['id'][$key], $_product->getId());
					$defaults .= '<div class="slide default-moreviews"><a class="zoom-thumbnail" href="'.$product_base['full'][$key].'" rel="popupWin:\''.$product_gallery.'\', useZoom: \'cloudZoom\', smallImage: \''.$product_image.'\'" class="cloud-zoom-gallery" title="'.$product_label.'"><img src="'.$product_thumb.'" width="'.$theSizes['more']['width'].'" height="'.$theSizes['more']['height'].'" alt="'.$product_label.'" title="'.$product_label.'" /></a></div>';
				endif;
			endforeach;
			
			if($onloads){ $html .= $onloads; }
			if($defaults){ $html .= $defaults; }
		
			foreach($confAttributes as $confAttribute):				
				$thecode = $confAttribute["attribute_code"];
				if(in_array($thecode, $swatch_attributes)): 
					$options = $confAttribute["values"];
					foreach($options as $option):
						$value = $option['value_index'];
						foreach($images as $key => $image):
							if($product_base['morev'][$key] == $value):
								$product_image = $product_base['image'][$key];
								$product_thumb = $product_base['thumb'][$key];
								$product_label = $product_base['label'][$key];
								$product_gallery = Mage::helper('colorselectorplus')->getGalUrl($product_base['id'][$key], $_product->getId());
								$html .= '<div class="moreview'.$value.'" style="display:none;"><a href="'.$product_base['full'][$key].'" rel="popupWin:\''.$product_gallery.'\', useZoom: \'cloudZoom\', smallImage: \''.$product_image.'\'" class="cloud-zoom-gallery" title="'.$product_label.'"><img src="'.$product_thumb.'" width="'.$theSizes['more']['width'].'" height="'.$theSizes['more']['height'].'" alt="'.$product_label.'" title="'.$product_label.'" /></a></div>';
							endif;
						endforeach;
               		endforeach;
				endif;
			endforeach;
		
			$html .= '</ul>';
			return $html;
		else:
			return '';
		endif;
	}
	
	public function getQueryString($_product)
	{
		$swatch_attributes = Mage::helper('colorselectorplus')->getSwatchAttributes();
		$query = array();
		$configurable_product = Mage::getModel('catalog/product_type_configurable');
		$parentIdArray = $configurable_product->getParentIdsByChild($_product->getId());
			
		if($_product->getTypeId() == 'simple' && isset($parentIdArray[0])) {
			foreach($_product->getAttributes() as $_attribute):
				if(in_array($_attribute->getAttributeCode(), $swatch_attributes))
				{ 
					$attributename = $_attribute->getAttributeCode();
					$attributeoption = Mage::getModel('catalog/product')->load($_product->getId())->getAttributeText($attributename);
					if($attributeoption) {
						$query[$attributename] = $attributeoption; }
				}
			endforeach;	 
		}
		
		if($query) {
			return $query; }
		
		return '';
	}
	
	public function getSwatchList()
	{
		$swatch_attributes = Mage::helper('colorselectorplus')->getSwatchAttributes();
		$html = '';
		
		$count = count($swatch_attributes);
		
		for($i = 0; $i < $count; $i++) {
			
			if($i == $count-1) {
				$html .= $swatch_attributes[$i];
			} else {
				$html .= $swatch_attributes[$i].'&nbsp;&#8226;&nbsp;';
			}
		}
		return $html;
	}
	
	public function getSwatchAttributes()
	{
		$swatch_attributes = array();
		$swatchattributes = Mage::getStoreConfig('color_selector_plus/colorselectorplusgeneral/colorattributes',Mage::app()->getStore());
		$swatch_attributes = explode(",", $swatchattributes);
		
		 foreach($swatch_attributes as &$attribute) {
         	$attribute = Mage::getModel('eav/entity_attribute')->load($attribute)->getAttributeCode();
		 }
		 unset($attribute);
	
		return $swatch_attributes;
	}
	
	public function getSwatchSize($theCode)
	{
		if($theCode == 'list'):
			$swatchsize = str_replace(" ", "", Mage::getStoreConfig('color_selector_plus/colorselectorplusgeneral/listsize', Mage::app()->getStore()));
			if (!$swatchsize){
				$swatchsize = '12x12';}
			if(preg_match("/x/i", $swatchsize)){
				return $swatchsize;}
			else {
				return $swatchsize.'x'.$swatchsize;}
			return $swatchsize;
		elseif($theCode == 'shopby'):
			$swatchsize = str_replace(" ", "", Mage::getStoreConfig('color_selector_plus/colorselectorplusgeneral/layersize', Mage::app()->getStore()));
			if (!$swatchsize){
				$swatchsize = '15x15';}
			if(preg_match("/x/i", $swatchsize)){
				return $swatchsize;}
			else {
				return $swatchsize.'x'.$swatchsize;}
			return $swatchsize;
		elseif($theCode != 'null'):
			$swatchsize = str_replace(" ", "", Mage::getStoreConfig('color_selector_plus/swatchsizes/swatchsize_'.$theCode.'_swatchsizes', Mage::app()->getStore()));
			if($swatchsize){
				if(preg_match("/x/i", $swatchsize)){
					return $swatchsize;}
				else {
					return $swatchsize.'x'.$swatchsize;}
			} else {
				$swatchsize = str_replace(" ", "", Mage::getStoreConfig('color_selector_plus/colorselectorplusgeneral/size',Mage::app()->getStore()));
				if (!$swatchsize){
					$swatchsize = '25x25';}
				if(preg_match("/x/i", $swatchsize)){
					return $swatchsize;}
				else {
					return $swatchsize.'x'.$swatchsize;}
				return $swatchsize;
			}
		else:
			$swatchsize = str_replace(" ", "", Mage::getStoreConfig('color_selector_plus/colorselectorplusgeneral/size',Mage::app()->getStore()));
			if (!$swatchsize){
				$swatchsize = '25x25';}
			if(preg_match("/x/i", $swatchsize)){
				return $swatchsize;}
			else {
				return $swatchsize.'x'.$swatchsize;}
			return $swatchsize;
		endif;
	}
	
	public function findColorImage($value, $arr, $key, $type)
	{
		$found = '';
		if(isset($arr[$key])) {
 			$total = count($arr[$key]);
			if($total>0)
 			{
				for($i=0; $i<$total;$i++)
 				{
					if($value == ucwords($arr[$key][$i]))//if it matches the color listed in the attribute
 					{
 						$found = $arr[$type][$i];//return the image src
					}
 				}
 			}
		}
 		return $found;
	}
	
	public function decodeImages($_product)
	{
		$_gallery = $_product->getMediaGalleryImages();
		$theSizes = Mage::helper('colorselectorplus')->getImageSizes();
		if(count($_gallery) > 3):
			$imgIdsBase = array();
			$imgIdsMoreViews = array();
			$product_base = array();
			$cjm_colorselector_base = unserialize($_product->getData('cjm_imageswitcher'));
			$cjm_colorselector_more = unserialize($_product->getData('cjm_moreviews'));
			if($cjm_colorselector_base && $cjm_colorselector_more):
				
				foreach($cjm_colorselector_base as $cjm_colorselectorItem => $cjm_colorselectorVal):
					$imgIdsBase[$cjm_colorselectorItem]['colorval'] = $cjm_colorselectorVal;
				endforeach;
				
				foreach($cjm_colorselector_more as $cjm_colorselectorItem => $cjm_colorselectorVal):
					$imgIdsMoreViews[$cjm_colorselectorItem]['moreviews'] = $cjm_colorselectorVal;
				endforeach;
				
				foreach ($_gallery as $_image ):
					$product_base['color'][] 		= strval($imgIdsBase[$_image['value_id']]['colorval']);
					$product_base['full'][] 		= strval(Mage::helper('catalog/image')->init($_product, 'image', $_image->getFile()));
					$product_base['image'][] 		= strval(Mage::helper('catalog/image')->init($_product, 'image', $_image->getFile())->resize(398,398));
					$product_base['thumb'][] 		= strval(Mage::helper('catalog/image')->init($_product, 'thumbnail', $_image->getFile())->resize($theSizes['more']['width'],$theSizes['more']['height']));
					$product_base['morev'][] 		= strval($imgIdsMoreViews[$_image['value_id']]['moreviews']);
					$product_base['label'][] 		= $_image['label'];
					$product_base['id'][] 			= $_image['value_id'];
					$product_base['defaultimg'][] 	= $_image['defaultimg'];
					$product_base['image_zoom'][]	= strval(Mage::helper('catalog/image')->init($_product, 'thumbnail', $_image->getFile())->resize(398,398));
				endforeach;
			endif;
		
			return $product_base;
		
		elseif (count($_gallery) > 1):
			$imgIdsBase = array();
			$imgIdsMoreViews = array();
			$product_base = array();
			$cjm_colorselector_base = unserialize($_product->getData('cjm_imageswitcher'));
			$cjm_colorselector_more = unserialize($_product->getData('cjm_moreviews'));
			foreach ($_gallery as $_image ):
 					$product_base['color'][] 		= '';
					$product_base['full'][] 		= strval(Mage::helper('catalog/image')->init($_product, 'image', $_image->getFile()));
					$product_base['image'][] 		= strval(Mage::helper('catalog/image')->init($_product, 'image', $_image->getFile())->resize(398,398));;
					$product_base['thumb'][] 		= strval(Mage::helper('catalog/image')->init($_product, 'thumbnail', $_image->getFile())->resize($theSizes['more']['width'],$theSizes['more']['height']));
					$product_base['morev'][] 		= '';
					$product_base['label'][] 		= $_image['label'];
					$product_base['id'][] 			= $_image['value_id'];
					$product_base['defaultimg'][] 	= $_image['defaultimg'];
					$product_base['image_zoom'][]	= strval(Mage::helper('catalog/image')->init($_product, 'thumbnail', $_image->getFile())->resize(398,398));
			endforeach;
			return $product_base;
		
		
		else:
			
			return '';
		
		endif;	
	}
	
	public function getSwatchUrl($optionId)
    {
        $uploadDir = Mage::getBaseDir('media') . DIRECTORY_SEPARATOR . 
                                                    'colorselectorplus' . DIRECTORY_SEPARATOR . 'swatches' . DIRECTORY_SEPARATOR;
        if (file_exists($uploadDir . $optionId . '.jpg'))
        {
            return Mage::getBaseUrl('media') . '/' . 'colorselectorplus' . '/' . 'swatches' . '/' . $optionId . '.jpg';
        }
        return '';
    }
	
	public function getSwatchHtml($attributeCode, $atid, $_product)
	{
		$storeId = Mage::app()->getStore();
		$frontendlabel = 'null';
		$urloption = '';
		$html = '';
		$cnt = 1;
		$_option_vals = array();				
		$_colors = array();
		$zoomenabled = Mage::getStoreConfig('color_selector_plus/zoom/enabled');
		$hide = Mage::getStoreConfig('color_selector_plus/colorselectorplusgeneral/hidedropdown', $storeId);
		$frontText = Mage::getStoreConfig('color_selector_plus/colorselectorplusgeneral/dropdowntext', $storeId);
		$swatchsize = Mage::helper('colorselectorplus')->getSwatchSize($attributeCode);
		$sizes = explode("x", $swatchsize);
        $width = $sizes[0];
        $height = $sizes[1];
        
        if(isset($_GET[$attributeCode])) {
        	$urloption = $_GET[$attributeCode]; }
		
		if($hide == 0) {
			$html = $html.'<div class="swatchesContainerPadded"><ul id="ul-attribute'.$atid.'">'; }
		else {
			$html = $html.'<div class="swatchesContainer"><ul id="ul-attribute'.$atid.'">'; }			
                				
        $_collection = Mage::getResourceModel('eav/entity_attribute_option_collection')->setPositionOrder('asc')->setAttributeFilter($atid)->setStoreFilter(0)->load();
		
		foreach( $_collection->toOptionArray() as $_cur_option ) {
			$_option_vals[$_cur_option['value']] = array(
				'internal_label' => $_cur_option['label'],
				'order' => $cnt
			);
			$cnt++;
        }

		$configAttributes = $_product->getTypeInstance(true)->getConfigurableAttributesAsArray($_product);
    	
    	foreach($configAttributes as $attribute) { 
			if($attribute['attribute_code'] == $attributeCode) {
				foreach($attribute["values"] as $value) {
					$adminlabel = "";
					$order = "";
					if (array_key_exists($value['value_index'],$_option_vals))
					{
						$adminlabel=$_option_vals[$value['value_index']]['internal_label'];
						$order=$_option_vals[$value['value_index']]['order'];
					}
         			array_push($_colors, array(
         				'id' => $value['value_index'],
         				'frontlabel' => $value['store_label'],
         				'adminlabel' => $adminlabel,
         				'order' => $order
         			));
         		}
         		break;
         	}
     	}
				
		$_color_swatch = Mage::helper('colorselectorplus')->getSortedByPosition($_colors);
		$_color_swatch = array_values($_color_swatch);
		
		foreach ($_color_swatch as $key => $val) { 
   			 $sortSingle[$key] = $_color_swatch[$key]['order']; } 

		asort ($sortSingle); 
		reset ($sortSingle); 
		
		while (list ($singleKey, $singleVal) = each ($sortSingle)) { 
    		$newArr[] = $_color_swatch[$singleKey]; } 

		$_color_swatch = $newArr;  
		
		foreach($_color_swatch as $_inner_option_id)
 		{
			$zoomStuff = '';
			$theId = $_inner_option_id['id'];
			$adminLabel = $_inner_option_id['adminlabel'];
			$altText = $_inner_option_id['frontlabel'];
			if($frontText == 0) {
				$frontendlabel = $altText; }
			else {
				$frontendlabel = 'null'; }
			
			preg_match_all('/((#?[A-Za-z0-9]+))/', $adminLabel, $matches);
				
			if ( count($matches[0]) > 0 )
			{
				$color_value = $matches[1][count($matches[0])-1];
				$findme = '#';
				$pos = strpos($color_value, $findme);
				
				$product_base = Mage::helper('colorselectorplus')->decodeImages($_product);	
				$product_image = Mage::helper('colorselectorplus')->findColorImage($theId, $product_base, 'color', 'image'); //returns url for base image
				
				if($zoomenabled && $product_image):
					$product_image_full = Mage::helper('colorselectorplus')->findColorImage($theId, $product_base, 'color', 'full'); 
					$product_gallery = Mage::helper('colorselectorplus')->getGalUrl(Mage::helper('colorselectorplus')->findColorImage($theId, $product_base, 'color', 'id'), $_product->getId());
					$zoomStuff = '<a id="anchor'.$theId.'" href="'.$product_image_full.'" rel="popupWin:\''.$product_gallery.'\', useZoom: \'cloudZoom\', smallImage: \''.$product_image.'\'" class="cloud-zoom-gallery" title="'.$frontendlabel.'">';
				endif;
				
				if($urloption==$altText)
				{
					$html = $html.'<script type="text/javascript">Event.observe';
					$html = $html."(window, 'load', function() {";
					$html = $html."colorSelected('attribute".$atid."','".$theId."','".$product_image."','".$frontendlabel."');clicker(".$theId.");});</script>";
				}
					
              	if ($_product->getCjm_useimages() == 1 && $product_image):
					$html = $html.'<li class="swatchContainer">';
					$html = $zoomStuff ? $html.$zoomStuff : $html;
					$html = $html.'<img src="'.$product_image.'" id="swatch'.$theId.'" class="swatch" alt="'.$altText.'" width="'.$width.'px" height="'.$height.'px" title="'.$altText.'" ';
					$html = $html.'onclick="colorSelected';
					$html = $html."('attribute".$atid."','".$theId."','".$product_image."','".$frontendlabel."')";
					$html = $html.'" />';
					$html = $zoomStuff ? $html.'</a></li>' : $html.'</li>';
              	elseif(Mage::helper('colorselectorplus')->getSwatchUrl($theId)):
					$swatchimage = Mage::helper('colorselectorplus')->getSwatchUrl($theId);
					$html = $html.'<li class="swatchContainer">';
					$html = $zoomStuff ? $html.$zoomStuff : $html;
					$html = $html.'<img src="'.$swatchimage.'" id="swatch'.$theId.'" class="swatch" alt="'.$altText.'" width="'.$width.'px" height="'.$height.'px" title="'.$altText.'" ';
					$html = $html.'onclick="colorSelected';
					$html = $html."('attribute".$atid."','".$theId."','".$product_image."','".$frontendlabel."')";
					$html = $html.'" />';
					$html = $zoomStuff ? $html.'</a></li>' : $html.'</li>';
				elseif($pos !== false):
              		$html = $html.'<li class="swatchContainer">';
					$html = $zoomStuff ? $html.$zoomStuff : $html;
					$html = $html.'<div id="swatch'.$theId.'" title="'.$altText.'" class="swatch" style="background-color:'.$color_value.'; width:'.$width.'px; height:'.$height.'px;" ';
					$html = $html.' onclick="colorSelected';
					$html = $html."('attribute".$atid."','".$theId."','".$product_image."','".$frontendlabel."')";
					$html = $html.'">';
					$html = $zoomStuff ? $html.'</div></a></li>' : $html.'</div></li>';
				else:
					//@perichus Cambiamos la funcionalidad de esta extensión
//					$swatchimage = Mage::helper('colorselectorplus')->getSwatchUrl('empty');
//					$html = $html.'<li class="swatchContainer">';
//					$html = $zoomStuff ? $html.$zoomStuff : $html;
//					$html = $html.'<img src="'.$swatchimage.'" id="swatch'.$theId.'" class="swatch" alt="'.$altText.'" width="'.$width.'px" height="'.$height.'px" title="'.$altText.' - Please Upload Swatch!" ';
//					$html = $html.'onclick="colorSelected';
//					$html = $html."('attribute".$atid."','".$theId."','".$product_image."','".$frontendlabel."')";
//					$html = $html.'" />';
//					$html = $zoomStuff ? $html.'</a></li>' : $html.'</li>';
				
				
					$swatchimage = Mage::helper('colorselectorplus')->getSwatchUrl('empty');
					$html = $html.'<li class="swatchContainer">';
					$html = $zoomStuff ? $html.$zoomStuff : $html;
					$html = $html.'<div id="swatch'.$theId.'" class="swatch" alt="'.$altText.'" title="'.$altText.'" ';
					$html = $html.'onclick="colorSelected';
					$html = $html."('attribute".$atid."','".$theId."','".$product_image."','".$frontendlabel."')";
					$html = $html.'">'.$altText.'</div>';
					$html = $zoomStuff ? $html.'</a></li>' : $html.'</li>';
				endif;
			}
 		}
		$html = $html.'</ul></div><p class="float-clearer"></p>';
		return $html;	
	}
	
	public function getSwatchImg($option)
    {
        return Mage::helper('colorselectorplus')->getSwatchUrl($option->getId());
    }
	
	public function getAttribOptions()
    {
   		$optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')->setAttributeFilter(Mage::registry('entity_attribute')->getId())->setPositionOrder('asc', true)->load();
        return $optionCollection;
    }
	
	public function gettheUrl()
    {
       $pageURL = 'http';
 		
 		if(isset($_SERVER["HTTPS"])) {
 			if ($_SERVER["HTTPS"] == "on") {
				$pageURL .= "s";}
		}
 		
 		$pageURL .= "://";
 		
 		if ($_SERVER["SERVER_PORT"] != "80") {
  			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 		} else {
  			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 		}
		
		return $pageURL;
    }
    
	public function getShopByHtml($_item)
	{
		$html = '';
		$swatch_attributes = Mage::helper('colorselectorplus')->getSwatchAttributes();
		$ss = Mage::helper('colorselectorplus/data')->getSwatchSize('shopby');
		$sizes = explode("x", $ss);
        $width = $sizes[0];
        $height = $sizes[1];
		$theAttribute = $_item['code'];
		$theUrl = $_item['url'];
		$theImage = $_item['image'];
		$theBGcolor = $_item['bgcolor'];
		$theLabel = $_item['label'].$_item['count'];
		
		if(in_array($theAttribute, $swatch_attributes) && Mage::getStoreConfig('color_selector_plus/colorselectorplusgeneral/showonlayer',Mage::app()->getStore()) == 1):
			if($theImage != ''):
        		$html = '<a href="'.$theUrl.'"><img class="swatch-shopby" src="'.$theImage.'" title="'.$theLabel.'" alt="'.$theLabel.'" style="width:'.$width.'px; height:'.$height.'px;"></a>';
      		elseif($theBGcolor != ''):
       			$html = '<a href="'.$theUrl.'">';
				$html .= '<div class="swatch-shopby" style="background-color:'.$theBGcolor.'; width:'.$width.'px; height:'.$height.'px;" title="'.$theLabel.'"></div>';
				$html .= '</a>';
			else:
				$html = '<a href="'.$theUrl.'" class="swatch-shopby-text">'.$theLabel.'</a>';
			endif;
		else:
			$html =  '<a href="'.$theUrl.'">'.$_item['label'].'</a>'.$_item['count'];
		endif;
		
		return $html;
	}
	
	public function getZoomOptions()
    {
        $options = '';
        
        $width = Mage::getStoreConfig('color_selector_plus/zoom/width');
        if (empty($width) || !is_numeric($width)) { $width = 'auto'; }
        
        $height = Mage::getStoreConfig('color_selector_plus/zoom/height');
        if (empty($height) || !is_numeric($height)) { $height = 'auto'; }
        
        $options .= "zoomWidth: '" . $width . "',";
        $options .= "zoomHeight: '" . $height . "',";
        $options .= "position: '" . Mage::getStoreConfig('color_selector_plus/zoom/position') . "',";
        $options .= "smoothMove: " . (int) Mage::getStoreConfig('color_selector_plus/zoom/smoothmove') . ",";
        $options .= "showTitle: " . Mage::getStoreConfig('color_selector_plus/zoom/title') . ",";
        $options .= "titleOpacity: " . (float) (Mage::getStoreConfig('color_selector_plus/zoom/titleopacity')/100) . ",";

        $adjustX = (int) Mage::getStoreConfig('color_selector_plus/zoom/xoffset');
        if ($adjustX > 0) { $options .= "adjustX: " . $adjustX . ","; }
        
        $adjustY = (int) Mage::getStoreConfig('color_selector_plus/zoom/yoffset');
        if ($adjustY > 0) { $options .= "adjustY: " . $adjustY . ","; }

        $options .= "lensOpacity: " . (float) (Mage::getStoreConfig('color_selector_plus/zoom/lensopacity')/100) . ",";

        $tint = Mage::getStoreConfig('color_selector_plus/zoom/tint');
        if (!empty($tint)) { $options .= "tint: '" . (Mage::getStoreConfig('color_selector_plus/zoom/softfocus') == 'true' ? '' : Mage::getStoreConfig('color_selector_plus/zoom/tint')) . "',"; }
        
        $options .= "tintOpacity: " . (float) (Mage::getStoreConfig('color_selector_plus/zoom/tintopacity')/100) . ",";
        $options .= "softFocus: " . Mage::getStoreConfig('color_selector_plus/zoom/softfocus') . "";

        return $options;
    }
    
   	public function getGalUrl($image=null, $prodId)
    {
        $params = array('id' => $prodId);
        if ($image) {
            $params['image'] = $image;
            return Mage::getUrl('*/*/gallery', $params);
        }
        return Mage::getUrl('*/*/gallery', $params);
    }

	// TO DO -- DISPLAYS SELECT BOXES FOR EACH SWATCH ATTRIBUTE
	//public function getAssociatedOptions($product)
//	{
//		$swatch_attributes = Mage::helper('colorselectorplus')->getSwatchAttributes();
//		$confAttributes = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);
//      $availattribs = array();
//		$thecode = '';
//		$thename = '';
//		$html = '';   	
//		
//		foreach ($confAttributes as $confAttribute) {
//			$thename = $confAttribute["label"];
//			$thecode = $confAttribute["attribute_code"];
//			if(in_array($thecode, $swatch_attributes))
//			{
//           		$html .= '<label>'.$thename.'</label>';
//				$html .= '<select class="imageSwitch" id="imageSwitch__value_id__" name="imageSwitch[__value_id__]" style="width:100px;">';
//				$html .= '<option value="">&nbsp;</option>';
//              	$options = $confAttribute["values"];
//				foreach($options as $option) {
//    				$string = $option["label"];
//					$result = trim(substr($string, 0, strpos("$string#", "#")));
//					$availattribs[] = $result;
//                    $html .= '<option value="'.$result.'">'.$result.'</option>';
//				}
//				$html .= '</select><br />';
//			}
//		}
//		
//		return $html;	
//	}
}