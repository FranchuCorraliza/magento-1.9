<?php
/*
 * Developer: Rene Voorberg
* Team site: http://cmsideas.net/
* Support: http://support.cmsideas.net/
*
*
*/
class Glace_Ajaxcart_Helper_Data extends Mage_Core_Helper_Abstract
{
	//check if extension is enabled
	public static function isEnabled()
	{
		$storeId = Mage::app()->getStore()->getId();
		$isModuleEnabled = Mage::getStoreConfig('advanced/modules_disable_output/Glace_Ajaxcart');
		$isEnabled = Mage::getStoreConfig('ajaxcart/configuration/enabled', $storeId);
		return ($isModuleEnabled == 0 && $isEnabled == 1);
	}
	
	//returns the ajaxcart initialize url with all the add to cart parameters
    public function getInitUrl()
    {
    	$product = Mage::registry('current_product');   
    	if ($product) {
			$block = new Mage_Catalog_Block_Product_View;
			
			if (Mage::helper('ajaxcart')->getMagentoVersion()>1420) { 
				$addToCartUrl = $block->getSubmitUrl($product);
			} else {
				$addToCartUrl = $block->getAddToCartUrl($product);
			}
    	
			$params = $this->getUrlParams($addToCartUrl); 
			unset($params['uenc']);
			$params['skip_popup'] = true;
		} else {
			$params = array();
		}
		
		return Mage::getUrl('ajaxcart/index/init', $params);
    }   	
    
    //adds qty inputs to cart sidebar items with increase/decrease buttons
    public function getQty($_block, $_qty) 
    {	       
		if($this->isEnabled() && Mage::getSingleton('customer/session')->getAjaxCartAction()=='cart-sidebar'){
			$itemId = $_block->getItem()->getId();
			if (Mage::getStoreConfig('ajaxcart/qty_configuration/show_qty_in_cartsidebar')){
				$productId = $_block->getItem()->getProductId();
				$html='';
				if (Mage::getStoreConfig('ajaxcart/qty_configuration/qty_buttons_in_cartsidebar')){
					$html.= '<span class="ajaxcart-qty" id="sidebar-qty-container-'.$itemId.'">';
						$html.='<input name="cart['.$itemId.'][qty]" id="sidebar-qty-'.$itemId.'" type="text" value="'.$_qty.'" class="input-text qty ajaxcart-qty-input" />';
						$html.='<span class="qty-control-box">';
							$html.='<button class="increase" href="javascript:void(0)" onclick="ajaxcart.qtyUp(\''.$productId.'\',\'sidebar-qty-'.$itemId.'\',this);">';
							$html.='<span>+</span>';
							$html.='</button>';
							$html.='<button class="decrease" href="javascript:void(0)" onclick="ajaxcart.qtyDown(\''.$productId.'\',\'sidebar-qty-'.$itemId.'\',this);">';
							$html.='<span>-</span>';
							$html.='</button>';
						$html.='</span>';
					$html.='</span>';
					//$html='test';
				} else {
					$html.= '<span class="ajaxcart-qty" id="sidebar-qty-container-'.$itemId.'">';
						$html.='<input name="cart['.$itemId.'][qty]" id="sidebar-qty-'.$itemId.'" type="text" value="'.$_qty.'" class="input-text qty ajaxcart-qty-input" />';
					$html.='</span>';
				}
				return $html;
			} else {
				return $_qty.'<span class="ajaxcart-qty" id="sidebar-qty-container-'.$itemId.'"><input name="cart['.$itemId.'][qty]" id="sidebar-qty-'.$itemId.'" type="hidden" value="'.$_qty.'" class="input-text qty ajaxcart-qty-input" /></span>';
			}
		} else {
			return $_qty;
		}
    }		
	
