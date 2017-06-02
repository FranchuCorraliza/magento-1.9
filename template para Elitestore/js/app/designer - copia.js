// Transformación Layer Navigation Catálogo
jQuery(document).ready(function() {
var div = jQuery('#layered-navigation-container');
var start = jQuery(div).offset().top;
	
jQuery('#filter-gender-title').click(function(){
	if(jQuery('#filter-gender-title').hasClass('filter-title-expanded')){
		jQuery('#filter-gender-title').removeClass('filter-title-expanded');
		jQuery('#filter-gender-title').addClass('filter-title-collapsed');
		jQuery('#filter-gender-content').slideUp("slow");
	}
	else{
		jQuery('#filter-gender-title').addClass('filter-title-expanded');
		jQuery('#filter-gender-title').removeClass('filter-title-collapsed');
		jQuery('#filter-gender-content').slideDown("slow");
	}
});	

jQuery('#filter-category-title').click(function(){
	if(jQuery('#filter-category-title').hasClass('filter-title-expanded')){
		jQuery('#filter-category-title').removeClass('filter-title-expanded');
		jQuery('#filter-category-title').addClass('filter-title-collapsed');
		jQuery('#filter-category-content').slideUp("slow");
	}
	else{
		jQuery('#filter-category-title').addClass('filter-title-expanded');
		jQuery('#filter-category-title').removeClass('filter-title-collapsed');
		jQuery('#filter-category-content').slideDown("slow");
	}
});	
	
jQuery('#filter-alfabeto-title').click(function(){
	if(jQuery('#filter-alfabeto-title').hasClass('filter-title-expanded')){
		jQuery('#filter-alfabeto-title').removeClass('filter-title-expanded');
		jQuery('#filter-alfabeto-title').addClass('filter-title-collapsed');
		jQuery('#filter-alfabeto-content').slideUp("slow");
	}
	else{
		jQuery('#filter-alfabeto-title').addClass('filter-title-expanded');
		jQuery('#filter-alfabeto-title').removeClass('filter-title-collapsed');
		jQuery('#filter-alfabeto-content').slideDown("slow");
	}
});	







});
	