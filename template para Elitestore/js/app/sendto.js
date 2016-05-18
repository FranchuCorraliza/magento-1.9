
/*jQuery( document ).ready(function( $ ) {
   jQuery.ajax({
                type: "POST",
                url: "http://192.168.1.201:8080/elitestore192/as/en/sendto/index/getStates",
            }).done(function(datos) {
                //jQuery(".col-main").html("<a href='"+datos+"'>"+datos+"</a>");
                alert(datos);
            });
});
*/

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
                //alert(datos);
                //jQuery(".col-main").html("<a href='"+datos+"'>"+datos+"</a>");
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
    }