	//returns the parameters of a url in array format
	public function getUrlParams($_url)
	{
		$baseUrl = Mage::getBaseUrl();
		$path = str_replace($baseUrl, '', $_url);
		$paramsArray = explode('/', $path);
		unset($paramsArray[count($paramsArray)-1]);
		unset($paramsArray[0]);
		unset($paramsArray[1]);
		unset($paramsArray[2]);
		$paramsArray = array_merge(array(), $paramsArray);
		
		$params = array();
		for ($i = 0; $i<count($paramsArray); $i=$i+2) {
			$params[$paramsArray[$i]] = $paramsArray[$i+1];
		}
		
		return $params;
	}
	
	public function getCallingFunction()
	{
	    $backtrace = debug_backtrace();
	
	    return $backtrace[2]['function'];
	}

	public function getMagentoVersion() {
		return (int)str_replace(".", "", Mage::getVersion());
    }
    
    public function getBrowserInfo() {
	    $ua = strtolower($_SERVER['HTTP_USER_AGENT']);
	    // you can add different browsers with the same way ..
	    if(preg_match('/(chromium)[ \/]([\w.]+)/', $ua))
	            $browser = 'chromium';
	    elseif(preg_match('/(chrome)[ \/]([\w.]+)/', $ua))
	            $browser = 'chrome';
	    elseif(preg_match('/(safari)[ \/]([\w.]+)/', $ua))
	            $browser = 'safari';
	    elseif(preg_match('/(opera)[ \/]([\w.]+)/', $ua))
	            $browser = 'opera';
	    elseif(preg_match('/(msie)[ \/]([\w.]+)/', $ua))
	            $browser = 'msie';
	    elseif(preg_match('/(mozilla)[ \/]([\w.]+)/', $ua))
	            $browser = 'mozilla';
	
	    preg_match('/('.$browser.')[ \/]([\w]+)/', $ua, $version);
	
	    return array($browser, 'name' => $browser, 'version' => $version[2]);
	}
	
	public function hex2rgb($hex) {
	   $hex = str_replace("#", "", $hex);

	   if(strlen($hex) == 3) {
		  $r = hexdec(substr($hex,0,1).substr($hex,0,1));
		  $g = hexdec(substr($hex,1,1).substr($hex,1,1));
		  $b = hexdec(substr($hex,2,1).substr($hex,2,1));
	   } else {
		  $r = hexdec(substr($hex,0,2));
		  $g = hexdec(substr($hex,2,2));
		  $b = hexdec(substr($hex,4,2));
	   }
	   $rgb = $r.', '.$g.', '.$b;
	   return $rgb;
	}
	
    public function isMobile()
    {    		
    	if(Mage::helper('ajaxcart/mobiledetect')->isMobile() && !Mage::helper('ajaxcart/mobiledetect')->isTablet()) 
		{ 
			return true; 
		} 
		return false; 
    }	 
	
    public function isTablet()
    {    		
    	if(Mage::helper('ajaxcart/mobiledetect')->isTablet()) 
		{ 
			return true; 
		} 
		return false; 
    }		
	
	public function getMaximumProductQty($_product)
	{
		$maxQty = Mage::getStoreConfig('cataloginventory/item_options/max_sale_qty'); 
		if ($_product->getTypeId() != 'configurable' && $_product->getTypeId() != 'bundle') {
			$maxQty = number_format(Mage::getModel('cataloginventory/stock_item')->loadByProduct($_product)->getQty(),0,'',''); 
		}		
		
		$stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($_product);

		if (!$stock->getManageStock() || $stock->getBackorders()!=0){
			$maxQty = 9999999999;   
		}	

		/* $maxQty = $maxQty - $this->getCartQty($_product); */
			
		return $maxQty;
	}
	
	public function getMinimumProductQty($_product)
	{
		$_viewBlock = new Mage_Catalog_Block_Product_View;
		$minQty = $_viewBlock->getProductDefaultQty($_product); 
		if ($_viewBlock->getProductDefaultQty($_product) == '') {
			$minQty = 1; 
		}	
		if (Mage::getModel('cataloginventory/stock_item')->loadByProduct($_product)->getEnableQtyIncrements()) {
			$qtyInc = Mage::getModel('cataloginventory/stock_item')->loadByProduct($_product)->getQtyIncrements();	
			if ($qtyInc>$minQty) {
				$minQty = $qtyInc;
			} else if ($minQty>$qtyInc && $minQty%$qtyInc!=0) {
				$minQty = $qtyInc * (floor($minQty/$qtyInc)+1);				
			}
		}
		
		return $minQty;
	}
	
