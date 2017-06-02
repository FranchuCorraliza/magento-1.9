var hijo=0;
jQuery(document).ready(function($){
    //coigo para hacer el slide del banner de ofertas 
    var intervaleado = setInterval(function(){ 
        if(jQuery(".texto--banner--restPages").length)
        {
            totlLi = jQuery(".texto--banner--restPages ul li").length
            cuentaLi = jQuery(".texto--banner--restPages ul").find(".active").attr('id').substr(0);
            liDeseado = parseInt(cuentaLi)+1;

            if(totlLi>1)
            {
                if(cuentaLi<totlLi-1){
                    jQuery(".texto--banner--restPages ul").find("#"+cuentaLi).fadeOut('slow', function(){
                        jQuery(".texto--banner--restPages ul").find("#"+liDeseado).fadeIn('slow');
                    });
                    jQuery(".texto--banner--restPages ul").find("#"+liDeseado).addClass('active');
                    jQuery(".texto--banner--restPages ul").find("#"+cuentaLi).removeClass('active');
                }
                if(cuentaLi>=totlLi-1){
                    jQuery(".texto--banner--restPages ul").find("#"+cuentaLi).fadeOut('slow', function(){
                        jQuery(".texto--banner--restPages ul").find("#0").fadeIn('slow');
                    });
                    jQuery(".texto--banner--restPages ul").find("#0").addClass('active');
                    jQuery(".texto--banner--restPages ul").find("#"+cuentaLi).removeClass('active');
                }
            }  
        } 
    },5000); 

//Transformador de cabecera --------------------------------------------------------------------------------------------

	if($('body').height()>$(window).height()-30){
		if (!IS_MOBILE){
			ajustarCabeceraEscritorio($);
		}else{
			ajustarCabeceraMovil($);
		}
		
		
		
		$('.mini-products-list').hide();
		$('.minicart-actions #cart-link-id').hide();
		$('.minicart-actions #minicart--checkout--buttom').hide();
		

		
		
}	
// Fin transformación cabecera -----------------------------------------------------------------------------------------------------------------------
	
//Buscador -----------------------------------
	
  
	$(".popup--search #searchContainer #primary-search fieldset .close").on( "click", function() {
		  $(".popup--search").hide() ;
		});
	$(document).on( "click", "#submit", function(event) {
			event.preventDefault();
		    $(".popup--search").fadeIn("Slow");
            $("#q").focus();
          
		});
	$(document).on( "click", "#submit2",function(event) {
            event.preventDefault();
            $(".popup--search").fadeIn("Slow");
            $("#q").focus();
		});


		
//Ajax Menu -------------------------------
	
	jQuery.ajax({
        type: "POST",
        url:MAGE_STORE_URL+"/ajaxcontrol/index/menu",
		data : {baseUrl: MAGE_STORE_URL},
    })
          .done(function(msg) {
            jQuery("#contenedormenu").html(msg);
          })
          .fail(function() {
          })
          .always(function() {
          });

//Anulamos los links del menú ppal
		jQuery('menu-principal-a').on("click",function(){
			alert(1);
		});
		
		jQuery(document).on("click","html",function(event){
			jQuery(".sheet").fadeOut("slow");
		});
		jQuery(document).on("click",".menu-principal-a",function(event){
			var buscado= jQuery(this).parent().find(".sheet").get(0);
			jQuery(".sheet").each(function(element){
				var muestra = jQuery(this).get(0);
				if (muestra!=buscado){
					jQuery(this).fadeOut("slow");
				}
			});
			jQuery(this).parent().find(".sheet").fadeIn("slow");
			event.stopPropagation();
			return false;
		});
	

// Buscamos Bloque Paises para moviles
		/*if (jQuery('#paises-mobile').length!=0){
			jQuery('#paises-mobile').html(jQuery('.header--content--left').html());
		}*/		  
});


function numberProductCart(number){
	jQuery('#minicart-trigger-cabecera a .count').html(number);
	jQuery('.mobile__header__right--cart a .count').html(number);
}
function showBag(){
  	jQuery('.mini-products-list').fadeIn();
	jQuery('.minicart-actions #cart-link-id').fadeIn();
	jQuery('.minicart-actions #minicart--checkout--buttom').fadeIn();
	jQuery('.minicart-actions #cartdetalles').hide();
}

function sliderOfertas(){
    
	/*if(hijo==0)
	{
		jQuery(".texto--banner--restPages ul").find("#0").fadeIn("slow");
		hijo=hijo+1;
	}
	else if(hijo>2)
	{
		jQuery(".texto--banner--restPages ul").find("#2").fadeOut("slow", function(){
			jQuery(".texto--banner--restPages ul").find("#0").fadeIn("slow");
			hijo=1;
		});
		
	}
	else
	{
		hijo=hijo-1;
		jQuery(".texto--banner--restPages ul").find("#" + hijo).fadeOut("slow", function(){
			hijo=hijo+1;
			jQuery(".texto--banner--restPages ul").find("#" + hijo).fadeIn("slow");
			hijo=hijo+1;
		});
		
	}	*/
         
}

