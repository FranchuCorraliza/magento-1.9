

jQuery(document).ready(function($){
	
// Transformacion Descripción y Videos de Catálogo
	var parrafo=[];
	jQuery(".products-grid .video").each(function(){
		var codigo;
		codigo = "<video style=\"max-width:100%\" autoplay loop><source src=\"" + "<?php echo Mage::getUrl('media') ?>" + this.getAttribute('data-video') + ".mp4\" type=\"video/mp4\"><source src=\"" + "<?php echo Mage::getUrl('media') ?>" + this.getAttribute('data-video') + ".webm\" type=\"video/webm\"></video>";
 		this.innerHTML = codigo;
	});
	decorateGeneric($$('ul.products-grid'), ['odd','even','first','last']);
	$(".text--js .paragraf").each(function( index, element ){
	var transic=[];
	transic[1]= $(element).find(".parrafo--title").html();
	transic[2]= $(element).find(".parrafo--text").html();
	parrafo.push(transic);
	});
if(parrafo.length>0){
	$('.products-grid li:eq(0)').before("<li class=\"text-center item\"><div class=\"textoli\"><h5 class=\"product-manufacturer\">" + parrafo[0][1] + "</h5><br/><div class=\"line\"></div><br/><div class=\"parrafo\"><p>" + 		
	parrafo[0][2] + "</p></div></div></li>");
}if(parrafo.length>1 && $(".products-grid li").size()>11){
	$('.products-grid li:eq(11)').before("<li class=\"text-center item\"><div class=\"textoli\"><h5 class=\"product-manufacturer\">" + parrafo[1][1] + "</h5><br/><div class=\"line\"></div><br/><div class=\"parrafo\"><p>" + 		parrafo[1][2] + "</p></div></div></li>");
}
else if(parrafo.length>1 && $(".products-grid li").size()<11){
	$('.products-grid').append("<li class=\"text-center item\"><div class=\"textoli\"><h5 class=\"product-manufacturer\">" + parrafo[1][1] + "</h5><br/><div class=\"line\"></div><br/><div class=\"parrafo\"><p>" + parrafo[1][2] + "</p></div></div></li>");
}
$(".text--js").hide();



// Transformación Layer Navigation Catálogo

var div = $('#layered-navigation-container');
var start = $(div).offset().top;
	$.event.add(window, "scroll", function() {
		var p = $(window).scrollTop();
		$('#layered-navigation-container').css('position',((p)>start) ? 'fixed' : 'relative');
		$('#layered-navigation-container').css('bottom',((p)>start) ? '15px' : '0');
		$('#layered-navigation-container').css('min-height',((p)>start) ? '90%' : '0');
		
	});
	$('.menu-principal').hover(function() {
		$(this).parent('#nav').parent('.nav-container__limit').parent('.nav-container').addClass('shown');
	},
	function() {
		$(this).parent('#nav').parent('.nav-container__limit').parent('.nav-container').removeClass('shown');
	});
	
	







})

// Control desplegables del toolbar
function showSortBy(){
   	jQuery("#toolbar-dropdown1").show();
  }
 
function hideSortBy(){  
    jQuery("#toolbar-dropdown1").hide();
}


//Resaltar Elementos Wishlist

function resaltarRecuadro(id){
		
		if (jQuery("#product-"+id).class('padre-deseado')){
			jQuery("#product-"+id).removeClass('padre-deseado');
		}else{
			jQuery("#product-"+id).addClass("padre-deseado");
		}
}