	public function getQtyIncrements($_product)
	{	
		if (Mage::getModel('cataloginventory/stock_item')->loadByProduct($_product)->getEnableQtyIncrements()) {
			$qtyIncrements = Mage::getModel('cataloginventory/stock_item')->loadByProduct($_product)->getQtyIncrements();	
		} else {
			$qtyIncrements = 1;
		}
		
		return $qtyIncrements;
	}
	
	protected function getBlockNameByType($type) 
	{
		$acBlocks = unserialize(Mage::getSingleton('customer/session')->getAcBlocks());
		
		return $acBlocks[$type];
	}

    //generate block html functions	
    public function getLoginHtml($_controller)
    {
		Mage::app()->getCacheInstance()->cleanType('layout');
        $layout = $_controller->getLayout();
        $update = $layout->getUpdate();
        $update->load('ajaxcart_index_login');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }
	
    public function getLoginLinkHtml($_controller)
    {        
		if (!Mage::getSingleton('customer/session')->isLoggedIn()) {	
            $text = $this->__('Log In');		
		} else {
            $text = $this->__('Log Out');			
		}
        
        return $text;
    }	
     	
    public function getCartHtml($_controller)
    {
	    Mage::app()->getCacheInstance()->cleanType('layout');
        Mage::getSingleton('customer/session')->setAjaxRequest(true);
        $layout = $_controller->getLayout();
        $update = $layout->getUpdate();
        $update->addHandle('default');
		$update->addUpdate('<remove name="messages"/>');
        $update->load();
        $layout->generateXml();
        $layout->generateBlocks();
        
	    $output = array();
	    foreach ($this->getBlockNameByType('checkout/cart_sidebar') as $block) {
	        if ($layout->getBlock($block)) {
		        $output[] = $layout->getBlock($block)->toHtml();
		    }  
	    }
        Mage::getSingleton('customer/session')->setAjaxRequest();
        
        return $output;
    }
    
    public function getCartCountHtml($_controller)
    {
    	Mage::app()->getCacheInstance()->cleanType('layout');
    	Mage::getSingleton('customer/session')->setAjaxRequest(true);
    	
    	$_cartQty = Mage::helper('checkout/cart')->getSummaryCount();
    	if(empty($_cartQty)) {
    		$_cartQty = 0;
    	}
    	
    	$output='<span class="icon"></span>
    	    <span class="label">'.$this->__('Cart').'</span>
    	    <span class="count">'.$_cartQty.'</span>
    	</a>';
    	
    	Mage::getSingleton('customer/session')->setAjaxRequest();
    
    	return $output;
    }
	
    public function getCartLinkHtml($_controller)
    {
        $count = Mage::helper('checkout/cart')->getSummaryCount();
        if ($count == 1) {
            $text = $this->__('Shopping Cart');
        } elseif ($count > 0) {
            $text = $this->__('Shopping Cart');
        } else {
            $text = $this->__('Shopping Cart');
        }
        
        return $text;
    }

    public function getCartPageHtml($_controller)
    {
        Mage::app()->getCacheInstance()->cleanType('layout');
        Mage::getSingleton('customer/session')->setAjaxRequest(true);
        $layout = $_controller->getLayout();
        $update = $layout->getUpdate();
        $update->addHandle('checkout_cart_index');        
        $update->load();
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getBlock($this->getBlockNameByType('checkout/cart'))->toHtml();
        Mage::getSingleton('customer/session')->setAjaxRequest();
        
        return $output;
    }       
	
    public function getOptionsHtml($_controller)
    {
	    Mage::app()->getCacheInstance()->cleanType('layout');
        $layout = $_controller->getLayout();
        $update = $layout->getUpdate();
        $update->load('ajaxcart_index_options');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();

        return $output;
    }	
	
