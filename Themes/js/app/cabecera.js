jQuery(document).ready(function($){

//Transformador de cabecera -------------------------------

	var div = $('.header--content--right');
	var logo = $('.logo');
	var start = $(div).offset().top;
if($('body').height()>$(window).height())
{
	$.event.add(window, "scroll", function() {
		var p = $(window).scrollTop();
		if($('body').hasClass("cms-home"))
		{
			if ((p)>start) {
				var wishlistpanel=$('#wishlistpanel');
				$('#wishlistpanel').remove();
				$('.bloque-menus').prepend(wishlistpanel);
				//$('#wishlistpanel').remove();
			} else{
				var wishlistpanel=$('#wishlistpanel');
				$('#wishlistpanel').remove();
				$('header').prepend(wishlistpanel);
			}
		}
		
	
	
		
		$('.bloque-menus').css('position',((p)>start) ? 'fixed' : 'relative');
		$('.bloque-menus').css('width',((p)>start) ? '100%' : '100%');
//		$('.bloque-menus').css('margin-top',((p)>start) ? '0' : '0px');
		$('.bloque-menus').css('top',((p)>start) ? '0px' : '');
		$('.bloque-menus').css('z-index',((p)>start) ? '1' : '');
		$('.bloque-menus').css('background',((p)>start) ? 'white' : 'none');		
		//cambiar posicion del menu derecha para registro, wishlist, y busqueda -------------
		$('.header--content--right').css('display',((p)>start) ? 'none' : 'block');
		$('.header--content--right2').css('display',((p)>start) ? 'block' : 'none');
		
			//fin de estilos para ponerlos en negro y demas----------------------------------
		//fin de cambis del menu derecha ----------------------------------------------------
		
		((p)>start) ? $('.nav-container .nav-container__limit .logo_mini').show() : $('.nav-container .nav-container__limit .logo_mini').hide();
		((p)>start) ? $('.toplinks_mini').show() : $('.toplinks_mini').hide();
		((p)>start) ? $('.topSearch').addClass('topsearchmini') : $('.topSearch').removeClass('topsearchmini');
		((p)>start) ? $('.toplinks_mini').removeClass('max') : $('.toplinks_mini').addClass('max');

	});
$('.mini-products-list').hide();
$('.minicart-actions #cart-link-id').hide();
$('.minicart-actions #minicart--checkout--buttom').hide();
$('.minicart-actions').prepend('<div class="cart-link" id="cartdetalles" onClick="showBag()">View Details</div>');
	
}	
	
	$('.menu-principal').hover(function() {
		$(this).parent('#nav').parent('.nav-container__limit').parent('.nav-container').addClass('shown');
	},
	function() {
		$(this).parent('#nav').parent('.nav-container__limit').parent('.nav-container').removeClass('shown');
	});
	
	
	
//Buscador -----------------------------------
	
  
	$(".popup--search #searchContainer #primary-search fieldset .close").on( "click", function() {
		  $(".popup--search").hide() ;
		});
	$("#submit").on( "click", function() {
			event.preventDefault();
		  $(".popup--search").fadeIn("Slow");
		});
	$("#submit2").on( "click", function() {
			event.preventDefault();
		  $(".popup--search").fadeIn("Slow");
		});

//Cambia  Wishlist button-----------------------

 jQuery('.bindRemove').html('Remove to Wishlist');		

		
//Ajax Menu -------------------------------
	
	jQuery.ajax({
        type: "POST",
        url:"http://192.168.1.201:8080/elitestore192/lux/en/ajaxcontrol/index/menu",
		data : {baseUrl: MAGE_STORE_URL},
    })
          .done(function(msg) {
            jQuery("#contenedormenu").html(msg);
          })
          .fail(function() {
          })
          .always(function() {
          });
   
});
function numberProductCart(number){
	jQuery('#minicart-trigger-cabecera a .count').html(number);
}
function showBag(){
  	jQuery('.mini-products-list').fadeIn();
	jQuery('.minicart-actions #cart-link-id').fadeIn();
	jQuery('.minicart-actions #minicart--checkout--buttom').fadeIn();
	jQuery('.minicart-actions #cartdetalles').hide();
}

