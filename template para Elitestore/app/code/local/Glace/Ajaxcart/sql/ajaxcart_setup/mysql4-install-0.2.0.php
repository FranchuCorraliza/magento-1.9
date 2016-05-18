<?php
/*
 * Developer: Rene Voorberg
* Team site: http://cmsideas.net/
* Support: http://support.cmsideas.net/
*
*
*/
$installer = $this;

$installer->startSetup();

$installer->setConfigData('ajaxcart/configuration/enabled', 										0);

$installer->setConfigData('ajaxcart/loader_configuration/vitualimage1',  									'#54ABB4');
$installer->setConfigData('ajaxcart/loader_configuration/vitualimage2',  									'#96E5EE');
$installer->setConfigData('ajaxcart/loader_configuration/vitualimage3',  									'#DEDEDE');

$installer->setConfigData('ajaxcart/popup_configuration/notification_popup',  						1);
$installer->setConfigData('ajaxcart/popup_configuration/autohide_notification_popup',  				'7');
$installer->setConfigData('ajaxcart/popup_configuration/notification_popup_bkg',  					'#FFFFFF');
$installer->setConfigData('ajaxcart/popup_configuration/enable_notification_popup_wrapper_bkg', 	0);
$installer->setConfigData('ajaxcart/popup_configuration/notification_popup_wrapper_bkg', 			'#FFFFFF');
$installer->setConfigData('ajaxcart/popup_configuration/notification_popup_bodersize',  			1);
$installer->setConfigData('ajaxcart/popup_configuration/notification_popup_bodercolor',  			'#E6E6E6');
$installer->setConfigData('ajaxcart/popup_configuration/options_popup_width',  						'500');
$installer->setConfigData('ajaxcart/popup_configuration/success_popup_width',  						'400');

$installer->setConfigData('ajaxcart/qty_configuration/qty_button_bkg_color',  						'#F16022');
$installer->setConfigData('ajaxcart/qty_configuration/qty_button_text_color',  						'#FFFFFF');
$installer->setConfigData('ajaxcart/qty_configuration/show_qty_in_categorypage',  					1);
$installer->setConfigData('ajaxcart/qty_configuration/qty_buttons_in_categorypage', 				1);
$installer->setConfigData('ajaxcart/qty_configuration/qty_buttons_in_productpage',  				1);
$installer->setConfigData('ajaxcart/qty_configuration/show_qty_in_cartsidebar',  					1);
$installer->setConfigData('ajaxcart/qty_configuration/qty_buttons_in_cartsidebar',  				1);
$installer->setConfigData('ajaxcart/qty_configuration/qty_buttons_in_popup',  						1);
$installer->setConfigData('ajaxcart/qty_configuration/qty_buttons_in_cartpage',  					1);
$installer->setConfigData('ajaxcart/qty_configuration/qty_buttons_in_wishlist',  					1);

$installer->setConfigData('ajaxcart/dragdrop/enable_category_dragdrop',  							1);
$installer->setConfigData('ajaxcart/dragdrop/dragme_text',  										'DRAG ME');
$installer->setConfigData('ajaxcart/dragdrop/dragme_text_color',  									'#FFFFFF');
$installer->setConfigData('ajaxcart/dragdrop/dragme_text_bkg',  									'#FF3000');
$installer->setConfigData('ajaxcart/dragdrop/drop_effect',  										'shrink');
$installer->setConfigData('ajaxcart/dragdrop/droppable_highlight_area_color',  						'#FC5831');
$installer->setConfigData('ajaxcart/dragdrop/tooltip_enable',  										1);
$installer->setConfigData('ajaxcart/dragdrop/tooltip_cart_text',  									'BUY ME');
$installer->setConfigData('ajaxcart/dragdrop/tooltip_compare_text',  								'COMPARE ME');
$installer->setConfigData('ajaxcart/dragdrop/tooltip_wishlist_text',  								'WISH ME');
$installer->setConfigData('ajaxcart/dragdrop/tooltip_text',  										'#FFFFFF');
$installer->setConfigData('ajaxcart/dragdrop/tooltip_bkg',  										'#FC4A26');

//ultimo theme specific configuration
$installer->setConfigData('ajaxcart/ultimo_configuration/cart_sidebar_left',  		0);
$installer->setConfigData('ajaxcart/ultimo_configuration/cart_sidebar_right',  		0);
$installer->setConfigData('ajaxcart/ultimo_configuration/compare_sidebar_left',  	0);
$installer->setConfigData('ajaxcart/ultimo_configuration/compare_sidebar_right',  	0);

$installer->endSetup(); 