    public function getConfigurableHtml($_controller)
    {
	    Mage::app()->getCacheInstance()->cleanType('layout');
        $layout = $_controller->getLayout();
        $update = $layout->getUpdate();
        $update->load('ajaxcart_index_configurable');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();

        return $output;
    }
	
    public function getBundleHtml($_controller)
    {
	    Mage::app()->getCacheInstance()->cleanType('layout');
        $layout = $_controller->getLayout();
        $update = $layout->getUpdate();
        $update->load('ajaxcart_index_bundle');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();

        return $output;
    }	   

	public function getDownloadableHtml($_controller)
    {
	    Mage::app()->getCacheInstance()->cleanType('layout');
        $layout = $_controller->getLayout();
        $update = $layout->getUpdate();
        $update->load('ajaxcart_index_downloadable');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();

        return $output;
    }	
	
    public function getGroupedHtml($_controller)
    {
	    Mage::app()->getCacheInstance()->cleanType('layout');
        $layout = $_controller->getLayout();
        $update = $layout->getUpdate();
        $update->load('ajaxcart_index_grouped');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();

        return $output;
    }	
	
    public function getWishlistHtml($_controller)
    {
	    Mage::app()->getCacheInstance()->cleanType('layout');
        Mage::getSingleton('customer/session')->setAjaxRequest(true);
        $layout = $_controller->getLayout();
        $update = $layout->getUpdate();
        $update->addHandle('default');
        $update->load();
        $layout->generateXml();
        $layout->generateBlocks();
	    
	    $output = array();
	    foreach ($this->getBlockNameByType('wishlist/customer_sidebar') as $block) {
	        if ($layout->getBlock($block)) {
		        $output[] = $layout->getBlock($block)->toHtml();
		    }  
	    }
	    Mage::getSingleton('customer/session')->setAjaxRequest();
        
        return $output;
    }

    public function getWishlistPageHtml($_controller)
    {
		Mage::helper('wishlist')->getWishlist()->getItemCollection()->clear();
        Mage::app()->getCacheInstance()->cleanType('layout');
        Mage::getSingleton('customer/session')->setAjaxRequest(true);
        $layout = $_controller->getLayout();
        $update = $layout->getUpdate();
        $update->addHandle('wishlist_index_index');        
        $update->load();
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getBlock($this->getBlockNameByType('wishlist/customer_wishlist'))->toHtml();
        Mage::getSingleton('customer/session')->setAjaxRequest();
        
        return $output;
    }   
	
    public function getWishlistLinkHtml($_controller)
    {
        $count = Mage::helper('wishlist')->getItemCount();
        if ($count == 1) {
            $text = $this->__('My Wishlist (%s item)', $count);
        } elseif ($count > 0) {
            $text = $this->__('My Wishlist (%s items)', $count);
        } else {
            $text = $this->__('My Wishlist');
        }
        
        return $text;
    }

    public function getCompareHtml($_controller)
    {
	    Mage::app()->getCacheInstance()->cleanType('layout');
        Mage::getSingleton('customer/session')->setAjaxRequest(true);
        $layout = $_controller->getLayout();
        $update = $layout->getUpdate();
        $update->addHandle('default');
        $update->load();
        $layout->generateXml();
        $layout->generateBlocks();
        
	    $output = array();
	    foreach ($this->getBlockNameByType('catalog/product_compare_sidebar') as $block) {
	        if ($layout->getBlock($block)) {
		        $output[] = $layout->getBlock($block)->toHtml();
		    }  
	    }
	    Mage::getSingleton('customer/session')->setAjaxRequest();
        
        return $output;
    }	

    public function getComparePopupHtml($_controller)
    {
	    Mage::app()->getCacheInstance()->cleanType('layout');
        Mage::getSingleton('customer/session')->setAjaxRequest(true);
        $layout = $_controller->getLayout();
        $update = $layout->getUpdate();
        $update->addHandle('ajaxcart_compare_popup');
        $update->load();
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getBlock($this->getBlockNameByType('catalog/product_compare_list'))->toHtml();
        Mage::getSingleton('customer/session')->setAjaxRequest();
        
        return $output;
    }	
}