<?php
/*
 * Developer: Rene Voorberg
* Team site: http://cmsideas.net/
* Support: http://support.cmsideas.net/
*
*
*/
class Glace_Ajaxcart_Block_Ajaxcart extends Mage_Checkout_Block_Onepage_Abstract
{    	
    //returns the ajaxcart initialize url with all the add to cart parameters
    public function getInitUrl()
    {
		return Mage::helper('ajaxcart')->getInitUrl();
    }   
	
	//returns the min/max qty's for products from the product page, options popup and cart sidebar items
	public function getAjaxCartQtysJs()
	{
		return Mage::getModel('ajaxcart/ajaxcart')->getAjaxCartQtysJs();	
	}
	
	public function getLocation() {
		if (Mage::app()->getRequest()->getControllerName() == 'product_compare' && Mage::app()->getRequest()->getActionName() == 'index') {
			return 'setPLocation';
		} else {
			return 'setLocation';
		}
	}
		
	public function getAjaxCartConfig()
	{			
		//LOAD JS CONFIG		
		//Preload loader image
		$html = '<script type="text/javascript">
					//<![CDATA[
					//Preload loader image
					var images = new Array();
					images[0] = new Image();
					images[0].src = "'.$this->getSkinUrl('ajaxcart/images/opc-ajax-loader.gif').'";';	
		
		//load global variables	
		$html .= 'var dragDropProducts = new Array();';
				
		//set ajaxcart login config	
		$isLogged = (Mage::helper('customer')->isLoggedIn()) ? 'true' : 'false';
		$html .= 'ajaxcartLogin.isLoggedIn = '.$isLogged.';
				  ajaxcartLogin.defaultWelcomeMsg = \''.str_replace("'","\'",Mage::getStoreConfig('design/header/welcome')).'\';';
				  
		//set ajaxcart config	
		$rgbNotificationBkgColor = (Mage::getStoreConfig('ajaxcart/popup_configuration/notification_popup_wrapper_bkg')) ? Mage::helper('ajaxcart')->hex2rgb(Mage::getStoreConfig('ajaxcart/popup_configuration/notification_popup_wrapper_bkg')) : '0, 0, 0';
		$rgbBoxShadowColor = (Mage::getStoreConfig('ajaxcart/dragdrop/dragme_text_bkg')) ? Mage::helper('ajaxcart')->hex2rgb(Mage::getStoreConfig('ajaxcart/dragdrop/dragme_text_bkg')) : '0, 0, 0';
		$rgbTooltipBkgColor = (Mage::getStoreConfig('ajaxcart/dragdrop/tooltip_bkg')) ? Mage::helper('ajaxcart')->hex2rgb(Mage::getStoreConfig('ajaxcart/dragdrop/tooltip_bkg')) : '0, 0, 0';

		//mobile/tablet notification
		if (Mage::getStoreConfig('ajaxcart/dragdrop/enable_category_dragdrop') && (Mage::helper('ajaxcart')->isMobile() || Mage::helper('ajaxcart')->isTablet()) && !Mage::getModel('core/cookie')->get('ajaxcart-mobile-notification')) {
			Mage::getModel('core/cookie')->set('ajaxcart-mobile-notification', true, 86400);
			$html .= 'alert(\''.$this->__('Drag a product to the cart, it\'s fun!').'\');';
		}

		$html .= 'var ajaxcart = new Ajaxcart({
					urls: {
						initialize: \''.$this->getInitUrl().'\',
						updateCart: \''.$this->getUrl('ajaxcart/cart/updatePost', array('form_key'=>Mage::getSingleton('core/session')->getFormKey())).'\',
						clearCart: \''.$this->getUrl('ajaxcart/cart/clearCart').'\',
						addWishlistItemToCart: \''.$this->helper('wishlist')->getAddToCartUrl('%item%').'\',
						addAllItemsToCart: \''.$this->getUrl('ajaxcart/wishlist/allcart', array('wishlist_id' => Mage::helper('wishlist')->getWishlist()->getId())).'\'
					},
					configuration: {
						show_notification: \''.Mage::getStoreConfig('ajaxcart/popup_configuration/notification_popup').'\',
						autohide_notification_time: \''.Mage::getStoreConfig('ajaxcart/popup_configuration/autohide_notification_popup').'\',
						notification_bkg: \''.Mage::getStoreConfig('ajaxcart/popup_configuration/enable_notification_popup_wrapper_bkg').'\',
						notification_wrapper_bkg: \''.$rgbNotificationBkgColor.'\',
						box_shadow_color: \''.$rgbBoxShadowColor.'\',
						is_mobile: '.var_export(Mage::helper('ajaxcart')->isMobile(), true).',
						is_tablet: '.var_export(Mage::helper('ajaxcart')->isTablet(), true).'
					},
					qtys: {
						category_qty: \''.Mage::getStoreConfig('ajaxcart/qty_configuration/show_qty_in_categorypage').'\',
						category_qty_buttons: \''.Mage::getStoreConfig('ajaxcart/qty_configuration/qty_buttons_in_categorypage').'\',				
						product_qty_buttons: \''.Mage::getStoreConfig('ajaxcart/qty_configuration/qty_buttons_in_productpage').'\',
						popup_qty_buttons: \''.Mage::getStoreConfig('ajaxcart/qty_configuration/qty_buttons_in_popup').'\',
						cartpage_qty_buttons: \''.Mage::getStoreConfig('ajaxcart/qty_configuration/qty_buttons_in_cartpage').'\',
						sidebar_qty: \''.Mage::getStoreConfig('ajaxcart/qty_configuration/show_qty_in_cartsidebar').'\',
						wishlist_qty_buttons: \''.Mage::getStoreConfig('ajaxcart/qty_configuration/qty_buttons_in_wishlist').'\'
					},
					dragdrop: {
						dragdrop_enable_category : \''.Mage::getStoreConfig('ajaxcart/dragdrop/enable_category_dragdrop').'\',
						dragdrop_drop_effect : \''.Mage::getStoreConfig('ajaxcart/dragdrop/drop_effect').'\',
						tooltip_enable : \''.Mage::getStoreConfig('ajaxcart/dragdrop/tooltip_enable').'\'
					}
				});
				
				ajaxcart.ajaxCartLoadingHtml = \'<span id="ajax-cart-please-wait" class="please-wait" ><img src="'.$this->getSkinUrl('ajaxcart/images/opc-ajax-loader.gif').'" alt="'.$this->__('Please wait...').'" title="'.$this->__('Please wait...').'" class="v-middle" /><span>'.$this->__('Please wait...').'</span></span>\';
				
				Translator.add(\'The requested quantity is not available.\',\''.$this->__('The requested quantity is not available.').'\');
				Translator.add(\'The minimum quantity allowed for purchase is \',\''.$this->__('The minimum quantity allowed for purchase is ').'\');
				Translator.add(\'The maximum quantity allowed for purchase is \',\''.$this->__('The maximum quantity allowed for purchase is ').'\');
				Translator.add(\'Are you sure you would like to remove this item from the shopping cart?\',\''.$this->__('Are you sure you would like to remove this item from the shopping cart?').'\');
				Translator.add(\'Are you sure you would like to remove this item from the wishlist?\',\''.$this->__('Are you sure you would like to remove this item from the wishlist?').'\');
				Translator.add(\'Update Cart\',\''.$this->__('Update Cart').'\');
				Translator.add(\'Empty Cart\',\''.$this->__('Empty Cart').'\');
				Translator.add(\'Are you sure you would like to remove all the items from the shopping cart?\',\''.$this->__('Are you sure you would like to remove all the items from the shopping cart?').'\');
				Translator.add(\'Update Wishlist\',\''.$this->__('Update Wishlist').'\');				
				
				Translator.add(\'Drag me\',\''.Mage::getStoreConfig('ajaxcart/dragdrop/dragme_text').'\');
				Translator.add(\'Buy Me!\',\''.Mage::getStoreConfig('ajaxcart/dragdrop/tooltip_cart_text').'\');
				Translator.add(\'Wish Me!\',\''.Mage::getStoreConfig('ajaxcart/dragdrop/tooltip_wishlist_text').'\');
				Translator.add(\'Compare Me!\',\''.Mage::getStoreConfig('ajaxcart/dragdrop/tooltip_compare_text').'\');
			//]]>
			</script>';
			
		//LOAD THEME SPECIFIC CONFIG
		//set default values
		$html .= '<script type="text/javascript">
					var disablePopupProductLoader = false;
					var cartLink = false;
					var wishlistLink = false;
					function dispatchBlockUpdates(response){}
					function dispatchButtonUpdates(button, onClick){}
					function dispatchLinkUpdates(button, onClick){}
					function dispatchLiveUpdates(type){}
				  </script>';	
				  				 
		if (Mage::getSingleton('core/design_package')->getTheme('default')=='groupdeals') {
			$html .= '<script type="text/javascript" src="'.$this->getSkinUrl('ajaxcart/themes/gdtheme.js').'"></script>';
			$html .= '<link rel="stylesheet" type="text/css" href="'.$this->getSkinUrl('ajaxcart/themes/gdtheme.css').'" />';
		} else if (Mage::getSingleton('core/design_package')->getPackageName()=='ultimo') {
			$html .= '<script type="text/javascript" src="'.$this->getSkinUrl('ajaxcart/themes/ultimo.js').'"></script>';
			$html .= '<link rel="stylesheet" type="text/css" href="'.$this->getSkinUrl('ajaxcart/themes/ultimo.css').'" />';
		}
		
		//LOAD EXTENSION SPECIFIC CONFIG
	    if (Mage::helper('core')->isModuleEnabled('Glace_Groupdeals') && Mage::helper('groupdeals')->isEnabled()) {
			$html .= '<script type="text/javascript" src="'.$this->getSkinUrl('ajaxcart/extensions/groupdeals.js').'"></script>';
			$html .= '<link rel="stylesheet" type="text/css" href="'.$this->getSkinUrl('ajaxcart/extensions/groupdeals.css').'" />';			
		}	
		
		//UPDATE AJAXCART BLOCKS
		$html .= '<script type="text/javascript">ajaxcart.updateAjaxCartBlocks();</script>';	

		//LOAD CSS CONFIG
		$html .= '<style>
					.popup-container .popup-content { 
						background:'.Mage::getStoreConfig('ajaxcart/popup_configuration/notification_popup_bkg').' !important;
						width:80%;
						max-width:'.Mage::getStoreConfig('ajaxcart/popup_configuration/options_popup_width').'px;
						border:'.Mage::getStoreConfig('ajaxcart/popup_configuration/notification_popup_bodersize').'px solid '.Mage::getStoreConfig('ajaxcart/popup_configuration/notification_popup_bodercolor').' !important;
					}
					#ajaxcart-login-popup-content { width:50%; max-width:310px; } 					
					#success-popup-content.popup-content { width:80%; max-width:'.Mage::getStoreConfig('ajaxcart/popup_configuration/success_popup_width').'px; }
					#ajaxcart-loading-popup-container.popup-container .popup-content { background:none !important; border:0 !important; }
					.popup-container .popup-content .ac-page-title { width:110%; max-width:'.(Mage::getStoreConfig('ajaxcart/popup_configuration/options_popup_width') + 40).'px }
					
					.qty-control-box button.increase { background:'.Mage::getStoreConfig('ajaxcart/qty_configuration/qty_button_bkg_color').' !important; }
					.qty-control-box button.decrease { background:'.Mage::getStoreConfig('ajaxcart/qty_configuration/qty_button_bkg_color').' !important; }	
					.qty-control-box button.increase span{ color:'.Mage::getStoreConfig('ajaxcart/qty_configuration/qty_button_text_color').' !important; background:'.Mage::getStoreConfig('ajaxcart/qty_configuration/qty_button_increase_bkg_color').' !important; }
					.qty-control-box button.decrease span{ color:'.Mage::getStoreConfig('ajaxcart/qty_configuration/qty_button_text_color').' !important; background:'.Mage::getStoreConfig('ajaxcart/qty_configuration/qty_button_decrease_bkg_color').' !important; }
					
					.draggable-over { border:1px solid '.Mage::getStoreConfig('ajaxcart/dragdrop/droppable_highlight_area_color').'; box-shadow: 0 0 4px '.Mage::getStoreConfig('ajaxcart/dragdrop/droppable_highlight_area_color').'; }
					.draggable-bkg .draggable-text { color:'.Mage::getStoreConfig('ajaxcart/dragdrop/dragme_text_color').' !important; }
					
					.tooltip-sidebar { background:rgba('.$rgbTooltipBkgColor.', 1) !important; color:'.Mage::getStoreConfig('ajaxcart/dragdrop/tooltip_text').' !important; }
					.tooltip-sidebar:after { border-color:rgba('.$rgbTooltipBkgColor.', 1) transparent transparent !important; }

					#vitualimage-1 { background: '.Mage::getStoreConfig('ajaxcart/loader_configuration/vitualimage1').' !important; }
					#vitualimage-2 { background: '.Mage::getStoreConfig('ajaxcart/loader_configuration/vitualimage2').' !important; }
					#vitualimage-3 { background: '.Mage::getStoreConfig('ajaxcart/loader_configuration/vitualimage3').' !important; }';	
		$html .= '</style>';
			
		return $html;
	}	
}