<?php
/*
 * Developer: Rene Voorberg
* Team site: http://cmsideas.net/
* Support: http://support.cmsideas.net/
*
*
*/
class Glace_Ajaxcart_Model_Observer
{
	public function updateBlocksBefore($observer)
	{		
        $block = $observer->getEvent()->getBlock();
        if ($block->getType() == 'checkout/cart') {
        	Mage::getSingleton('customer/session')->setAjaxCartAction('cart');
        }
        if ($block->getType() == 'checkout/cart_sidebar') {
        	Mage::getSingleton('customer/session')->setAjaxCartAction('cart-sidebar');
        }
        if ($block->getType() == 'wishlist/customer_sidebar') {
        	Mage::getSingleton('customer/session')->setAjaxCartAction('wishlist-sidebar');
        }
        if ($block->getType() == 'catalog/product_compare_sidebar') {
        	Mage::getSingleton('customer/session')->setAjaxCartAction('compare-sidebar');
        }        
        if ($block->getNameInLayout() == 'head' && Mage::getStoreConfig('ajaxcart/configuration/enabled') && Mage::getStoreConfig('ajaxcart/dragdrop/enable_category_dragdrop')) { 
        	$block->setAllowDragAndDrop(true);
        }       
	}
	
	public function updateBlocksAfter($observer)
	{		
        $block = $observer->getEvent()->getBlock();
        $_transportObject = $observer->getEvent()->getTransport();
        $html = $_transportObject->getHtml();
        
        if (Mage::getSingleton('customer/session')->getAcBlocks()) {
    		$acBlocks = unserialize(Mage::getSingleton('customer/session')->getAcBlocks());
    	} else {
			$acBlocks = array();
		}
		
        if ($block->getType() == 'checkout/cart') {        
        	$acBlocks['checkout/cart'] = $block->getNameInLayout();
        	
        	if (!Mage::getSingleton('customer/session')->getAjaxRequest()) {
        		$html = '<div id="ac-cart">'.$html.'</div>';
				$_transportObject->setHtml($html);
        	}
        	
			Mage::getSingleton('customer/session')->setAjaxCartAction();
        } else if ($block->getType() == 'checkout/cart_sidebar') {
        	if (!is_array($acBlocks['checkout/cart_sidebar'])) {
	        	$acBlocks['checkout/cart_sidebar'] = array();
        	}
        	if (!in_array($block->getNameInLayout(), $acBlocks['checkout/cart_sidebar'], true)) {
	        	$i = count($acBlocks['checkout/cart_sidebar']);
	        	$acBlocks['checkout/cart_sidebar'][$i] = $block->getNameInLayout();      
	        } else {
		        $i = array_search($block->getNameInLayout(), $acBlocks['checkout/cart_sidebar']);
	        }
	        
	        if (!Mage::getSingleton('customer/session')->getAjaxRequest()) {
	        	$html = '<div class="ac-cart-sidebar" id="ac-cart-sidebar'.$i.'">'.$html.'</div>';
				$_transportObject->setHtml($html);
	        }
	        
			Mage::getSingleton('customer/session')->setAjaxCartAction();
        } else if ($block->getType() == 'wishlist/customer_sidebar') {    
            if (!in_array($block->getNameInLayout(), $acBlocks['wishlist/customer_sidebar'], true)) {
	        	$i = count($acBlocks['wishlist/customer_sidebar']);
	        	$acBlocks['wishlist/customer_sidebar'][$i] = $block->getNameInLayout();      
	        } else {
		        $i = array_search($block->getNameInLayout(), $acBlocks['wishlist/customer_sidebar']);
	        }	    
        	
        	if ((!Mage::getSingleton('customer/session')->getAjaxRequest() || Mage::getModel('customer/session')->getIsFirstLogin()) && Mage::helper('customer')->isLoggedIn()) {
        		$html = '<div class="ac-wishlist-sidebar" id="ac-wishlist-sidebar'.$i.'">'.$html.'</div>';
				$_transportObject->setHtml($html);
        	}
        	
			Mage::getSingleton('customer/session')->setAjaxCartAction();
			Mage::getSingleton('customer/session')->setIsFirstLogin();
        } else if ($block->getType() == 'catalog/product_compare_sidebar') { 
        	if (!in_array($block->getNameInLayout(), $acBlocks['catalog/product_compare_sidebar'], true)) {
	        	$i = count($acBlocks['catalog/product_compare_sidebar']);
	        	$acBlocks['catalog/product_compare_sidebar'][$i] = $block->getNameInLayout();      
	        } else {
		        $i = array_search($block->getNameInLayout(), $acBlocks['catalog/product_compare_sidebar']);
	        }	        
        	
        	if (!Mage::getSingleton('customer/session')->getAjaxRequest()) {
        		$html = '<div class="ac-compare-sidebar" id="ac-compare-sidebar'.$i.'">'.$html.'</div>';
				$_transportObject->setHtml($html);
        	}
        	
			Mage::getSingleton('customer/session')->setAjaxCartAction();
        } else if ($block->getType() == 'catalog/product_list' || $block->getType() == 'catalog/product_new' || $block->getType() == 'multipledeals/list' || $block->getType() == 'multipledeals/recent' || $block->getType() == 'groupdeals/product_list') {	        
        	$html = '<div class="ac-product-list">'.$html.'</div>';
        	$_transportObject->setHtml($html);
        } else if ($block->getType() == 'page/template_links' ) {	        
        	$html = '<div id="ac-links">'.$html.'</div>';
        	$_transportObject->setHtml($html);
        } else if ($block->getType() == 'catalog/product_compare_list' ) {	        
        	$acBlocks['catalog/product_compare_list'] = $block->getNameInLayout();
        	
        	if (!Mage::getSingleton('customer/session')->getAjaxRequest()) {
        		$html = '<div id="ac-compare-popup">'.$html.'</div>';
				$_transportObject->setHtml($html);
        	}
        	
			Mage::getSingleton('customer/session')->setAjaxCartAction();
        } else if ($block->getType() == 'wishlist/customer_wishlist') {
	        $acBlocks['wishlist/customer_wishlist'] = $block->getNameInLayout();
        	
        	if (!Mage::getSingleton('customer/session')->getAjaxRequest()) {
        		$html = '<div id="ac-wishlist">'.$html.'</div>';
				$_transportObject->setHtml($html);
        	}
        	
			Mage::getSingleton('customer/session')->setAjaxCartAction();
        }
		
		Mage::getSingleton('customer/session')->setAcBlocks(serialize($acBlocks));
	}
	
