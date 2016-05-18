<?php
/*
 * Developer: Rene Voorberg
* Team site: http://cmsideas.net/
* Support: http://support.cmsideas.net/
*
*
*/
class Glace_Ajaxcart_Model_Ajaxcart extends Mage_Core_Model_Abstract
{    
	//add quote layout messages to checkout session
    public function loadQuoteMessages()
    {
		if (Mage::helper('ajaxcart')->getMagentoVersion()>1420 && Mage::helper('ajaxcart')->getMagentoVersion()<1800) {    
			$messages = array();
			$cart = Mage::getSingleton('checkout/cart');
			foreach ($cart->getQuote()->getMessages() as $message) {
				if ($message) {
					// Escape HTML entities in quote message to prevent XSS
					$message->setCode(Mage::helper('core')->escapeHtml($message->getCode()));
					$messages[] = $message;
				}
			}
			$cart->getCheckoutSession()->addUniqueMessages($messages);	
		}
    }
	
	//returns the min/max qty's for all the products on the page
	public function getAjaxCartQtysJs()
	{
		$html = '<script type="text/javascript">					
					var productMinMax = new Array();';
					
		if (Mage::getSingleton('customer/session')->getProductMinMax()) {
			$productMinMax = unserialize(Mage::getSingleton('customer/session')->getProductMinMax());
			if(count($productMinMax)){
				foreach ($productMinMax as $key => $value){
					$html .= 'productMinMax[\''.$key.'\'] = new Array();';
					$html .= 'productMinMax[\''.$key.'\'][\'min\'] = '.$productMinMax[$key]['min'].';';
					$html .= 'productMinMax[\''.$key.'\'][\'max\'] = '.$productMinMax[$key]['max'].';';
					$html .= 'productMinMax[\''.$key.'\'][\'inc\'] = '.$productMinMax[$key]['inc'].';';
				}
			}
		}
		
		//Get Cart items min/max Qtys; Used on shopping cart page
		$siderbarBlock = Mage::app()->getLayout()->getBlockSingleton('checkout/cart_sidebar');
 		$items = $siderbarBlock->getItems();
		if (count($items)){			
			foreach($items as $item){
				$sidebarProduct = Mage::getModel('catalog/product')->load($item->getProductId());
				
				$minQty = Mage::helper('ajaxcart')->getMinimumProductQty($sidebarProduct);
				$maxQty = Mage::helper('ajaxcart')->getMaximumProductQty($sidebarProduct);
				$incQty = Mage::helper('ajaxcart')->getQtyIncrements($sidebarProduct);
				
				$html .= 'productMinMax[\'item'.$item->getId().'\'] = new Array();';
				$html .= 'productMinMax[\'item'.$item->getId().'\'][\'min\'] = '.$minQty.';';
				$html .= 'productMinMax[\'item'.$item->getId().'\'][\'max\'] = '.$maxQty.';';
				$html .= 'productMinMax[\'item'.$item->getId().'\'][\'inc\'] = '.$incQty.';';
			}					
		} 
		
		//Get Wishlist items min/max Qtys; Used on wishlist page
		$siderbarBlock = Mage::app()->getLayout()->getBlockSingleton('wishlist/customer_wishlist');
 		$items = $siderbarBlock->getWishlistItems();
		if (count($items)){			
			foreach($items as $item){
				$sidebarProduct = Mage::getModel('catalog/product')->load($item->getProductId());
				
				$minQty = Mage::helper('ajaxcart')->getMinimumProductQty($sidebarProduct);
				$maxQty = Mage::helper('ajaxcart')->getMaximumProductQty($sidebarProduct);
				$incQty = Mage::helper('ajaxcart')->getQtyIncrements($sidebarProduct);
				
				$html .= 'productMinMax[\'witem'.$item->getId().'\'] = new Array();';
				$html .= 'productMinMax[\'witem'.$item->getId().'\'][\'min\'] = '.$minQty.';';
				$html .= 'productMinMax[\'witem'.$item->getId().'\'][\'max\'] = '.$maxQty.';';
				$html .= 'productMinMax[\'witem'.$item->getId().'\'][\'inc\'] = '.$incQty.';';
			}					
		} 
		
		// Get Product min/max qty
		$product = Mage::registry('product');
		if ($product && $product->getId()){
    		$helper = Mage::helper('ajaxcart');
			$html .= 'productMinMax[\''.$product->getId().'\'] = new Array();';
			$html .= 'productMinMax[\''.$product->getId().'\'][\'min\'] = '.$helper->getMinimumProductQty($product).';';
			$html .= 'productMinMax[\''.$product->getId().'\'][\'max\'] = '.$helper->getMaximumProductQty($product).';';
			$html .= 'productMinMax[\''.$product->getId().'\'][\'inc\'] = '.$helper->getQtyIncrements($product).';';		
		} 
		
		$html .= '</script>';	
		//Mage::getSingleton('customer/session')->setProductMinMax();
		
		return $html;
	}		
	
	//returns the min/max qty's for products from the Compare list page
	/*
public function getCompareListQtysJs()
	{
		$html = '<script type="text/javascript">';
 		$_category = new Mage_Catalog_Block_Product_Compare_List;
 		$_productCollection = $_category->getItems();
		if(count($_productCollection)){
			foreach ($_productCollection as $_listProd){
				$_listProduct = Mage::getModel('catalog/product')->load($_listProd->getId());
				$html .= 'productMinMax[\''.$_listProduct->getId().'\'] = new Array();';
				$html .= 'productMinMax[\''.$_listProduct->getId().'\'][\'min\'] = '.$this->getMinimumProductQty($_listProduct).';';
				$html .= 'productMinMax[\''.$_listProduct->getId().'\'][\'max\'] = '.$this->getMaximumProductQty($_listProduct).';';	
			}
		}
		
		$html .= '</script>';
		
		return $html;
	}
*/		
	
	//DEPRECATED FUNCTION
	//get the product qty that has been added to the cart
	public function getCartQty($_product)
	{
		$items = Mage::getModel('checkout/cart')->getItems();
		$qty = 0;
		foreach ($items as $item){
			if ($item->getProductId() == $_product->getId()){
				$qty = $qty + $item->getQty();
			}
		}
		
		return $qty;
	}	
	
}