jQuery(document).ready(function(){
    //actualizar la cantidad en el carrito de la compra

	
	jQuery('.choose-shipping').click(function(){
		jQuery('.shipping__choose--popup').show();
	});
	jQuery('.shipping__choose--popup--content .close').click(function(){
		jQuery('.shipping__choose--popup').hide();
	});
	
	jQuery('.gift__message--title').click(function(){
		jQuery('.gift__message--corpus').show();
	});
	jQuery('.gift__message--corpus .close').click(function(){
		jQuery('.gift__message--corpus').hide();
	});
/*	
	//Buscamos linea de shipping cost
	if (!jQuery('#shopping-cart-totals-table').find('.shipping-total').length ){
				jQuery('.shipping-choose').show();
			}
			

	//al cerrar el popup del shipping and tax
    jQuery(".close").on("click", function(){
        jQuery(".shipping__choose--popup").hide();
    });
    
    setTimeout(explode, 2000);
*/
})

function activarSugerencias(este){
		var selector = ".sugerencias-nav li";
		jQuery(selector).removeClass('active');
		jQuery(este).addClass('active');
		jQuery("#contenido-sugerencias-relacionadas").hide();
			jQuery("#contenido-sugerencias-recientes").hide();
			if (jQuery(este).attr('activar')=="contenido-sugerencias-relacionadas"){
				jQuery("#contenido-sugerencias-relacionadas").show();
			}else if (jQuery(este).attr('activar')=="contenido-sugerencias-recientes"){
				jQuery("#contenido-sugerencias-recientes").show(); 
			}
		
		jQuery( ".listado-sugerencias li" ).last().addClass( "last" );
}