	//saves the min/max qty's of all the products in every collection in a session
	public function setProductMinMax($observer)
    {
    	$helper = Mage::helper('ajaxcart');
    	if (Mage::getSingleton('customer/session')->getProductMinMax()) {
    		$productMinMax = unserialize(Mage::getSingleton('customer/session')->getProductMinMax());
    	} else {
			$productMinMax = array();
		}
		
    	$products = $observer->getEvent()->getCollection();	
        Mage::getModel('cataloginventory/stock')->addItemsToProducts($products);	
        
    	foreach ($products as $product) {
			$productMinMax[$product->getId()] = array();
    		$productMinMax[$product->getId()]['min'] = $helper->getMinimumProductQty($product);
    		$productMinMax[$product->getId()]['max'] = $helper->getMaximumProductQty($product);
    		$productMinMax[$product->getId()]['inc'] = $helper->getQtyIncrements($product);
		}
		
		Mage::getSingleton('customer/session')->setProductMinMax(serialize($productMinMax));
	}
	
	//save the options of the configurable products when updating them on the shopping cart page
	public function updateCustomOptions($observer)
	{
		$_this = $observer->cart;
		$data = $observer->info;
					Mage::getSingleton('core/session')->setTest($data);
		foreach ($data as $itemId => $itemInfo) {
			$item = $_this->getQuote()->getItemById($itemId);
			if (!$item) continue;
			if (!isset($itemInfo['option']) or empty($itemInfo['option'])) continue;
			
			$confProduct = Mage::getModel('catalog/product')->load($item->getProductId());
			$child = Mage::getModel('catalog/product_type_configurable')->getProductByAttributes($itemInfo['option'],$confProduct);

			foreach ($item->getOptions() as $option){
				if ($option->getCode()=='info_buyRequest'){
					$unserialized = unserialize($option->getValue());
					$unserialized['super_attribute'] = $itemInfo['option'];
					$option->setValue(serialize($unserialized));
				} elseif ($option->getCode()=='attributes'){
					$option->setValue(serialize($itemInfo['option']));
				} elseif (substr($option->getCode(),0,12)=='product_qty_') {		
					$option->setProductId($child->getId());	
					$option->setCode('product_qty_'.$child->getId());									
				} elseif ($option->getCode()=='simple_product') {		
					$option->setProductId($child->getId());	
					$option->setValue($child->getId());									
				}
			}			
			
			$item->setProductOptions($options);
			$item->save();
		}
	}		
}
