
<?php
$this->startSetup();
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);


$staticBlock = array(
                'title' => 'Customer Service',
                'identifier' => 'rt001-footer-box1',                   
                'content' => '<ul>
                	<li><a href="{{store url="customer-service"}}">Customer Service</a></li>
                	<li><a href="{{store url="sales/guest/form"}}">Orders and Returns</a></li>
                	<li><a href="{{store url="customer-service"}}">Shipping and Delivery</a></li>
                	<li><a href="{{store url="catalog/seo_sitemap/category"}}">Sitemap</a></li>
                </ul>',
                'is_active' => 1,                   
                'stores' => array(0)
                );
Mage::getModel('cms/block')->setData($staticBlock)->save();

$staticBlock = array(
                'title' => 'About Us',
                'identifier' => 'rt001-footer-box2',                   
                'content' => '<ul>
                	<li><a href="{{store url="about-magento-demo-store"}}">About Us</a></li>
                	<li><a href="{{store url="privacy-policy-cookie-restriction-mode"}}">Privacy Policy</a></li>
                	<li><a href="{{store url="contacts"}}">Contact Us</a></li>
                	<li><a href="{{store url="customer-service"}}">Shopping Infos</a></li>	
                </ul>',
                'is_active' => 1,                   
                'stores' => array(0)
                );
Mage::getModel('cms/block')->setData($staticBlock)->save();

$staticBlock = array(
                'title' => 'New Products',
                'identifier' => 'rt001-footer-box3',                   
                'content' => '<ul>
                	<li><a href="{{store url="#"}}">Textlink</a></li>
                	<li><a href="{{store url="#"}}">Textlink</a></li>
                	<li><a href="{{store url="#"}}">Textlink</a></li>
                	<li><a href="{{store url="#"}}">Textlink</a></li>
                </ul>',
                'is_active' => 1,                   
                'stores' => array(0)
                );
Mage::getModel('cms/block')->setData($staticBlock)->save();

$staticBlock = array(
                'title' => 'Popular',
                'identifier' => 'rt001-footer-box4',                   
                'content' => '<ul>
                	<li><a href="{{store url="#"}}">Textlink</a></li>
                	<li><a href="{{store url="#"}}">Textlink</a></li>
                	<li><a href="{{store url="#"}}">Textlink</a></li>
                	<li><a href="{{store url="#"}}">Textlink</a></li>
                </ul>',
                'is_active' => 1,                   
                'stores' => array(0)
                );
Mage::getModel('cms/block')->setData($staticBlock)->save();

$staticBlock = array(
                'title' => 'Left Collout Banners',
                'identifier' => 'left_collout_banner',                   
                'content' => '
                <ul>
                <li><a href="{{store url="furniture.phtml"}}"> 
                <img src="{{media url="wysiwyg/img/col_left_callout.jpg"}}" alt="Image1" /></a></li>
                <li><img src="{{media url="wysiwyg/img/ph_callout_left_rebel.jpg"}}" alt="Image2" /></li>
                <li><img src="{{media url="wysiwyg/img/col_left_callout.jpg"}}" alt="Image3" /></li>
                </ul>',
                'is_active' => 1,                   
                'stores' => array(0)
                );
Mage::getModel('cms/block')->setData($staticBlock)->save();

$staticBlock = array(
                'title' => 'Right Collout Banners',
                'identifier' => 'right_collout_banner',                
                'content' => '
                <ul>
                <li><a href="{{store url="apparel.html"}}"> 
                <img src="{{media url="wysiwyg/img/ph_callout_left_rebel.jpg"}}" alt="Image1" /></a></li>
                <li><img src="{{media url="wysiwyg/img/col_left_callout.jpg"}}" alt="Image2" /></li>
                <li><img src="{{media url="wysiwyg/img/ph_callout_left_rebel.jpg"}}" alt="Image3" /></li>
                </ul>',
                'is_active' => 1,                   
                'stores' => array(0)
                );
Mage::getModel('cms/block')->setData($staticBlock)->save();

$staticBlock = array(
                'title' => 'Category Banners',
                'identifier' => 'kartparadigm_category_banners',                
                'content' => '
<table style="width: 100%; height: auto;" border="0" cellspacing="10">
<tbody>
<tr>
<td><a href="{{store url="electronics/cell-phones.html"}}">
<img style="padding: 10;" src="{{media url="wysiwyg/img/electronics_cellphones.jpg"}}" alt="Image1" width="90%" />
</a></td>
<td><a href="{{store url="electronics/cameras.html"}}">
<img style="padding: 10;" src="{{media url="wysiwyg/img/electronics_digitalcameras.jpg"}}" alt="Image2" width="90%" />
</a></td>
<td><a href="{{store url="electronics/computers.html"}}">
<img style="padding: 10;" src="{{media url="wysiwyg/img/electronics_laptops.jpg"}}" alt="Image3" width="90%" />
</a></td>
</tr>
</tbody>
</table>',
                'is_active' => 1,                   
                'stores' => array(0)
                );
Mage::getModel('cms/block')->setData($staticBlock)->save();

$staticBlock = array(
                'title' => 'Custom Banners  Slider',
                'identifier' => 'custom_banners_slider',                
                'content' => '
                <div id="custom_slider" class="owl-carousel owl-theme">
<div class="item"><a href="#"> <img src="{{media url="wysiwyg/img/col_left_callout.jpg"}}" alt="Image1" /></a></div>
<div class="item"><a href="#"> <img src="{{media url="wysiwyg/img/ph_callout_left_rebel.jpg"}}" alt="Image2" /></a></div>
<div class="item"><a href="#"> <img src="{{media url="wysiwyg/img/col_left_callout.jpg"}}" alt="Image3" /></a></div>
</div>',
                'is_active' => 1,                   
                'stores' => array(0)
                );
Mage::getModel('cms/block')->setData($staticBlock)->save();

Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
$cmsPageData = array(
    'title' => 'Kartparadigm Home Page',
    'root_template' => 'two_columns_right',
    'meta_keywords' => 'meta,keywords',
    'meta_description' => 'meta description',
    'identifier' => 'kartparadigm-home',
    'is_active' => 1,
    'sort_order' => 0,
    'stores' => array(0),//available for all store views
    'content' => '<h2>Featured Products</h2>{{block type="catalog/product_list" column_count="3" category_id="5" template="catalog/product/list.phtml"}}<br/><hr style="width="100%" />
    {{widget type="catalog/product_widget_new" display_type="all_products" products_count="10" template="catalog/product/widget/new/content/new_grid.phtml"}}'
);

Mage::getModel('cms/page')->setData($cmsPageData)->save();

$this->endSetup();
?>
