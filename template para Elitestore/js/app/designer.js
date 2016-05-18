// Transformación Layer Navigation Catálogo
jQuery(document).ready(function() {
var div = jQuery('#layered-navigation-container');
var start = jQuery(div).offset().top;
	jQuery.event.add(window, "scroll", function() {
		var p = jQuery(window).scrollTop();
		jQuery('#layered-navigation-container').css('position',((p)>start) ? 'fixed' : 'relative');
		jQuery('#layered-navigation-container').css('bottom',((p)>start) ? '15px' : '0');
		jQuery('#layered-navigation-container').css('min-height',((p)>start) ? '90%' : '0');
		
	});
	jQuery('.menu-principal').hover(function() {
		jQuery(this).parent('#nav').parent('.nav-container__limit').parent('.nav-container').addClass('shown');
	},
	function() {
		jQuery(this).parent('#nav').parent('.nav-container__limit').parent('.nav-container').removeClass('shown');
	});
	
jQuery('#filter-gender-title').click(function(){
	jQuery('#filter-gender-content').toggle();
});	

jQuery('#filter-category-title').click(function(){
	jQuery('#filter-category-content').toggle();
});	
	
jQuery('#filter-alfabeto-title').click(function(){
	jQuery('#filter-alfabeto-content').toggle();
});	







});

function filtraLetra(letra){
	jQuery('.listado-diseñadores').find('li').each(function(index,listadoLetra){
		if (jQuery(listadoLetra).attr('letra')!=letra){
			listadoLetra.toggle();
		}
	});
	jQuery('.filter-alfabeto').find('li').each(function(index,boton){
		if (jQuery(boton).html()!=letra){
			if(jQuery(boton).hasClass('deshabilitada')){
				jQuery(boton).removeClass('deshabilitada');
			}else{
			jQuery(boton).addClass('deshabilitada');	
			}
		}
	});
	
}

function filterCategory(category){
	
	jQuery('.listado-diseñadores').find('li.letra').each(function(index,letra){
		jQuery(letra).hide();
	});
	
	filtros=new Array();
	boton=jQuery('#filter-category-'+category.toLowerCase());
	if(boton.hasClass('selected')){
		boton.removeClass('selected');
	}else{
		boton.addClass('selected');
	}
	boton.parent().find('li.selected').each(function(index,botones){
			filtros.push(jQuery(botones).attr('category'));	
	});
	
	jQuery('.listado-diseñadores').find('li').each(function(index,listado){
		stringCategorias=jQuery(listado).attr('category');
		if (typeof stringCategorias !== typeof undefined && stringCategorias !== false) {
			stringCategorias=stringCategorias.toLowerCase();
			categoriasMarca=stringCategorias.split(',');
			if (filtros.length>0){
				listado.hide();
				for (var i=0; i < filtros.length; i++){
					if (categoriasMarca.indexOf(filtros[i])>=0){
						jQuery(listado).parent().parent().show();
						listado.show();
					}	
				}
			}else{
				jQuery(listado).parent().parent().show();
				listado.show();
				
			}
		}		
	});
}
	


function filterGender(gender){
	filtros=new Array();
	boton=jQuery('#filter-gender-'+gender.toLowerCase());
	if(boton.hasClass('selected')){
		boton.removeClass('selected');
	}else{
		boton.addClass('selected');
	}
	boton.parent().find('li.selected').each(function(index,botones){
			filtros.push(jQuery(botones).attr('gender'));	
	});
	
	jQuery('.listado-diseñadores').find('li').each(function(index,listado){
		stringGeneros=jQuery(listado).attr('gender');
		if (typeof stringGeneros !== typeof undefined && stringCategorias !== false) {
			stringGeneros=stringGeneros.toLowerCase();
			generosMarca=stringGeneros.split(',');
			if (filtros.length>0){
				listado.hide();
				for (var i=0; i < filtros.length; i++){
					if (generosMarca.indexOf(filtros[i])>=0){
						listado.show();
					}	
				}
			}else{
				listado.show();
			}
		}		
	});
}		