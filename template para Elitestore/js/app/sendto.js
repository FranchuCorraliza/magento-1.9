
jQuery( document ).ready(function( $ ) {

			jQuery('ul').each(function(index, element){
				if(jQuery.trim($(element).html())=='')
				  {
					jQuery(element).remove();
				  }
			});
    jQuery('.zonaAcordeon').addClass('contraido');

    jQuery('.zonaAcordeon').on('click', function(){
        var objeto = jQuery(this);
        if(jQuery(this).hasClass('contraido')){
            jQuery(this).parent('a').parent('.selfclear').find('ul').slideDown("slow", function(){
                objeto.removeClass('contraido');
                objeto.addClass('abierto');
            });
        }
        if(jQuery(this).hasClass('abierto')){
            jQuery(this).parent('a').parent('.selfclear').find('ul').slideUp("slow", function(){
                objeto.removeClass('abierto');
                objeto.addClass('contraido');
            });
        }
    });

});

function overContinent(b, url){//efecto over sobre el mapa
        var a=document.getElementById("imgWorldmap");
        if(b==""){
            a.src=url + "images/mapa/worldmap.jpg"
        }else{
            a.src=url + "images/mapa/worldmap_"+b+".jpg"
        }
        if(window.event){
            window.event.returnValue=false;
        }
    }

function setCountry(nombre, bandera, idioma, moneda, ruta, zona, ultimaUrl){//asigna por ajax el pais y la bandera
		event.preventDefault();
        jQuery.ajax({
                type: "POST",
                url: ruta,
                data: { nombre: nombre, bandera: bandera, idioma: idioma, moneda: moneda, zona:zona, ultimaUrl: ultimaUrl}
            }).done(function(datos) {
                window.location.href = datos;
            });
    }
function displayStates(pais)//función para mostrar u ocultar los paises cuando haces click en la zona del mapa
    {
        jQuery(".selfclear").each( function(index, element){
                if (index!=0) {
                    jQuery(element).hide();
                }
            });
        jQuery("#"+pais+"_mideast").show("slow");
		jQuery("html, body").animate({ scrollTop: jQuery(document).height() }, 1000);
    }