function ajustarCabeceraEscritorio($){
	var div = $('.col-main');
	var start = $(div).offset().top;
	$(window).on("scroll", function() {
		var p = $(window).scrollTop();
		//alert(p);
		//alert(start);
		if((p)>start){
			if(!$('.bloque--ficticio').length)
			{
				$('header').prepend('<div class="bloque--ficticio" style="height: 30px;"></div>');
			}
		}
		else
		{
			$('.bloque--ficticio').remove();
		}
		if((p)>start){
				$('#nav').addClass('menu--fixed');
		}
		else
		{
			$('#nav').removeClass('menu--fixed');
		}
		$('.bloque-menus').css('position',((p)>start) ? 'fixed' : 'relative');
		//añadimos un div ficticio para que no suba la web al hacer el menu fixed
		

		$('.bloque-menus').css('width',((p)>start) ? '100%' : '100%');
		
		//$('.bloque-menus').css('box-shadow',((p)>start) ? '0 2px 5px rgba(0,0,0,0.1)' : 'none');
		$('.bloque-menus').css('top',((p)>start) ? '0px' : '');
		$('.bloque-menus').css('z-index',((p)>start) ? '2' : '');
		$('.bloque-menus').css('background',((p)>start) ? 'black' : 'none');
		
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
					//$('.header--content--right2 #header-login-trigger-cabecera a img').attr('src', $('.header--content--right2 #header-login-trigger-cabecera a img').attr('src').substring(0,$('.header--content--right2 #header-login-trigger-cabecera a img').attr('src').length-4)+'BLK.png');
					
				}
				
				$('.header--content--right').remove();

				$('.header--content--right2 .wishlist #wishlist-label .icon img').attr('src', MAGE_STORE_SKIN_URL+'/images/estrellagalicia_BLK.png');
				$('.header--content--right2 #minicart-trigger-cabecera a .icon img').attr('src', MAGE_STORE_SKIN_URL+'/images/bolsa_BLK.png');
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
				if(!$('.header--content--right').length)
					{
						$('.header--content--right #header-login-trigger-cabecera a img').attr('src', $('.header--content--right #header-login-trigger-cabecera a img').attr('src').substring(0,$('.header--content--right #header-login-trigger-cabecera a img').attr('src').length-7)+'.png');
					}
				$('.header--content--right .wishlist #wishlist-label .icon img').attr('src', MAGE_STORE_SKIN_URL+'/images/estrella_WHT.png');
				$('.header--content--right #minicart-trigger-cabecera a .icon img').attr('src', MAGE_STORE_SKIN_URL+'/images/bolsa_WHT.png');
				$('.header--content--right2').remove();
			}
		}
		//fin de estilos para ponerlos en negro y demas----------------------------------
		//fin de cambis del menu derecha ----------------------------------------------------
		
		((p)>start) ? $('.nav-container__limit .logo_mini').show() : $('.nav-container__limit .logo_mini').hide();
		
		// Desplazamiento de la flor del loading en el listado de productos
		
		if($('#loading-mask-content .loader').length){
			var x=(jQuery(window).height()/2)-jQuery("#loading-mask-content").offset().top;
			jQuery("#loading-mask-content .loader").css('top', x+'px');	
		}
		
		
	});
	
	// Disparador de Minicarrito de la cabecera ---------------------------------------

		jQuery('#minicart-trigger-cabecera').hover(function(){
				jQuery('#header-cart').show();	
			},function(){
				jQuery('#header-cart').hide();
			});
		
		jQuery(document).on("mouseenter", "#minicart-trigger-cabecera", function() {
				jQuery('#header-cart').show();
			});

		jQuery(document).on("mouseleave", "#minicart-trigger-cabecera", function() {
				jQuery('#header-cart').hide();
			});


	// Disparador de Miniwishlist de la cabecera ---------------------------------------

		jQuery('#wishlist-trigger').hover(function(){
				jQuery('#wishlistpanel').show();
			},function(){
				jQuery('#wishlistpanel').hide();
			});

		jQuery(document).on("mouseenter", "#wishlist-trigger", function() {
			jQuery('#wishlistpanel').show();
			});
		jQuery(document).on("mouseleave", "#wishlist-trigger", function() {
			jQuery('#wishlistpanel').hide();
			});
	
	// Lanzar Popup Promoción -----------------------------------------------------------
	
		jQuery('.promo-popup').on("click",function(event){
			event.preventDefault();
			event.stopPropagation();
			jQuery(".modalwindow .border .content").html("<div class='close'>&#62134;</div><div class='loading'><span id='floatingCirclesG-content' class='frame sprite'></span><br/>Loading...</div>");
			jQuery('.modalwindow').lightbox_me({
					centered: true,
					zIndex:1,
					onLoad: function() {
						jQuery('.modalwindow').show();				}
				});
			
			var url=jQuery(this).attr('href');
			jQuery.ajax({
				type: "POST",
				url:url
				})
			.done(function(msg) {
            jQuery(".modalwindow .border .content").html(msg);
			})
			.fail(function() {
          })
			.always(function() {
          });
	});
	
}

function ajustarCabeceraMovil($){
	var div = $('.col-main');
	var start = $(div).offset().top;
	$(".scroller").on("scroll", function() {  //Solo para moviles
		var p = $(".scroller").scrollTop();
		var start = $(div).offset().top;
		$('header').css('position',((p)>start) ? 'fixed' : 'relative');		
	});
}