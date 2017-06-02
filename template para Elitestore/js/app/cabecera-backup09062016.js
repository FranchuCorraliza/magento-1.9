jQuery(document).ready(function($){

//Transformador de cabecera -------------------------------

	var div = $('.header--content--right');
	var logo = $('.logo');
	var start = $(div).offset().top;
if($('body').height()>$(window).height())
{
	$.event.add(window, "scroll", function() {
		var p = $(window).scrollTop();
	
		$('.bloque-menus').css('position',((p)>start) ? 'fixed' : 'relative');
		$('.bloque-menus').css('width',((p)>start) ? '100%' : '100%');
		$('.bloque-menus').css('box-shadow',((p)>start) ? '0 2px 5px rgba(0,0,0,0.1)' : 'none');
//		$('.bloque-menus').css('margin-top',((p)>start) ? '0' : '0px');
		$('.bloque-menus').css('top',((p)>start) ? '0px' : '');
		$('.bloque-menus').css('z-index',((p)>start) ? '2' : '');
		$('.bloque-menus').css('background',((p)>start) ? 'white' : 'none');		
		//cambiar posicion del menu derecha para registro, wishlist, y busqueda -------------
		$('.header--content--right').css('display',((p)>start) ? 'none' : 'block');
		$('.header--content--right2').css('display',((p)>start) ? 'block' : 'none');
		
		if((p)>start){
			if(!$('.header--content--right2').length)
			{
				$('<div class="header--content--right2"></div').insertBefore('#contenedormenu');
			}

			if($.trim($('.header--content--right2').html())=="")
			{
				$('.header--content--right2').html($('.header--content--right').html());
				if (typeof(objetoso) !== "undefined") {
					$('.header--content--right2 #header-login-trigger-cabecera a img').attr('src', $('.header--content--right2 #header-login-trigger-cabecera a img').attr('src').substring(0,$('.header--content--right2 #header-login-trigger-cabecera a img').attr('src').length-4)+'BLK.png');
				}
				$('.header--content--right').remove();
			}
		}
		else
		{
			if(!$('.header--content--right').length)
			{
				$( ".header--content" ).append('<div class="header--content--right"></div');
			}
			if($.trim($('.header--content--right').html())=="")
			{
				$('.header--content--right').html($('.header--content--right2').html());
				$('.header--content--right #header-login-trigger-cabecera a img').attr('src', $('.header--content--right #header-login-trigger-cabecera a img').attr('src').substring(0,$('.header--content--right #header-login-trigger-cabecera a img').attr('src').length-7)+'.png');
				$('.header--content--right2').remove();
			}
		}
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
	$(document).on( "click", "#submit", function(event) {
			event.preventDefault();
		  $(".popup--search").fadeIn("Slow");
		});
	$(document).on( "click", "#submit2",function(event) {
			event.preventDefault();
		  $(".popup--search").fadeIn("Slow");
		});

//Cambia  Wishlist button-----------------------

 jQuery('.bindRemove').html('Remove to Wishlist');		

		
//Ajax Menu -------------------------------
	
	jQuery.ajax({
        type: "POST",
        url:"http://desarrollo.elitestore.es/lux/en/ajaxcontrol/index/menu",
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

