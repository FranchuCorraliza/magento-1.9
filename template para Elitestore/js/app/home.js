var hijo=0;

jQuery(document).ready(function($){
//Transformador de cabecera -------------------------------

	var div = $('.header--content--right');
	var intervaleado = setInterval(increment,5000);
	if ($(".texto--banner--restPages").length > 0) {
		$(".texto--banner--restPages").addClass("texto--banner");
		$(".texto--banner--restPages").removeClass(".texto--banner--restPages");
	}
$.event.add(window, "scroll", function() {
		var p = $(window).scrollTop();
		//$('.nav-container').css('background',((p)>start) ? 'rgb(255,255,255)' : 'rgba(255,255,255,0)');
		//cambiar los links del menu para que cuando baje se pongan negros o blancos
		//$('.menu-principal .blanco').css('color',((p)>start) ? 'rgb(0,0,0)' : 'rgb(255,255,255)');
		//tambien cambiamos los links que no tienen sabana
		//$('.menu__principal__sinsabana .blanco').css('color',((p)>start) ? 'rgb(0,0,0)' : 'rgb(255,255,255)');
		
	});
});

function increment(){
    totlLi = jQuery(".texto--banner ul li").length
    cuentaLi = jQuery(".texto--banner ul").find(".active").attr('id').substr(0);
    liDeseado = parseInt(cuentaLi)+1;

    if(totlLi>1)
    {
        if(cuentaLi<totlLi-1){
            jQuery(".texto--banner ul").find("#"+cuentaLi).fadeOut('slow', function(){
                jQuery(".texto--banner ul").find("#"+liDeseado).fadeIn('slow');
            });
            jQuery(".texto--banner ul").find("#"+liDeseado).addClass('active');
            jQuery(".texto--banner ul").find("#"+cuentaLi).removeClass('active');
        }
        if(cuentaLi>=totlLi-1){
            jQuery(".texto--banner ul").find("#"+cuentaLi).fadeOut('slow', function(){
                jQuery(".texto--banner ul").find("#0").fadeIn('slow');
            });
            jQuery(".texto--banner ul").find("#0").addClass('active');
            jQuery(".texto--banner ul").find("#"+cuentaLi).removeClass('active');
        }
    }   
}